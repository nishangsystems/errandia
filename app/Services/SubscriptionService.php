<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Repositories\PaymentRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionService{
    private SubscriptionRepository $subscriptionRepository;
    private PaymentRepository $paymentRepository;


    public function __construct(SubscriptionRepository $subscriptionRepository, PaymentRepository $paymentRepository){
        $this->subscriptionRepository = $subscriptionRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function find_all_by_user($user_id)
    {
        return $this->subscriptionRepository->find_all_by_user($user_id);
    }

    public function subscribe($user_id,  $data)
    {
        if($this->subscriptionRepository->get_current($user_id)) {
            throw new \Exception('You are already under a subscription plan.');
        }


        $plan = Plan::find($data['plan_id']);
        $payment = DB::transaction(function () use ($data, $user_id, $plan) {

           $values =  [
               'plan_id' => $data['plan_id'],
               'user_id' => $user_id,
               'amount' => $plan->unit_price,
           ];
           $subscription = $this->subscriptionRepository->save($values);
           $payment_values = [
               'payment_ref' => str_replace("-", "", (string) Str::orderedUuid()),
               'subscription_id' => $subscription->id,
               'user_id' => $user_id,
               'phone_number' => $data['phone_number'],
               'amount' => $plan->unit_price
           ];
           return $this->paymentRepository->save($payment_values);
       });

       if (!$payment) {
           throw new \Exception("operation failed. try again");
       }

       $paymentService = new PaymentService();
       $response_data = $paymentService->makePaymentRequest(
           $payment->phone_number,
           $payment->amount,
           $payment->payment_ref,
           "Subscription for a ". $plan->name
       );
       if($response_data == null) {
           throw new \Exception("Our payment service is unreachable. try again later");
       }

       $status = $response_data['status'] == 'FAILED' ? 'FAILED' :  ($response_data['status'] == 'SUCCESSFUL' ? 'SUCCESS' : 'PENDING');
       Payment::where('id', $payment->id)
            ->update(['status' => $status, 'request_id' => $response_data['requestId']]);

       $this->subscriptionRepository->chnageStatus(
           $payment->subscription_id,
           $status, $status == 'SUCCESS' ?  self::get_expired_date($plan['name']) : null
       );

       if ($status == 'FAILED') {
           throw new \Exception("Our payment service is unreachable. try again later");
       }

       logger()->info("payment request api successfully done");
    }

    public function get_current($user_id)
    {
        return $this->subscriptionRepository->get_current($user_id);
    }

    public function mark_as_expired($id)
    {
        $this->subscriptionRepository->set_as_expired($id);
    }

    public static function get_expired_date($plan_name)
    {
        switch ($plan_name) {
           case  "YEARLY":
              return Carbon::now()->addYear();
            case  "MONTHLY":
                return Carbon::now()->addMonth();
            default:
                return Carbon::now()->addDay();
        }
    }

    public function update_payment(array $all)
    {
    }

    public function check_status($id)
    {
        $subscription = $this->subscriptionRepository->find($id);
        if($subscription == null) {
            throw new \Exception('subscription not found');
        }

        $payment_service = new PaymentService();
        $response_data =  $payment_service->getPaymentDetails($subscription->payment['request_id']);

        if ($response_data == null) {
            throw new \Exception('failed to check subscription status');
        }

        $payment = $subscription->payment;
        $plan = $subscription->plan;
        if ($response_data['status'] != 'PENDING') {
            $status = $response_data['status'] == 'FAILED' ? 'FAILED' :  ($response_data['status'] == 'SUCCESSFUL' ? 'SUCCESS' : 'PENDING');
            Payment::where('id', $payment['id'])->update(['status' => $status]);
            $this->subscriptionRepository->chnageStatus(
                $payment->subscription_id,
                $status, ($status == 'SUCCESS') ?  self::get_expired_date($plan['name']) : null
            );
        }

        return $this->subscriptionRepository->find($id);
    }

}