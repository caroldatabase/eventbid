<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With, auth-token');
header('Access-Control-Allow-Credentials: true');

Route::get('/', function () {

   echo "Access Deny!";
});

    Route::match(['post','get'],'getToken','PaymentController@getToken'); 
    Route::match(['post','get'],'saveCard','PaymentController@saveCard');  
        
/*
* Rest API Request , auth  & Route
*/ 
Route::group(['prefix' => 'api/v1'], function()
{   
    Route::group(['middleware' => 'api'], function () {
        
            Route::match(['post','get'],'newEBCategory/create','CustomCategoryController@newEBCategory'); 
            Route::match(['post','get'],'newEBCategory/delete/{id}','CustomCategoryController@newEBCategoryDelete'); 

            Route::match(['post','get'],'contactus','ApiController@contactUs'); 
            Route::match(['post','get'],'EBManagerContactEnquiry','ApiController@EBManagerContactEnquiry');   
            Route::match(['post','get'],'user/signup','ApiController@register');  
            
            Route::match(['post','get'],'user/update-profile/{user_id}','ApiController@updateProfile'); 
            
            Route::match(['post','get'],'user/deactivate/{userId}', 'ApiController@deactivateUser'); 
            
            Route::match(['post','get'],'addPersonalMessage', 'ApiController@addPersonalMessage'); 
            Route::match(['post','get'],'addQualification', 'ApiController@addQualification'); 
            Route::match(['post','get'],'addInsurance', 'ApiController@addInsurance'); 
            
            
            
            Route::match(['post','get'],'user/login', 'ApiController@login'); 
            Route::match(['post','get'],'email_verification','ApiController@emailVerification');  
            Route::match(['post','get'],'user/forget-password','ApiController@forgetPassword'); 
            
            Route::match(['post','get'],'post-task/create','ApiController@postTask'); 
            Route::match(['post','get'],'post-task/getPostTask','ApiController@getPostTask'); 
            Route::match(['post','get'],'post-task/getPostTask/{id}','ApiController@getPostTask'); 

            Route::match(['post','get'],'post-task/update/{id}','ApiController@postTask'); 

            Route::match(['post','get'],'post-task/delete/{id}','ApiController@postTaskDelete'); 
            
            Route::match(['post','get'],'post-task/category','ApiController@category');  
            Route::match(['post','get'],'post-task/getcategory','ApiController@getCategory'); 
            
            Route::match(['post','get'],'category/delete/{id}','ApiController@deleteCategory'); 

            Route::match(['post','get'],'user/details/{id}','ApiController@getUserDetails');  
            
            Route::match(['post','get'],'post-task/request-category','CustomCategoryController@customCategory'); 
            Route::match(['post','get'],'post-task/request-category/delete/{id}','CustomCategoryController@customCategoryDelete'); 
             
            Route::match(['post','get'],'validate_user','ApiController@validateUser');
            // 2-sep-2017
            Route::match(['post','get'],'customerBusinessTask/{id}','ApiController@customerBussinessTask'); 
            Route::match(['post','get'],'bussinessDashboard/{id}','ApiController@bussinessDashboard'); 

            Route::match(['post','get'],'getRecommendTask/{id}','ApiController@getRecommendTask'); 
            
            Route::match(['post','get'],'blog/create','ApiController@createBlog'); 
            Route::match(['post','get'],'getEventbidHub','ApiController@getBlog'); 
            Route::match(['post','get'],'blog/delete/{id}','ApiController@deleteBlog'); 
            Route::match(['post','get'],'blog/update/{id}','ApiController@updateBlog'); 

            Route::match(['post','get'],'assignTask','ApiController@assignTask'); 
            Route::match(['post','get'],'interestUsersList/{id}','ApiController@interestUsersList'); 
            Route::match(['post','get'],'showInterestList','ApiController@showInterestList');
            Route::match(['post','get'],'deleteInterest/{id}','ApiController@deleteInterest');   
        
            Route::match(['post','get'],'makePayment','PaymentController@makePayment'); 
            
             //added by Ocean
            Route::match(['post','get'],'addCard','PaymentController@addCard'); 
            Route::match(['post','get'],'getCards','PaymentController@cardList'); 
            Route::match(['post','get'],'updateCard','PaymentController@updateCard'); 
            Route::match(['post','get'],'deleteCard','PaymentController@deleteCard'); 
            Route::match(['post','get'],'paymentByCard','PaymentController@paymentByCard'); 
               
            Route::match(['post','get'],'user/changePassword/{id}','ApiController@changePassword');

            Route::match(['get','post'],'comment/post',[
                'as' => 'commentPost',
                'uses' => 'ApiController@comment'
                ]
            );

               
            //Route::match(['post','get'],'user/','ApiController@changePassword');
            Route::match(['post','get'],'user/resetPassword','ApiController@resetPassword');
             
            Route::group(['middleware' => 'jwt-auth'], function () 
            { 
               Route::match(['post','get'],'get_condidate_record','APIController@getCondidateRecord'); 
               Route::match(['post','get'],'user/logout','ApiController@logout'); 
               Route::match(['post','get'],'user/details','ApiController@getUserDetails'); 
                
            }
        );
    });    
});
/*
* Admin Based Auth
*/  
  

Route::get('/login','Adminauth\AuthController@showLoginForm'); 
Route::post('password/reset','Adminauth\AuthController@resetPassword');
