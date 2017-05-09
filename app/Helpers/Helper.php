<?php

namespace App\Helpers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Mail;
use Auth;
use Config;
use View;
use Input;
use session;
use Crypt;
use Hash;
use Menu;
use Html;
use Illuminate\Support\Str;
use App\User;
use Phoenix\EloquentMeta\MetaTrait; 
use Illuminate\Support\Facades\Lang;
use App\CorporateProfile;
use Validator; 
use App\Position;
use App\InterviewRating;
use App\Interview;
use App\Criteria;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\RatingFeedback;
use PHPMailerAutoload;
use PHPMailer; 
 

class Helper {

    /**
     * function used to check stock in kit
     *
     * @param = null
     */
    
    public function generateRandomString($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

         return $key;
    } 
/* @method : createCompanyGroup
    * @param : email,user_id
    * Response :  string
    * Return : company name
    */
    
/* @method : getCorporateGroupName
    * @param : email
    * Response :  string
    * Return : company name
    */
    public function getCorporateGroupName($email=null)
    {
        $fps =  strripos($email,"@");
        $lps =  strpos(substr($email,$fps),".");
        $company_name = substr($email,$fps+1,$lps-1);
        return  $company_name;       
    } 
/* @method : getCompanyUrl
    * @param : email
    * Response :  string
    * Return : company URL
    */
    public function getCompanyUrl($email=null)
    {   
        $fps =  strripos($email,"@");
        $lps =  strpos(substr($email,$fps),".");
        $company_url = substr($email,$fps+1);
        return  $company_url;       
    }

 
/* @method : isUserExist
    * @param : user_id
    * Response : number
    * Return : count
    */
    static public function isUserExist($user_id=null)
    {
        $user = User::where('id',$user_id)->count(); 
        return $user;
    }
 
/* @method : getpassword
    * @param : email
    * Response :  
    * Return : true or false
    */
    
    public static function getPassword(){
        $password = "";
        $user = Auth::user();
        if(isset($user)){
            $password = Auth::user()->Password;
        }
        return $password;
    }
/* @method : check mobile number
    * @param : mobile_number
    * Response :  
    * Return : true or false
    */     
   
    
    public static function FormatPhoneNumber($number){
        return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number). "\n";
    } 

   /* @method : get user details
    * @param : userid
    * Response : json
    * Return : User details 
   */
   
    public static function getUserDetails($user_id=null)
    {
        $user = User::find($user_id);
        $data['userID'] = $user->id;
        $data['firstName'] = $user->first_name;
        $data['lastName'] = $user->last_name;
       return  $data;
    }
/* @method : send Mail
    * @param : email
    * Response :  
    * Return : true or false
    */
    public  function sendMailFrontEnd($email_content, $template, $template_content)
    {        
        $template_content['verification_token'] =  Hash::make($email_content['receipent_email']);
        $template_content['email'] = isset($email_content['receipent_email'])?$email_content['receipent_email']:'';
        
        return  Mail::send('emails.'.$template, array('content' => $template_content), function($message) use($email_content)
          {
            $name = "admin";
            $message->from('kundan.roy@webdunia.net',$name);  
            $message->to($email_content['receipent_email'])->subject($email_content['subject']);
            
          });
    } 
  /* @method : send Mail
    * @param : email
    * Response :  
    * Return : true or false
    */
     public  function sendMail($email_content, $template)
    {        
          
        return  Mail::send('emails.'.$template, array('content' => $email_content), function($message) use($email_content)
          {
            $name = $_SERVER['SERVER_NAME'];
            $message->from('no-reply@admin.com',$name);  
            $message->to($email_content['receipent_email'])->subject($email_content['subject']);
            
          });
    }
   
   public static function sendEmail( $email_content, $template)
    {
        $mail       = new PHPMailer;
        $html       = view::make('emails.'.$template,['content' => $email_content]);
        $html       = $html->render(); 
        $subject    = $email_content['subject'];

        try {
            $mail->isSMTP(); // tell to use smtp
            $mail->CharSet = "utf-8"; // set charset to utf8
             

            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->Host       = "mail.99ekart.com"; // sets the SMTP server
            $mail->Port       = 587;   
            $mail->SMTPSecure = 'false';                 // set the SMTP port for the GMAIL server
            $mail->Username   = "admin@99ekart.com"; // SMTP account username
            $mail->Password   = "admin@123!"; 

            $mail->setFrom("admin@99ekart.com", "admin");
            $mail->Subject = $subject;
            $mail->MsgHTML($html);
            $mail->addAddress($email_content['receipent_email'], "admin");
            $mail->addAddress("kroy.iips@gmail.com","admin"); 
            //$mail->addAddress("guruhomeshop1983@gmail.com","guruhomeshop");
            //$mail->addReplyTo(‘examle@examle.net’, ‘Information’);
            //$mail->addBCC(‘examle@examle.net’);
            //$mail->addAttachment(‘/home/kundan/Desktop/abc.doc’, ‘abc.doc’); // Optional name
            $mail->SMTPOptions= array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
            );

            $mail->send();
            //echo "success";
            } catch (phpmailerException $e) {
             
            } catch (Exception $e) {
             
            }
         
       
    }
  
     
}
