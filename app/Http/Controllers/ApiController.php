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
use App\CustomCategory;
use App\CategoryQuestion;
use App\ContactUs;
use App\CommondataFields;
use App\Blogs;


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

        try{
            $columns = \Schema::getColumnListing('users');
            foreach ($columns as $key => $value) {
                if($request->input(lcfirst(studly_case($value)))!=null){
                    $user->$value  =  $request->input(lcfirst(studly_case($value)));   
                }
            } 
            $user->save();
            return response()->json(
                            [ 
                            "status"=>1,
                            'code'   => 200,
                            "message"=> "Profile updated successfully",
                            'data'=>$user
                            ]
                        );
        }catch(\Exception $e){
            return response()->json(
                            [ 
                            "status"=>0,
                            'code'   => 500,
                            "message"=> $e->getMessage(),
                            'data'=>$user
                            ]
                        );
        }
         
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
       
        $validator = Validator::make($request->all(), [
           'categoryName'     => "required" 
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
        $cat->categoryImage         =   $request->input('categoryImage');
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
        $cat =  Category::all(['id','name','categoryImage']);
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
    public function deleteCategory(Request $request, $categoryId) 
    {
        
         $validator = Validator::make(['categoryId'=>$categoryId], [
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
        $cat = Category::where('id','=', $categoryId)->first(); 
            if($cat==null){
                 return Response::json(array(
                    'status' => 0,
                    'code'   => 500,
                    'message' => 'Category id does not exist in database!',
                    'data'  =>  ['categoryId'=>$categoryId]
                    )
                );
            }   
        $cat = Category::where('id',$categoryId)->delete();

        return response()->json(
                            [ 
                                "status"=>1,
                                "code"=>200,
                                "message"=>"Category deleted successfully." ,
                                'data' => ['categoryId'=>$categoryId]
                            ]
                        );

    }
    public function postTaskDelete($post_task_id)
    {
        $obj = PostTask::where('id','=', $post_task_id)->first(); 
            if($obj==null){
                 return Response::json(array(
                    'status' => 0,
                    'code'   => 500,
                    'message' => 'Post Task id does not exist in database!',
                    'data'  =>  ['post_task_id'=>$post_task_id]
                    )
                );
            }   

        PostTask::where('id',$post_task_id)->delete();

        return response()->json(
                            [ 
                                "status"=>1,
                                "code"=>200,
                                "message"=>"Post Task deleted successfully." ,
                                'data' => ['post_task_id'=>$post_task_id]
                            ]
                        );
    }

    public function getPostTask(Request $request, $id=null)
    {
        
        try { 
            $page_num = ($request->get('page_num'))?$request->get('page_num'):1;
            $page_size = ($request->get('page_size'))?$request->get('page_size'):20;
            $task_status = ($request->get('task_status'))?$request->get('task_status'):'open';

            $post_user_id = ($request->get('post_user_id'))?$request->get('post_user_id'):null;
            $seeker_user_id = ($request->get('seeker_user_id'))?$request->get('seeker_user_id'):null;
            $id = isset($id)?$id:$request->get('id');
            $category_id = $request->get('category_id');

            $postTask = PostTask::with('category','postUserDetail','seekerUserDetail')
                    ->where(function($query) 
                    use($category_id,$id,$task_status,$page_num,$page_size,$post_user_id,$seeker_user_id) {

                    if (is_numeric($post_user_id)) {
                        $query->Where('post_user_id',$post_user_id); 
                    }
                    if (is_numeric($seeker_user_id)) {
                        $query->Where('seeker_user_id',$seeker_user_id); 
                    }
                    if (is_numeric($id)) {
                        $query->Where('id',$id) ;
                    }
                    if ($task_status) {
                        $query->Where('task_status',$task_status) ;
                    } 
                    if ($category_id) {
                        $query->Where('category_id',$category_id) ;
                    } 
                         
                });
          
            if($id){
               $post_task =  $postTask->get();
            }else{
                if($page_num==1){
                     $offset = 0;
                 }else{
                    $offset = $page_size*($page_num-1);
                 }
               
               $post_task =  $postTask->offset($offset)
                        ->limit($page_size)
                        ->get();  
            }

        $msg = ($post_task->count())?"Post task record found.":"Post task record not found!";    
             
        }catch(\Exception $e){
            $msg = $e->getMessage();
            $post_task = [];
        }

        $total = PostTask::count();

        return response()->json(
                                    [ 
                                        "status"=>1,
                                        "code"=>($postTask->count())?200:404,
                                        'total_record'=>$total,
                                        'found_record' => $post_task->count(),
                                        'page_num' => intval($page_num),
                                        'page_size' => intval($page_size),
                                        "message"=>$msg,
                                        'data' => $post_task
                                    ]
                                );


    }
    public function postTask(Request $request,$id=null)
    {
        $postTask = new  PostTask;
        $message = "Post task created successfully." ;

        if($id){
            $data =    PostTask::find($id);
            if($data==null){
                  return Response::json(array(
                    'status'    => 0,
                    'code'      => 500,
                    'message'   => 'Post task id does not exist!',
                    'data'      => ['id'=>$id]
                    )
                );
            }else{
                $postTask = PostTask::find($id);
                $postTask->id  = $postTask->id;
                $message = "Post task update successfully.";
            }
        }else{
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
                    'status'    => 0,
                    'code'      => 500,
                    'message'   => $error_msg[0],
                    'data'      => $request->all()
                    )
                );
            } 
        }   
    
        if($request->get('eventTitle')){  
             $postTask->event_title      =  $request->get('eventTitle');
        }
        if($request->get('eventType')){
            $postTask->event_type       =  $request->get('eventType');
        }
        if($request->get('dateRequired')){
             $postTask->date_required      =  $request->get('dateRequired');
        }
        if($request->get('timeFrom')){
             $postTask->time_from           =  $request->get('timeFrom');
        }
        if($request->get('timeTo')){
             $postTask->time_to             =  $request->get('timeTo');
        }
        if($request->get('post_user_id')){
             $postTask->post_user_id        =  $request->get('post_user_id');
        }
        if($request->get('category')){
             $postTask->category_id         =  $request->get('category');
        }
        if($request->get('seeker_user_id')){
             $postTask->seeker_user_id      =  $request->get('seeker_user_id');
        }
        if($request->get('task_status')){
             $postTask->task_status         =  $request->get('task_status');
        }
        $category_question                  =   $request->get('category_question');
        if(is_array($category_question)){ 
             $postTask->category_question   =  json_encode($request->get('category_question'));
        }
         
        $photo = $request->get('inspirationPhoto'); 
        $pic = 1;
        if(is_array($photo)){
           foreach ($photo  as $key => $value) {
                if($key==3){
                    break;
                }

                $keyName = 'inspiration_photo'.++$key;
                
                $img  = explode(',',$value);
                $image = base64_decode($img[1]);
                $image_name= $pic++.time().'.png';
                $path = public_path() . "/images/" . $image_name;
              
                file_put_contents($path, $image); 
                $postTask->$keyName = url::to(asset('public/images/'.$image_name));

                $arr[] = $keyName ;
            }   
            
        }   
        try{
            $postTask->save(); 
           
        }catch(\Exception  $e) { 
            return response()->json(
                                    [ 
                                        "status"=>0,
                                        "code"=>500,
                                        "message"=>$e->getMessage(),
                                        'data' => $postTask
                                    ]
                                );
        }

        
      

        $postTask = ($id)?PostTask::find($id):$postTask;

       // unset($postTask->inspiration_photo1);
        //unset($postTask->inspiration_photo2);
       // unset($postTask->inspiration_photo3);
        return response()->json(
                                    [ 
                                        "status"=>1,
                                        "code"=>200,
                                        "message"=>$message,
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
   
    public function getUserDetails(Request $request, $uid)
    {
        $user =  User::find($uid);
        
        /*$data = [];
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
        $data['token']          =   $token; */
       

        return response()->json(
                [ "status"=>1,
                  "code"=>200,
                  "message"=>"Record found successfully." ,
                  "data" => $user 
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
        $email_response = $helper->sendEmail(
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
    * @param : CustomCategory ID
    * Response : json
    * Return :   getCondidateRecord
    */
    public function contactUs(Request $request,User $user)
    {   
        $input['firstName']    = $request->input('firstName'); 
        $input['lastName']     = $request->input('lastName'); 
        $input['email']        = $request->input('email');  
        $input['comments']     = ($request->input('comments'))?$request->input('comments'):'';
       
         //Server side valiation
        $validator = Validator::make($request->all(), [
           'email' => 'required|email',
            'firstName' => 'required'

        ]);
        /** Return Error Message **/
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
        
        $helper = new Helper;
        /** --Create USER-- **/
      
        $subject = "New Contact mail!";

        $reciver_email = 'eventbid@mailinator.com';

        $email_content = [
                'receipent_email'=> $reciver_email,
                'subject'=>$subject,
                'greeting'=> 'Conatct Mail',
                'name'=> 'Event Bid',
                'data' => $request->all()
                ];

        $verification_email = $helper->sendMailContactUs($email_content,'contactus');

        $contact            =   new ContactUs;
        $contact->firstName =   $request->get('firstName');
        $contact->lastName  =   $request->get('lastName');
        $contact->comments  =   $request->get('comments');
        $contact->email     =   $request->get('email');
        $contact->save();

       
        return response()->json(
                            [ 
                                "status"=>1,
                                "code" => 200,
                                "message"=>"Thank you for contacting us.",
                                'data'=>$request->all()
                            ]
                        );
    }

    public function EBManagerContactEnquiry(Request $request,User $user)
    {   
        $input['firstName']    = $request->input('firstName'); 
        $input['lastName']     = $request->input('lastName'); 
        $input['mailId']        = $request->input('mailId');  
         $input['phoneNumber']        = $request->input('phoneNumber');  
        $input['description']     = ($request->input('description'))?$request->input('description'):'';
       
         //Server side valiation
        $validator = Validator::make($request->all(), [
           'mailId' => 'required|email',
            'firstName' => 'required',
            'phoneNumber' => 'required|numeric'

        ]);
        /** Return Error Message **/
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
        
        $helper = new Helper;
        /** --Create USER-- **/
      
        $subject = "New Enquiry mail!";

        $reciver_email = 'kanikasethi04@gmail.com';

        $email_content = [
                'receipent_email'=> $reciver_email,
                'subject'=>$subject,
                'greeting'=> 'Enquiry Mail',
                'name'=> 'Event Bid',
                'data' => $request->all()
                ];

        $verification_email = $helper->sendMailContactUs($email_content,'enquiry');

        /*$contact            =   new ContactUs;
        $contact->firstName =   $request->get('firstName');
        $contact->lastName  =   $request->get('lastName');
        $contact->mailId  =   $request->get('mailId');
        $contact->phoneNumber     =   $request->get('phoneNumber');
        $contact->description     =   $request->get('description');
        $contact->save();*/


        foreach ($request->all() as $key => $value) {
            $contact            =   new CommondataFields;
            $contact->field_key = $key;
            $contact->field_value = $value;
            $contact->save();
        }

       
        return response()->json(
                            [ 
                                "status"=>1,
                                "code" => 200,
                                "message"=>"Enquiry submitted successfully.",
                                'data'=>$request->all()
                            ]
                        );
    }
 
  
    public function newCategory(Request $request, Category $category) 
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

    public function CustomerBussinessTask($uid)
    {
       return $this->bussinessTask($uid,'seeker_user_id');
    }
    public function bussinessDashboard($uid)
    {
       return $this->bussinessTask($uid,'post_user_id');
    }

    public function bussinessTask($uid=null,$business_type=null)
    {  
        try {
            $task = PostTask::with('category','postUserDetail','seekerUserDetail')
                    ->where($business_type,$uid)->get();
            $result = [];
            foreach ($task as $key => $value) {
                if($value->task_status=="open")
                {
                    $result['openTask'][] = $value;
                }else{
                    $result['progressTask'][] = $value;
                }
                
            }

            if(!empty($result)){
                $msg = "Bussiness Task details" ;
                $status = 1;
                $code = 200;
            }else{
                $msg = "Bussiness Task details not found" ;
                $status = 0;
                $code = 404; 
            }

        }catch(\Exception $e){
            $msg = $e->getMessage();
            $status = 0;
            $code = 500; 
        }
        
        return response()->json(
                                [ 
                                    "status"=>$status,
                                    "code"=>$code,
                                    "message"=>$msg,
                                    'data' => $result
                                ]
                            );



    }


    public function getRecommendTask($user_id)
    {
        $user =  User::find($user_id);
        $category_id = $user->category_id;
        $cat_id = explode(',', $category_id);
         
        try{
            $result = PostTask::select('*')
                                ->with('category','postUserDetail')
                                ->whereIn('category_id',$cat_id)
                                ->where('task_status','open')
                                ->groupBy('event_title')
                                ->limit(10)
                                ->get(); 
            if($result->count()>0){
                $msg    = "Recommended Task" ;
                $status = 1;
                $code   = 200;
            }else{
                $msg    = "Recommended Task not found" ;
                $status = 0;
                $code   = 404; 
            }

        }catch(\Exception $e){
            $msg    = $e->getMessage();
            $status = 0;
            $code   = 500;
            $result = []; 
        }
        return response()->json(
                                [ 
                                    "status"=>$status,
                                    "code"=>$code,
                                    "message"=>$msg,
                                    'data' => $result
                                ]
                            );

    }
    // createBlog
    public function createBlog(Request $request, $id=null)
    {
        $blog = new Blogs;

        $table_cname = \Schema::getColumnListing('blogs');

        $validator = Validator::make($request->all(), [
            'blog_title' => 'required' 
        ]); 
        
        // Return Error Message
        if ($validator->fails()) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status' => 0,
                 'code' => 500,
                'message' => $error_msg[0],
                'data'  =>  []
                )
            );
        }

        $except = ['id','create_at','updated_at'];
        foreach ($table_cname as $key => $value) {
           
           if(in_array($value, $except )){
                continue;
           }

           $blog->$value = $request->get($value);
        }

        $blog->save();

       return  response()->json([ 
                    "status"=>1,
                    "code"=> 200,
                    "message"=>"Blog created successfully.",
                    'data' => $blog
                   ]
                );
    }
    // update blog
    public function updateBlog(Request $request, $id=null)
    {
        $blog = Blogs::find($id);

        if($blog==null)
        {
            return  response()->json([ 
                    "status"=>0,
                    "code"=> 500,
                    "message"=>"Blog id is invalid",
                    'data' => ['id'=>$id]
                   ]
                );
        }

        $table_cname = \Schema::getColumnListing('blogs');

        $except = ['id','create_at','updated_at'];

        $input = $request->all();

        foreach ($table_cname as $key => $value) {
           
           if(in_array($value, $except )){
                continue;
           }

           if(isset($input[$value])) {
               $blog->$value = $request->get($value); 
           } 
        }

        $blog->save();

       return  response()->json([ 
                    "status"=>1,
                    "code"=> 200,
                    "message"=>"Blog updated successfully.",
                    'data' => $blog
                   ]
                );
    }
    // get blog
    public function getBlog(Request $request){

        $blog_course_id= $request->get('blog_course_id');
        $id= $request->get('id');

        $blog = Blogs::where(function($query)use($blog_course_id,$id){
                     if($id){
                          $query->where('id',$id);
                    }
                  
                })->get();

        return  response()->json([ 
                    "status"=>1,
                    "code"=> 200,
                    "message"=>"Blog list",
                    'data' => $blog
                   ]
                );

    }
    // delete Blog
    public function deleteBlog($id=null)
    {
        $blog = Blogs::where('id',$id)->delete();

        return  response()->json([ 
                    "status"=>1,
                    "code"=> 200,
                    "message"=>"Blog deleted successfully.",
                    'data' => []
                   ]
                );
    }


} 