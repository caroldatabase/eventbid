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
use App\Models\Cards;

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
    
    public $paypal_client_id = "AXAf3XiKlY89LsbBhZVVmf4fSyX6poAzg_6DCWPb4SyYZsCk27iA2-I4dudccLIfmpZXSW4BgHR-TVTz"; //client ID
    public $paypal_secret    = "ECat8tXLGpBtEaZUCbqLeEy89eXKHHH4y8vJnHcFDe_ZoK3g9-d6_NREaW-90tCy-P7Xs0SCUW8jLzYU"; //Secret ID
    
     public function __construct(Request $request) {

        if ($request->header('Content-Type') != "application/json")  {
            $request->headers->set('Content-Type', 'application/json');
        }
    }
    
    public function getToken(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.sandbox.paypal.com/v1/oauth2/token",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 60,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "grant_type=client_credentials",
          CURLOPT_HTTPHEADER => array(
            "authorization: Basic QVhBZjNYaUtsWTg5THNiQmhaVlZtZjRmU3lYNnBvQXpnXzZEQ1dQYjRTeVlac0NrMjdpQTItSTRkdWRjY0xJZm1wWlhTVzRCZ0hSLVRWVHo6RUNhdDh0WExHcEJ0RWFaVUNicUxlRXk4OWVYS0hISDR5OHZKbkhjRkRlX1pvSzNnOS1kNl9OUkVhVy05MHRDeS1QN1hzMFNDVVc4akx6WVU=",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return false;
         
        } else {
          $token =  json_decode($response);
          return  $token->access_token;
        }
    }
    
    public function saveCardDetail($token=null,$data)
    {
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.sandbox.paypal.com/v1/vault/credit-cards",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$token,
            "cache-control: no-cache",
            "content-type: application/json",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
           return false;
        } else {
          return $response;
        }
    }

    public function saveCard(Request $request){
       
        $token =  $this->getToken();
        if(!$token){
           return ['error'=>'invalid token'];
        }else{
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'number' => 'required|max:16',
            'cvv2' => 'required|numeric|min:3',
            'expire_month' => 'required',
            'expire_year' => 'required|digits:4|integer|min:'.(date('Y')),
            'type' => 'required',
            'userId' => 'required'
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
        
        $this->saveCardDetail($token,$request->all());
          
       }
    }

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
    
    
    // =========================
    
    
	/*
	* Name: getAccessToken
	* Created By: Ocean
	* Purpose: To get access token from oauth2 
	*/

    private function getAccessToken() 
	{
		$accessToken = "";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_USERPWD, $this->paypal_client_id.":".$this->paypal_secret);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

		$result = curl_exec($ch);

		if(empty($result))die("Error: No response.");
		else
		{
		    $json = json_decode($result);
		    $accessToken = $json->access_token;
		}

		curl_close($ch);

		return $accessToken;

	}

	/*
	* Name: addCard
	* Created By : Ocean
	* Purpose: To add card on paypal voult and database.
	*/
    public function addCard(Request $request) {
        
        $response_array['code']    = 406;
        $response_array['message'] = "Due to some reason card is not added!";
        $response_array['success'] = false;

        $requests = $request->only('card_number','card_type','expire_month','expire_year','cvv','first_name','last_name', 'userId');

        $validator = validator::make($requests, [

        	'card_number' 	=> 'required',        	
        	'card_type' 	=> 'required',        	
        	'expire_month' 	=> 'required',
        	'expire_year' 	=> 'required',
        	'cvv' 		=> 'required',
        	'first_name'	=> 'required',
        	'last_name'     => 'required',
                'userId'        => 'required',
        ]);

        if ($validator->fails()) {
        
            $response_array['code'] = 406;
            $response_array['result'] = $validator->errors();
            $response_array['message'] = 'failed.';
            $response_array['success'] = false;
        
        } else {

        	try{

        		/*// get id from token with JWT Auth
                $user = JWTAuth::parseToken()->authenticate();*/
                
        		//Variables
        		$cardNumber 	= $request->input('card_number'); 
        		$cardType	    = $request->input('card_type');
        		$expireMonth 	= $request->input('expire_month');
        		$expireYear 	= $request->input('expire_year');
        		$cvv2 			= $request->input('cvv');
        		$firstName 		= $request->input('first_name');
                        $lastName       = $request->input('last_name');
        		$userId 		= $request->input('userId');
        		
                //Check Card exists
                $checkCardNum = 'xxxxxxxxxxxx'.substr($cardNumber, -4);

                $checkCardExists = Cards::where(array('card_number' => $checkCardNum, 'customer_id' => $userId))->first();

                if (count($checkCardExists) > 0) {
                    
                        $response_array['code']     = 406;
                        $response_array['message']  = "This card already added!";
                        $response_array['success']  = false;
                
                }else{

                    //Get access token
            		$checkAccessToken = $this->getAccessToken();

            		if ($checkAccessToken != "") {
            			
            			$url = 'https://api.sandbox.paypal.com/v1/vault/credit-card';
    			      	
    			        $headr                       	= array();
    			        $postField                   	= array();
    			        
    			        $postField['type']           		= $cardType;
    			        $postField['number']         		= $cardNumber;
    			        $postField['expire_month']   		= $expireMonth;
    			        $postField['expire_year']    		= $expireYear;
    			        $postField['first_name']     		= $firstName;
    			        $postField['last_name']      		= $lastName;
    			        $postField['cvv2']      	 		=  $cvv2;
    			        $postField['payer_id']    	 		= 'payer_'.$userId;
    			        $postField['merchant_id'] 	 		= 'merchant_'.$userId;
    			        $postField['external_card_id'] 		= 'external_card_id_'.rand(11111, 99999);
    			        $postField['external_customer_id'] 	= 'external_customer_'.$userId;

    			        $dataString  = json_encode( $postField );

    			      	$headr[]  = 'Content-length: '.strlen( $dataString );
    			        $headr[]  = 'Content-type: application/json';
    			        $headr[]  = "Authorization: Bearer $checkAccessToken";
            			
                                $ch = curl_init( $url );

    			        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    			        curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
    			        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    			        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
    			        curl_setopt( $ch, CURLOPT_POSTFIELDS, $dataString );
    			        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    			        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );

    			        $storeCreditCard    = curl_exec( $ch );			      

                                $storeCreditCard    = json_decode($storeCreditCard);

    			        if(isset($storeCreditCard->state) && $storeCreditCard->state == "ok" ){
    				        
    				        $creditCardId       = $storeCreditCard->id;

    			        	$customerCard = array(

    			        		"card_id" 			=> $creditCardId,
    			        		"external_card_id"	=> $storeCreditCard->external_card_id,
    			        		"customer_id" 		=> $userId,
    			        		"first_name"		=> $storeCreditCard->first_name,
    			        		"last_name"			=> $storeCreditCard->last_name,
    			        		"card_number" 		=> $storeCreditCard->number,
    			        		"expire_month" 		=> $storeCreditCard->expire_month,
    			        		"expire_year" 		=> $storeCreditCard->expire_year,
    			        		"type" 				=> $storeCreditCard->type,
    			        		"created_at"    	=> date('Y-m-d H:i:s')
    			        	);


    			        	$checkInsertCardId = Cards::insertGetId($customerCard);

    			        	if ($checkInsertCardId) {
    							
    							$response_array['code'] 	= 200;
    		            		$response_array['message'] 	= "Card inserted successfully!";
    		            		$response_array['success'] 	= true;			        		

    			        	}
    			        }else{

    					
    			        	$response_array['code'] 	= 406;
    	            		$response_array['message'] 	= isset($storeCreditCard->details) ? $storeCreditCard->details : "Due to some reason card is not added!";
    	            		$response_array['success'] 	= false;

    			        }

            		}else{

            			$response_array['code'] 	= 406;
                		$response_array['message'] 	= "Authentication Failed!";
                		$response_array['success'] 	= false;

            		}
                }
			            		
        	} catch (\Illuminate\Database\QueryException $e) {
                
                $response_array['code'] = 406;
                $response_array['message'] = $e->getMessage();
                $response_array['success'] = false;
            }
        }
        $response = Response::json($response_array);
		return $response;
    }

    /*
    * Name: cardList
    * Created By: Ocean
    * Purpose: To provide card listing
    */

    public function cardList(Request $request){

    	$response_array['code']    = 406;
        $response_array['message'] = "No card added yet!";
        $response_array['success'] = false;

        $requests = $request->only('');

        $validator = validator::make($requests, [
			   	
        ]);

        if ($validator->fails()) {
        
            $response_array['code'] = 406;
            $response_array['result'] = $validator->errors();
            $response_array['message'] = 'failed.';
            $response_array['success'] = false;
        
        } else {

        	try{

                // get id from token with JWT Auth
               // $user = JWTAuth::parseToken()->authenticate();
                $userId = $request->input('userId');
                //print_r($user->id);die;
                //Conditions
                $where = array('customer_id' => $userId);

                //Select from DB
                $select = array('card_id','external_card_id','first_name','last_name','card_number','expire_month','expire_year','type');

                //Get access token
        		$checkAccessToken = $this->getAccessToken();

        		if ($checkAccessToken != "") {
        			
	                //Query to DB
	                $getCards = Cards::where($where)->get($select);

	                if (count($getCards) > 0) {
						
                                $response_array['code'] 	= 200;
            			$response_array['message'] 	= "Cards found!";
            			$response_array['success'] 	= true;
            			$response_array['result'] 	= $getCards;

	                }

        		}else{

        			$response_array['code'] 	= 406;
            		$response_array['message'] 	= "Authentication Failed!";
            		$response_array['success'] 	= false;

        		}

        	}catch (\Illuminate\Database\QueryException $e) {
                
                $response_array['code'] = 406;
                $response_array['message'] = $e->getMessage();
                $response_array['success'] = false;
            }
        }
        $response = Response::json($response_array);
		return $response;
    }

    /*
    * Name: updateCard
    * Created By: Ocean
    * Purpose: To update card. 
    */

    public function updateCard(Request $request){

    	$response_array['code']    = 406;
        $response_array['message'] = "No card added yet!";
        $response_array['success'] = false;

        $requests = $request->only('card_id','card_type','expire_month','expire_year','first_name','last_name');

        $validator = validator::make($requests, [
			
                'card_id' 	=> 'required',
        	'card_type' 	=> 'required',        	
        	'expire_month' 	=> 'required',
        	'expire_year' 	=> 'required',
        	'first_name'	=> 'required',
        	'last_name'     => 'required', 			   	
        ]);

        if ($validator->fails()) {
        
            $response_array['code'] = 406;
            $response_array['result'] = $validator->errors();
            $response_array['message'] = 'failed.';
            $response_array['success'] = false;
        
        }else{

        	try{	
        		// get id from token with JWT Auth
                //$user = JWTAuth::parseToken()->authenticate();
                $userId = $request->input('userId');
                //Variables
                $cardId 		= $request->input('card_id');
        		$cardType	    = $request->input('card_type');
        		$expireMonth 	= $request->input('expire_month');
        		$expireYear 	= $request->input('expire_year');
        		$firstName 		= $request->input('first_name');
        		$lastName 		= $request->input('last_name');
        		
        		  //Get access token
        		$checkAccessToken = $this->getAccessToken();

        		if ($checkAccessToken != "") {

        			$url = 'https://api.sandbox.paypal.com/v1/vault/credit-cards/'.$cardId;

	        		$headr = array();
					      
			        $updatefiels[0]['op'] 		= "replace";
			        $updatefiels[0]['path'] 	= "/first_name";
			        $updatefiels[0]['value'] 	= $firstName;
			        
			        $updatefiels[1]['op'] 		= "replace";
			        $updatefiels[1]['path'] 	= "/last_name";
			        $updatefiels[1]['value'] 	= $lastName;

			       	$updatefiels[2]['op'] 		= "replace";
			        $updatefiels[2]['path'] 	= "/type";
			        $updatefiels[2]['value'] 	= $cardType;

			        $updatefiels[3]['op'] 		= "replace";
			        $updatefiels[3]['path'] 	= "/expire_month";
			        $updatefiels[3]['value'] 	= $expireMonth;

			       	$updatefiels[4]['op'] 		= "replace";
			        $updatefiels[4]['path'] 	= "/expire_year";
			        $updatefiels[4]['value'] 	= $expireYear;


			        $dataString  = json_encode( $updatefiels );
			      	$headr[]  = 'Content-length: '.strlen( $dataString );
			        $headr[]  = 'Content-type: application/json';
			        $headr[]  = "Authorization: Bearer $checkAccessToken";
	    			
	  				$ch = curl_init( $url );

			        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			        curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
			        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PATCH" );
			        curl_setopt( $ch, CURLOPT_POSTFIELDS, $dataString );
			        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );

			        $storeCreditCard    = curl_exec( $ch );
			        $storeCreditCard    = json_decode($storeCreditCard);

			        if (isset($storeCreditCard->debug_id) && $storeCreditCard->debug_id != "") {
						
						$response_array['code'] 	= 406;
	            		$response_array['message'] 	= $storeCreditCard->details;
	            		$response_array['success'] 	= false;			        	

			        }else if(isset($storeCreditCard->state) && $storeCreditCard->state == "ok"){
			        	
			        	//Update on database
			        	Cards::where(array('card_id' => $cardId))->update(array(
			        			
			        			'first_name' 	=> $firstName,
			        			'last_name'  	=> $lastName,
			        			'type'   	 	=> $cardType,
			        			'expire_month' 	=> $expireMonth,
			        			'expire_year'	=> $expireYear
			        	));

                            $response_array['code'] 	= 406;
                            $response_array['message'] 	= "Card updated successfully!";
                            $response_array['success'] 	= false;	
                            }
                    }else{

                        $response_array['code'] 	= 406;
            		$response_array['message'] 	= "Authentication Failed!";
            		$response_array['success'] 	= false;

        		}
        		

        	}catch (\Illuminate\Database\QueryException $e) {
                
                $response_array['code'] = 406;
                $response_array['message'] = $e->getMessage();
                $response_array['success'] = false;
            }
        }

        $response = Response::json($response_array);
        return $response;
    }

    /*
    * Name: deleteCard
    * Created By: Ocean
    * Purpose: To delete card from database and paypal. 
    */
    public function deleteCard(Request $request){

    	$response_array['code']    = 406;
        $response_array['message'] = 'No card added yet!';
        $response_array['success'] = false;

        $requests = $request->only('card_id');
        
        $validator = validator::make($requests, [
     	    'card_id' 		=> 'required', 			   	
        ]);

        if ($validator->fails()) {        
            $response_array['code'] = 406;
            $response_array['result'] = $validator->errors();
            $response_array['message'] = 'failed.';
            $response_array['success'] = false;
        }else{
        	try{	
                 // get id from token with JWT Auth
                //$user = JWTAuth::parseToken()->authenticate();
                $userId = $request->input('userId');
                //Variables
                $cardId = $request->input('card_id');
        		  //Get access token
                    $checkAccessToken = $this->getAccessToken();
                    if ($checkAccessToken != "") {

                            $url = 'https://api.sandbox.paypal.com/v1/vault/credit-cards/'.$cardId;
                            $headr[]  = 'Content-type: application/json';
                            $headr[]  = "Authorization: Bearer $checkAccessToken";
                            $ch = curl_init( $url );
                            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
                            curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
                            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
                            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
                            //curl_setopt( $ch, CURLOPT_POSTFIELDS, $dataString );
                            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );

                            $deleteCreditCard    = curl_exec( $ch );

                            $deleteCreditCard = json_decode($deleteCreditCard);

                            if (isset($deleteCreditCard->debug_id) && $deleteCreditCard->debug_id != "") {

                                            $response_array['code'] 	= 400;
                            $response_array['message'] 	= "Failed to remove the card!";
                            $response_array['success'] 	= false;
                            $response_array['result'] 	= $deleteCreditCard->message;			        	

                            }else{

                                    Cards::where(array('card_id' => $cardId))->forcedelete();

                                    $response_array['code'] 	= 200;
                            $response_array['message'] 	= "Card removed successfully!";
                            $response_array['success'] 	= true;

                            }


                    }else{

                        $response_array['code'] 	= 406;
                        $response_array['message'] 	= "Authentication Failed!";
                        $response_array['success'] 	= false;
                    } 
        	}catch (\Illuminate\Database\QueryException $e) {
                
                $response_array['code'] = 406;
                $response_array['message'] = $e->getMessage();
                $response_array['success'] = false;
            }
        }

        $response = Response::json($response_array);
		return $response;
    }

    /*
    * Name: paymentByCard
    * Created By: Ocean
    * Purpose: Customer can pay by his card.
    */

    public function paymentByCard(Request $request){

        $response_array['code']    = 406;
        $response_array['message'] = "No card added yet!";
        $response_array['success'] = false;

        $requests = $request->all();

        $validator = validator::make($requests, [ 
            'card_id'       => 'required',
            'amount'        => 'required', 
            'userId'        => 'required'
        ]);
        
        if ($validator->fails()) {
        
            $response_array['code'] = 406;
            $response_array['result'] = $validator->errors();
            $response_array['message'] = 'failed.';
            $response_array['success'] = false;
        
        }else{
            try{
                // get id from token with JWT Auth
               // $user = JWTAuth::parseToken()->authenticate();
                $userId = $request->input('userId');
                //Variables
                $cardId = $request->input('card_id');
                $amount = $request->input('amount');
                //Get access token
                $checkAccessToken = $this->getAccessToken();
                if ($checkAccessToken != "") {
                    $url                = 'https://api.sandbox.paypal.com/v1/payments/payment'; 
                    $headr              = array();
                    $postField          = array();

                    $creditCardToken    = array('credit_card_id' => $cardId,
                                                'payer_id'       => "payer_".$userId);

                    $amountDetail       = array('total'   => $amount,
                                                'currency' => "USD");
                    $postField['intent']    = 'sale';
                    $postField['payer']['payment_method'] =  'credit_card';
                    $postField['payer']['funding_instruments'] = array(
                                                        array(
                                                            'credit_card_token' => $creditCardToken
                                                        )
                                                );

                    $postField['transactions'] = array(
                                                    array(
                                                        'amount'      => $amountDetail,
                                                        'description' => "Task"
                                                    )
                                                );

                    $dataString  = json_encode( $postField );    
                    $headr[]     = 'Content-length: '.strlen( $dataString );
                    $headr[]     = 'Content-type: application/json';
                    $headr[]     = "Authorization: Bearer $checkAccessToken";

                    $ch = curl_init( $url );

                    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
                    curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
                    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $dataString );
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );

                    $resp    = curl_exec( $ch );
                    $resp    = json_decode($resp);
                    
                    if( isset($resp->state) && $resp->state == "approved") {
                        
                        $response_array['code']     = 406;
                        $response_array['message']  = "Payment has been successfully done!";
                        $response_array['success']  = false;                        

                    }else{
                        
                        $response_array['code']     = 406;
                        $response_array['message']  = "Payment Failed!".$resp->message;
                        $response_array['success']  = false;                        
                    }
                
                }else{

                    $response_array['code']     = 406;
                    $response_array['message']  = "Authentication Failed!";
                    $response_array['success']  = false;

                }

            }catch (\Illuminate\Database\QueryException $e) {
                
                $response_array['code'] = 406;
                $response_array['message'] = $e->getMessage();
                $response_array['success'] = false;
            }
        }
        $response = Response::json($response_array);
        return $response;
    }
}   

