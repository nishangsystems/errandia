<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Transaction;
use Bmatovu\MtnMomo\Exceptions\CollectionRequestException;
use Bmatovu\MtnMomo\Exceptions\MtnMomoRequestException;
use Bmatovu\MtnMomo\Products\Collection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class TransactionController extends Controller
{
    public function paymentForm()
    {
        return view('transaction.transaction_form'); // TODO: Change the autogenerated stub
    }

    public function makePayments($resource)
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
        
        //todo: remove try catch before pushing to life
        try {

            $collection = new Collection();
            
            $payer_id = strlen($resource['account_number']) < 12 ? '+237'.$resource['account_number'] : $resource['account_number'];
            $momoTransactionId = $collection->requestToPay(Uuid::uuid4()->toString(), $payer_id, $resource['amount']);
            
           if($momoTransactionId == false || $momoTransactionId == null){
               return response('Operation failed. Unable to trigger payment. Make sure you are connected and try again', 500);
            }
            else{
                $data['transaction_Id'] = $momoTransactionId;
                return response()->json($data);
            }
        } catch (MtnMomoRequestException $e) {
            // do {
            //     printf("\n\r%s:%d %s (%d) [%s]\n\r",
            //         $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), get_class($e));
            // } while ($e = $e->getPrevious());
            return response($e->getCode().' : '.$e->getMessage(), 500);
        } catch (CollectionRequestException $e) {
            // do {
            //     printf("\n\r%s:%d %s (%d) [%s]\n\r",
            //         $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode(), get_class($e));
            // } while ($e = $e->getPrevious());
            return response($e->getCode().' : '.$e->getMessage(), 500);
        }

    }

    public function getTransactionStatus(Request $request)
    {
        try {
            
            $transaction_id = $request->transaction_id;
            $collection = new Collection();
            $transaction_status = $collection->getTransactionStatus($transaction_id);
            // dd($transaction_status);
            return response()->json($transaction_status);
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

    }

}
