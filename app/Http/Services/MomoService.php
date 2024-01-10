<?php

namespace App\Http\Services;


use Bmatovu\MtnMomo\Products\Collection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class MomoService
{

    public function __construct()
    {
        # code...
    }

    public function paymentForm()
    {
        return view('transaction.transaction_form'); // TODO: Change the autogenerated stub
    }

    public static function makePayments($resource)
    {

        /**
         * Algorithm
         * 1- Validate input request
         * 2- initiate transaction by calling requestToPay on collection instance
         * 3- Update transactions table with request data and set the status to pending
         * 3- Send response to the user,the response can either be an error or transaction id
         * 4- use the transaction id to check transaction status
         */

        
        $validator = Validator::make($resource, [
            'account_number'=>'required|numeric|min:9',
            'amount'=>'required|numeric'
        ]);
        if($validator->fails())
            throw new Exception($validator->errors()->first());

            

        $collection = new Collection();
        
        $payer_id = strlen($resource['account_number']) < 12 ? '+237'.$resource['account_number'] : $resource['account_number'];
        $momoTransactionId = $collection->requestToPay(Uuid::uuid4()->toString(), $payer_id, $resource['amount']);
        
        if($momoTransactionId == false || $momoTransactionId == null){
            throw new Exception('Operation failed. Unable to trigger payment. Make sure you are connected and try again');
        }
        else{
            $data['transaction_Id'] = $momoTransactionId;
            return $momoTransactionId;
        }

    }

    public static function getTransactionStatus($transaction_id)
    {
        try {
            $collection = new Collection();
            $transaction_status = $collection->getTransactionStatus($transaction_id);
            // dd($transaction_status);

            return $transaction_status;
        } catch (\Throwable $th) {
            throw $th;
        }

    }

}