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
use Auth,Crypt,Hash,Lang,JWTAuth,Input,Closure,URL; 
use JWTExceptionTokenInvalidException; 
use App\Helpers\Helper as Helper;
use App\User;
use App\PostTask;
use App\SubCategory;
use App\Category;
use App\CustomCategory;
use App\NewCategory;
/**
 * Class AdminController
 */
class CustomCategoryController extends Controller {
    /**
     * @var  Repository
     */

    /**
     * Displays all admin.
     *
     * @return \Illuminate\View\View
     */
    public function __construct() { 

    }

   
    /*
     * Dashboard
     * */

    public function index(Course $course, Request $request) 
    {  
    }

    /*
     * create Group method
     * */

    public function customCategory(Request $request, CustomCategory $customCategory)
    {    
        
        $validator = Validator::make($request->all(), [
            'category_title'    => "required|unique:custom_categories,category_title" ,
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|email' 
        ]);
        // Return Error Message
        if ($validator->fails()) {
            $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status'    => 0,
                'code'      => 500,
                'message'   => $error_msg,
                'data'      => $request->all()
                )
            );
        }

        $customCategory->category_title = $request->get('category_title');
        $customCategory->first_name     = $request->get('first_name');
        $customCategory->last_name      = $request->get('last_name');
        $customCategory->email          = $request->get('email');
        $customCategory->description    = $request->get('description');
        $customCategory->created_by     = $request->get('created_by'); 
        $customCategory->save();

         return Response::json(array(
                'status'    => 1,
                'code'      => 200,
                'message'   => 'Category created successfully',
                'data'      => $request->all()
                )
            );
    }

    public function newCategory(Request $request, NewCategory $newCategory)
    {    
        
        $validator = Validator::make($request->all(), [
            'titleOfNewCtaegory'    => "required|unique:new_category,titleOfNewCtaegory" ,
            'firstName'        => 'required',
            'lastName'         => 'required',
            'email'             => 'required|email',
            'userType'         => 'required', 
        ]);
        // Return Error Message
        if ($validator->fails()) {
            $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);     
                    }
                            
            return Response::json(array(
                'status'    => 0,
                'code'      => 500,
                'message'   => $error_msg,
                'data'      => $request->all()
                )
            );
        }
        $newCactegory                   = new NewCategory;

        $newCategory->firstName         = $request->get('firstName');
        $newCategory->lastName          = $request->get('lastName');
        $newCategory->email             = $request->get('email');
        $newCategory->userType          = $request->get('userType'); 
        $newCategory->titleOfNewCtaegory =  $request->get('titleOfNewCtaegory');
        $newCategory->whyNeedCtaegory   = $request->get('whyNeedCtaegory');
        
        $newCategory->save();

         return Response::json(array(
                'status'    => 1,
                'code'      => 200,
                'message'   => 'New Category created successfully',
                'data'      => $request->all()
                )
            );
    }


    
    /*
     *Delete User
     * @param ID
     * 
     */
    public function destroy(Course $course) { 
    }

    public function show(Course $course) {
        
    }

}
