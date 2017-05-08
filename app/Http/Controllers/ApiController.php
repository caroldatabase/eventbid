<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Log\Writer;
use Monolog\Logger as Monolog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests; 
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Config,Mail,View,Redirect,Validator,Response; 
use Auth,Crypt,okie,Hash,Lang,JWTAuth,Input,Closure,URL; 
use JWTExceptionTokenInvalidException; 
use App\Helpers\Helper as Helper;
use App\User;
use App\ProfessorProfile;
use App\StudentProfile;
use App\PostTask;
use App\SubCategory;
use App\Category;




class ApiController extends Controller
{
    
   /* @method : validateUser
    * @param : email,password,firstName,lastName
    * Response : json
    * Return : token and user details
    * Author : kundan Roy
    * Calling Method : get  
    */

    public function __construct(Request $request) {

        if ($request->header('Content-Type') != "application/json")  {
            $request->headers->set('Content-Type', 'application/json');
        }
        $user_id =  $request->input('userId');
       
    } 
    
   /* @method : register
    * @param : email,password,deviceID,firstName,lastName
    * Response : json
    * Return : token and user details
    * Author : kundan Roy
    * Calling Method : get  
    */

    public function register(Request $request,User $user)
    {   
        $user->first_name    =  $request->input('firstName');
        $user->last_name     =  $request->input('lastName');  
        $user->user_type     =  $request->input('userType');  
        $user->company_url   =  $request->input('companyUrl');
        $user->email         =  $request->input('email');
        $user->password      = Hash::make($request->input('password'));


         
        if ($request->input('userId')) {
            $u = $this->updateProfile($request,$user);
            return $u;
        } 
         
         //Server side valiation
        $validator = Validator::make($request->all(), [
           'email'     => "required|email|unique:users,email" ,  
           'password' => 'required',
           'userType' => 'required'

        ]);
         /** Return Error Message **/
        if ($validator->fails()) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'code'   => 500,
                'message' => $error_msg[0],
                'data'  =>  $request->all()
                )
            );
        } 

        $user->save();

        $user   = User::find($user->id);

        $user   = User::where('id',$user->id)->first(['id as userId','first_name as firstName','last_name as lastName','email','user_type as userType']);

        $status = 1;
        $code   = 200;
        $message = "Registration successfully done."; 
        

        /*      
            helper = new Helper;
            $subject = "Welcome to syncabi! Verify your email address to get started";
            $email_content = array('receipent_email'=> $user->email,'subject'=>'subject');
            $verification_email = $helper->sendMailFrontEnd($email_content,'verification_link',['name'=> 'fname']);
        */
        return response()->json(
                            [ 
                            "status"=>$status,
                            'code'   => $code,
                            "message"=>$message,
                            'data'=>$user
                            ]
                        );
    }

/* @method : update User Profile
    * @param : email,password,deviceID,firstName,lastName
    * Response : json
    * Return : token and user details
    * Author : kundan Roy
    * Calling Method : get  
    */
    public function updateProfile(Request $request,User $user,$user_id=null)
    {       
        if(!Helper::isUserExist($user_id))
        {
            return Response::json(array(
                'status' => 0,
                'message' => 'Invalid user ID!',
                'data'  =>  ''
                )
            );
        } 
        $user = User::find($user_id); 
       
        $user->first_name    = ($request->input('firstName'))?$request->input('firstName'):$user->first_name;
        $user->last_name     = ($request->input('lastName'))?$request->input('lastName'):$user->last_name;  
        $user->user_type     = ($request->input('userType'))?$request->input('userType'):$user->user_type;  
        $user->company_url   = ($request->input('companyUrl'))?$request->input('companyUrl'):$user->company_url; 
         
        $user->specialization   = ($request->input('specialization'))?$request->input('specialization'):$user->specialization; 

        $user->about_me         = ($request->input('aboutMe'))?$request->input('aboutMe'):$user->about_me; 

        $user->verification_skills   = ($request->input('verificationSkills'))?$request->input('verificationSkills'):$user->verification_skills; 

        $user->review_rating   = ($request->input('reviewRating'))?$request->input('reviewRating'):$user->review_rating; 

        $user->portfolio        = ($request->input('portfolio'))?$request->input('portfolio'):$user->portfolio; 
         $user->save();
        
        return response()->json(
                            [ 
                            "status"=>1,
                            'code'   => 200,
                            "message"=> "Profile updated successfully",
                            'data'=>$user
                            ]
                        );
         
    }
    public function category(Request $request, Category $category) 
    {  
 
        $parent_id = $request->input('categoryId');
        $level=1;
        while (1) {
           $data = SubCategory::find($parent_id);
           
            if($data)
            {
                $level++;
                $parent_id = $data->parent_id;
                $cname[] = ['id'=>$data->id, 'cname'=>$data->name,'level'=>$data->level];
            }else{
                break;
            }
        }

      //  dd($cname);
       
        $validator = Validator::make($request->all(), [
           'categoryName'     => "required" ,  

        ]);


        if($parent_id == null)
        {
            $cat = Category::where('name',$request->input('categoryName'))->get();
             
            if($cat->count()>0){
                 return Response::json(array(
                    'status' => 0,
                    'code'   => 500,
                    'message' => 'Category name already taken!',
                    'data'  =>  $request->all()
                    )
                );
            }
        }else{
            $cat = Category::where('name',$request->input('categoryName'))
                    ->where('id','!=', $parent_id)->first(); 
            if($cat){
                 return Response::json(array(
                    'status' => 0,
                    'code'   => 500,
                    'message' => 'Category name already taken!',
                    'data'  =>  $request->all()
                    )
                );
            }        
        } 


        if ($validator->fails()) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'code'   => 500,
                'message' => $error_msg[0],
                'data'  =>  $request->all()
                )
            );
        }  
        $cat = new Category;
        if($request->input('categoryId')){
             $cat  = Category::find($parent_id);
        } 

       
        $cat->name                  =   $request->input('categoryName');
        $cat->slug                  =   strtolower(str_slug($request->input('categoryName')));
        $cat->parent_id             =   !empty($request->input('category_id'))?$request->input('category_id'):0; 
        $cat->level                 =   $level;
        
        $cat->save(); 
        $cat->id;

         return response()->json(
                                    [ 
                                        "status"=>1,
                                        "code"=>200,
                                        "message"=>"category created successfully." ,
                                        'data' => $cat
                                    ]
                                );

        
    }

    public function getCategory(Request $request, Category $category) 
    {
        $cat =  Category::all();
        if($cat)
        {
              return response()->json(
                                    [ 
                                        "status"=>1,
                                        "code"=>200,
                                        "message"=>"category list." ,
                                        'data' => $cat
                                    ]
                                );
          }else{
              return response()->json(
                                    [ 
                                        "status"=>0,
                                        "code"=>204,
                                        "message"=>"category list not found." ,
                                        'data' => $cat
                                    ]
                                );
          }
      

    }
    public function deleteCategory(Request $request, Category $category) 
    {
        
         $validator = Validator::make($request->all(), [
           'categoryId'     => "required" ,  

        ]);

        if ($validator->fails()) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'code'   => 500,
                'message' => $error_msg[0],
                'data'  =>  $request->all()
                )
            );
        }  

        $cat = Category::where('id','=', $request->input('categoryId'))->first(); 
            if($cat==null){
                 return Response::json(array(
                    'status' => 0,
                    'code'   => 500,
                    'message' => 'Invalid category id!',
                    'data'  =>  $request->all()
                    )
                );
            }   
        $cat = Category::where('id',$request->input('categoryId'))->delete();

        return response()->json(
                            [ 
                                "status"=>0,
                                "code"=>200,
                                "message"=>"category deleted successfully." ,
                                'data' => $request->all()
                            ]
                        );

    }
    public function postTask(Request $request)
    {
        $postTask = new PostTask;
        $validator = Validator::make($request->all(), [
           'eventTitle'     => "required" ,  
           'eventType'     => 'required'

        ]);

        if ($validator->fails()) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'code'   => 500,
                'message' => $error_msg[0],
                'data'  =>  $request->all()
                )
            );
        }  

        $postTask->event_title      =  $request->get('eventTitle');
        $postTask->event_type       =  $request->get('eventType');
        $postTask->date_required    =  $request->get('dateRequired');
        $postTask->time_from        =  $request->get('timeFrom');
        $postTask->time_to          =  $request->get('timeTo');
        $postTask->category         =  $request->get('category');
        $postTask->inspiration_photo=  $request->get('inspirationPhoto');
        $postTask->save();

        return response()->json(
                                    [ 
                                        "status"=>1,
                                        "code"=>200,
                                        "message"=>"Post task created successfully." ,
                                        'data' => $postTask
                                    ]
                                );



    }
   /* @method : login
    * @param : email,password and deviceID
    * Response : json
    * Return : token and user details
    * Author : kundan Roy   
    */
    public function login(Request $request)
    {    
        
        $validator = Validator::make($request->all(), [
           'email'     => "required|email" ,  
           'password' => 'required'

        ]);
         /** Return Error Message **/
        if ($validator->fails()) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'code'   => 500,
                'message' => $error_msg[0],
                'data'  =>  $request->all()
                )
            );
        }  

        $input = $request->all();
        if (!$token = JWTAuth::attempt(['email'=>$request->input('email'),'password'=>$request->input('password')])) {
            return response()->json(
                                    [
                                     "status"=>0,
                                     'code'   => 500,
                                     "message"=>"Invalid email or password. Try again!" ,
                                     'data' => $request->all() 
                                     ]
                                 );
        }

        $user = JWTAuth::toUser($token); 

        $data['userId']         =   $user->id;
        $data['firstName']      =   $user->first_name; 
        $data['email']          =   $user->email; 
        $data['lastName']       =   $user->last_name;
        $data['userType']       =   $user->user_type;
        $data['token']          =   $token; 
 
            return response()->json(
                                    [ 
                                        "status"=>1,
                                        "code"=>200,
                                        "message"=>"Successfully logged in." ,
                                        'data' => $data 
                                    ]
                                );

    } 
   /* @method : get user details
    * @param : Token and deviceID
    * Response : json
    * Return : User details 
   */
   
    public function getUserDetails(Request $request)
    {
        $user = JWTAuth::toUser($request->input('token'));
        $data = [];
        $data['userId']         =   $user->id;
        $data['firstName']      =   $user->first_name; 
        $data['email']          =   $user->email; 
        $data['lastName']       =   $user->last_name;
        $data['userType']       =   $user->user_type;

        $data['photo']          =   $user->photo;
        $data['specialization'] =   $user->specialization;
        $data['aboutMe']        =   $user->about_me;
        $data['verificationSkills']       =   $user->verification_skills;
        $data['reviewRating']   =   $user->review_rating;
        $data['portfolio']      =   $user->portfolio;
        $data['companyUrl']     =   $user->company_url;
        $data['token']          =   $token; 
       

        return response()->json(
                [ "status"=>1,
                  "code"=>200,
                  "message"=>"Record found successfully." ,
                  "data" => $data 
                ]
            ); 
    }
   /* @method : Email Verification
    * @param : token_id
    * Response : json
    * Return :token and email 
   */
   
    public function emailVerification(Request $request)
    {
        $verification_code = $request->input('verification_code');
        $email    = $request->input('email');

        if (Hash::check($email, $verification_code)) {
           $user = User::where('email',$email)->get()->count();
           if($user>0)
           {
              User::where('email',$email)->update(['status'=>1]);  
           }else{
            echo "Verification link is Invalid or expire!"; exit();
                return response()->json([ "status"=>0,"message"=>"Verification link is Invalid!" ,'data' => '']);
           }
           echo "Email verified successfully."; exit();  
           return response()->json([ "status"=>1,"message"=>"Email verified successfully." ,'data' => '']);
        }else{
            echo "Verification link is Invalid!"; exit();
            return response()->json([ "status"=>0,"message"=>"Verification link is invalid!" ,'data' => '']);
        }
    }
   
   /* @method : logout
    * @param : token
    * Response : "logout message"
    * Return : json response 
   */
    public function logout(Request $request)
    {   
        $token = $request->input('token');
         
        JWTAuth::invalidate($request->input('token'));

        return  response()->json([ 
                    "status"=>1,
                    "code"=> 200,
                    "message"=>"You've successfully signed out.",
                    'data' => ""
                    ]
                );
    }
   /* @method : forget password
    * @param : token,email
    * Response : json
    * Return : json response 
    */
     public function forgetPassword(Request $request)
    {  
        $email = $request->input('email');

        //Server side valiation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        $helper = new Helper;
       
        if ($validator->fails()) {
            $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'message' => $error_msg[0],
                'data'  =>  ''
                )
            );
        }

        $user =   User::where('email',$email)->first();
      
        if($user==null){
            return Response::json(array(
                'status' => 0,
                'message' => "Oh no! The address you provided isn't in our system",
                'data'  =>  ''
                )
            );
        }
        $user_data = User::find($user->id);
        $temp_password = Hash::make($email);
       
        
      // Send Mail after forget password
        $temp_password =  Hash::make($email);
 
        $email_content = array(
                        'receipent_email'   => $request->input('email'),
                        'subject'           => 'Your Account Password',
                        'name'              => $user->first_name,
                        'temp_password'     => $temp_password,
                        'encrypt_key'       => Crypt::encrypt($email)
                    );
        $helper = new Helper;
        $email_response = $helper->sendMail(
                                $email_content,
                                'forgot_password_link'
                            ); 
       
       return   response()->json(
                    [ 
                        "status"=>1,
                        "code"=> 200,
                        "message"=>"Reset password link has sent. Please check your email.",
                        'data' => ''
                    ]
                );
    }

   /* @method : change password
    * @param : token,oldpassword, newpassword
    * Response : "message"
    * Return : json response 
   */
    public function changePassword(Request $request)
    {   
        $user = JWTAuth::toUser($request->input('deviceToken'));
        $user_id = $user->userID; 
        $old_password = $user->password;
     
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6'
        ]);
        // Return Error Message
        if ($validator->fails()) {
            $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                'message' => $error_msg[0],
                'data'  =>  ''
                )
            );
        }

         
        if (Hash::check($request->input('oldPassword'),$old_password)) {

           $user_data =  User::find($user_id);
           $user_data->password =  Hash::make($request->input('newPassword'));
           $user_data->save();
           return  response()->json([ 
                    "status"=>1,
                    "code"=> 200,
                    "message"=>"Password changed successfully.",
                    'data' => ""
                    ]
                );
        }else
        {
            return Response::json(array(
                'status' => 0,
                'message' => "Old password mismatch!",
                'data'  =>  ''
                )
            );
        }         
    }
 
    /*SORTING*/
    public function array_msort($array, $cols)
    {
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;

}
   /* @method : Get Condidate rating
    * @param : InviteUser ID
    * Response : json
    * Return :   getCondidateRecord
    */
    
 
  
    public function InviteUser(Request $request,InviteUser $inviteUser)
    {   
        $user =   $inviteUser->fill($request->all()); 
       
        $user_id = $request->input('userID'); 
        $invited_user = User::find($user_id); 
        
        $user_first_name = $invited_user->first_name ;
        $download_link = "http://google.com";
        $user_email = $request->input('email');

        $helper = new Helper;
        $cUrl =$helper->getCompanyUrl($user_email);
        $user->company_url = $cUrl; 
        /** --Send Mail after Sign Up-- **/
        
        $user_data     = User::find($user_id); 
        $sender_name     = $user_data->first_name;
        $invited_by    = $invited_user->first_name.' '.$invited_user->last_name;
        $receipent_name = "User";
        $subject       = ucfirst($sender_name)." has invited you to join";   
        $email_content = array('receipent_email'=> $user_email,'subject'=>$subject,'name'=>'User','invite_by'=>$invited_by,'receipent_name'=>ucwords($receipent_name));
        $helper = new Helper;
        $invite_notification_mail = $helper->sendNotificationMail($email_content,'invite_notification_mail',['name'=> 'User']);
        $user->save();

        return  response()->json([ 
                    "status"=>1,
                    "code"=> 200,
                    "message"=>"You've invited your colleague, nice work!",
                    'data' => ['receipentEmail'=>$user_email]
                   ]
                );

    }
    
} 