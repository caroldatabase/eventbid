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

   // dd(Hash::make('admin'));
    return view('welcome'); 
});

/*
* Rest API Request , auth  & Route
*/ 
Route::group(['prefix' => 'api/v1'], function()
{   
    Route::group(['middleware' => 'api'], function () {
            Route::match(['post','get'],'user/signup','ApiController@register');  
            Route::match(['post','get'],'user/update-profile/{user_id}','ApiController@updateProfile'); 
            Route::match(['post','get'],'user/login', 'ApiController@login'); 
            Route::match(['post','get'],'email_verification','ApiController@emailVerification');  
            Route::match(['post','get'],'user/forget-password','ApiController@forgetPassword'); 
            
            Route::match(['post','get'],'post-task/create','ApiController@postTask'); 
            Route::match(['post','get'],'post-task/category','ApiController@category');  
            

            Route::match(['post','get'],'validate_user','ApiController@validateUser');
            Route::group(['middleware' => 'jwt-auth'], function () 
            { 
               Route::match(['post','get'],'get_condidate_record','APIController@getCondidateRecord'); 
               Route::match(['post','get'],'user/logout','ApiController@logout'); 
               Route::match(['post','get'],'change_password','ApiController@changePassword');
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