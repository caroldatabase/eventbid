<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ProductRequest;
use App\User;  
use Modules\Admin\Models\ShippingBillingAddress;
use Modules\Admin\Models\Transaction;
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
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'credit_card_number' => 'required|max:16',
            'cvv' => 'required|numeric|min:3',
            'month' => 'required',
            'year' => 'required|digits:4|integer|min:'.(date('Y'))
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

            $gateway = Omnipay::create('PayPal_Pro');
            
            $gateway->setUsername( 'kundan.r-facilitator-3_api1.cisinlabs.com' );
            $gateway->setPassword( '32UN5286G4FDKWK7' );
            $gateway->setSignature( 'AgsyRufAX1NOEGmzAg0vXIX4pkjQAEaRyKcNiHzfR5Ka0I-74umoKXhH' ); 
            $gateway->setTestMode( true );
        
        
            $card = new CreditCard(array(
                'firstName'             => $request->get('first_name'),
                'lastName'              => $request->get('lastName'),
                'number'                => $request->get('credit_card_number'),
                'expiryMonth'           => $request->get('month'),
                'expiryYear'            => $request->get('year'),
                'cvv'                   => $request->get('cvv')
            )); 
            
            $transaction_paypal = $gateway->purchase(array(
                 'currency'         => 'USD',
                 'description'      => 'Toys Box',
                 'card'             =>  $card,
                 'name'             => 'newborn', 
                 'amount'           =>  "100.00" //$sub_total 
            ));
            
            if(Auth::check()){
                $user       = User::where('id',Auth::user()->id)->first();
                $user_id    = $user->id;
            }else{
                $user = User::where('email',$request->get('email'))->first();
                 
            }
            $response   = $transaction_paypal->send();
            $data       = $response->getData(); 

 

//            $shippingBillingAddress = new ShippingBillingAddress;
//
//            $shippingBillingAddress->name = $request->get('first_name').' '.$request->get('last_name');
//            $shippingBillingAddress->name       =   $request->get('first_name');
//            $shippingBillingAddress->user_id    =   $user_id;
//            $shippingBillingAddress->phone      =   $request->get('phone');
//            $shippingBillingAddress->email      =   $request->get('email');
//            $shippingBillingAddress->address1   =   $request->get('address');
//            $shippingBillingAddress->address2   =   $request->get('apt_unit');
//            $shippingBillingAddress->city       =   $request->get('city');
//            $shippingBillingAddress->status     =   $request->get('status');
//            $shippingBillingAddress->state      =   $request->get('state');
//            $shippingBillingAddress->zip_code   =   $request->get('postal_code');
//            $shippingBillingAddress->country    =   $request->get('country');
//            $shippingBillingAddress->address_type=  1;
//            $shippingBillingAddress->payment_mode = "PayPal";
//            $shippingBillingAddress->others_detail = json_encode($request->all());
//            $shippingBillingAddress->save();


            if(isset($data['ACK']) && $data['ACK']=='Success'){

//                $trns = Transaction::find($transaction->id);
//                $trns->transaction_detail =  json_encode($data);
//                $trns->paypal_transaction_id =  $data['TRANSACTIONID'];
//                $trns->status = $data['ACK'];
//                $trns->save();

            }else{ 
//                $trns = Transaction::find($transaction->id);
//                $trns->transaction_detail =  json_encode($data);
//                $trns->paypal_transaction_id =  isset($data['TRANSACTIONID'])?$data['TRANSACTIONID']:'';
//                $trns->status = isset($data['ACK'])?$data['ACK']:'';
//                $trns->save(); 
            } 
            return ['status'=>1,'code'=>200,'message'=>$data['ACK'],'data'=>$data];
        }catch (\Exception $e) {  
            
            return ['status'=>0,'code'=>500,'message'=>$e->getMessage(),'data'=>[]];
        } 
    } 
}   

