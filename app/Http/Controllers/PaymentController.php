<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ProductRequest;
use App\User;  
use Modules\Admin\Models\ShippingBillingAddress;
use App\PostTask;
use Input;
use Validator;
use Auth; 
use Form;
use Hash; 
use URL; 
use Session;
use Route;
use Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Dispatcher; 
use App\Helpers\Helper;
use Response; 
use Modules\Admin\Models\Settings;
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard; 
use Omnipay\PayPal; 
use App\Transaction;
/**
 * Class AdminController
 */
class PaymentController extends Controller {
    /**
     * @var  Repository
     */

    /**
     * Displays all admin.
     *
     * @return \Illuminate\View\View
    
     */

    private $user_id;

     
    // make payment 
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|max:50',
            'lastName' => 'required|max:50',
            'cardNumber' => 'required|max:16',
            'cvv' => 'required|numeric|min:3',
            'month' => 'required',
            'year' => 'required|digits:4|integer|min:'.(date('Y')),
            'taskId' => 'required',
            'userId' => 'required',
           // 'amount' => 'required'
        ]); 
       if (isset($validator) && $validator->fails()) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'code'   => 500,
                'message' => $error_msg,
                'data'  =>  $request->all()
                )
            );
        } 
 
        try{

            $task = PostTask::find($request->get('taskId'));
            
            $gateway = Omnipay::create('PayPal_Pro');
            
            $gateway->setUsername( 'kundan.r-facilitator-3_api1.cisinlabs.com' );
            $gateway->setPassword( '32UN5286G4FDKWK7' );
            $gateway->setSignature( 'AgsyRufAX1NOEGmzAg0vXIX4pkjQAEaRyKcNiHzfR5Ka0I-74umoKXhH' ); 
            $gateway->setTestMode( true );
        
      
            $card = new CreditCard(array(
                'firstName'             => $request->get('firstName'),
                'lastName'              => $request->get('lastName'),
                'number'                => $request->get('cardNumber'),
                'expiryMonth'           => $request->get('month'),
                'expiryYear'            => $request->get('year'),
                'cvv'                   => $request->get('cvv')
            )); 
            
            $transaction_paypal = $gateway->purchase(array(
                 'currency'         => 'AUD',
                 'description'      => isset($task->event_title)?$task->event_title:'paying for task',
                 'card'             =>  $card,
                 'name'             => isset($task->event_title)?$task->event_title:'task', 
                 'amount'           =>  !empty($request->get('amount'))?$request->get('amount'):'1.00'
            ));
             
            $response   = $transaction_paypal->send();
            $data       = $response->getData(); 
            
            // L_LONGMESSAGE0
            if(isset($data['ACK']) && $data['ACK']=="Failure")
            {
            	$transaction = new Transaction;
	            $transaction->firstName =  $request->get('firstName');
	            $transaction->lastName 	= $request->get('lastName');
	            $transaction->userId 	= $request->get('userId');
	            $transaction->taskId 	= $request->get('taskId');
	            $transaction->amount 	= $request->get('amount');
	            $transaction->cardDetails = json_encode($request->all());
	            $transaction->transactionDetails =  json_encode($data);
	            $transaction->transactionId =  time();
	            $transaction->save();
	            return ['status'=>0,'code'=>500,'message'=>$data['ACK'],'data'=>$data];
       

            }
            if(isset($data['ACK']) && $data['ACK']=="Success")
            {
            	$transaction = new Transaction;
	            $transaction->firstName =  $request->get('firstName');
	            $transaction->lastName 	= $request->get('lastName');
	            $transaction->userId 	= $request->get('userId');
	            $transaction->taskId 	= $request->get('taskId');
	            $transaction->amount 	= $request->get('amount');
	            $transaction->cardDetails = json_encode($request->all());
	            $transaction->transactionDetails =  json_encode($data);
	            $transaction->transactionId =  $data['TRANSACTIONID'];
	            $transaction->save();
	            return ['status'=>1,'code'=>200,'message'=>$data['ACK'],'data'=>$data];
       
            		
            }
           
 
 			
        }catch (\Exception $e) {  
            
            return ['status'=>0,'code'=>500,'message'=>$e->getMessage(),'data'=>[]];
        } 
    } 
}   

