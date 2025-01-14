<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
use File;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);
    
            if ($validator->fails()) {
                return response(['errors' => $validator->errors()->all()], 422);
            }
    
            $new = new User();
            $new->name = $request->name;
            $new->email = $request->email;
            $new->address = $request->address;
            $new->city = $request->city;
            $new->state = $request->state;
            $new->country = $request->country;
            $new->postal_code = $request->postal_code;
            $new->phone = $request->phone;
            // $token = uniqid();
            // $new->remember_token = $token;
            $new->password = Hash::make($request->password);
            $new->platform = $request->platform;
            $new->device_name = $request->device_name;
            $new->device_type = $request->device_type;
            $new->device_token = $request->device_token;
            $new->location = $request->location;
            $new->user_type = 'customer';
            $new->is_active = 1;
            $new->save();

            $token = $new->createToken('Laravel Password Grant Client')->accessToken;
    
            // Mail::send(
            //     'email.customer_verification',
            //     [
            //         'token' => $token,
            //         'name' => $new->name,
            //     ], 
            //     function ($message) use ($new) {
            //         $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
            //         $message->to($new->email);
            //         $message->subject('Email Verification');
            //     }
            // );
    
            $response = ['status' => true, 'message' => 'Registered Successfully.'];
            return response([$response, 200,'token' => $token,'user'=>$new]);
    
        } catch (Exception $e) {
            // Send error email
            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );
    
            // Log the exception
            Log::error('Registration error', ['exception' => $e]);
    
            // Return a response
            return response(['error' => 'Something went wrong, please try again later.'], 500);
        }
    }


    public function login(Request $request) {

        try
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }
            
            $user = User::where('email', $request->email)->first();
            if ($user) {
    
            if($user->remember_token == null)
            {   
                if($user->is_active == 1)
                {
                    if (Hash::check($request->password, $user->password)) {
                        if($request->device_token)
                        {
                            $user->device_token = $request->device_token;
                            $user->save();
                        }

        
                            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                            $response = ['status'=>true,"message" => "Login Successfully",'token' => $token,'user'=>$user];
                            return response($response, 200);
        
                        
                    } else {
                        $response = ['status'=>false,"message" => "Password mismatch"];
                        return response($response, 422);
                    }
        
                }
                else
                {
                    $response = ['status'=>false,"message" =>'Your Account has been Blocked by Admin!'];
                    return response($response, 422);
                }
            } 
            else
            {
    
    
                $response = ['status'=>false,"message" =>'your email is not verified. we have sent a verification link to your email while registration!'];
                return response($response, 422);
            }  
    
    
            } else {
                $response = ['status'=>false,"message" =>'User does not exist'];
                return response($response, 422);
            }
        }catch (Exception $e) {
        // Send error email
        Mail::send(
            'email.exception',
            [
                'exceptionMessage' => $e->getMessage(),
                'exceptionFile' => $e->getFile(),
                'exceptionLine' => $e->getLine(),
            ],
            function ($message) {
                $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                $message->to('support@dragonautomart.com'); // Send to support email
                $message->subject('Dragon Exception');
            }
        );

        // Log the exception
        Log::error('Registration error', ['exception' => $e]);

        // Return a response
        return response(['error' => 'Something went wrong, please try again later.'], 500);
    }
        


        
    }



    public function social_login(Request $request)
    {
        $check_user = User::where('email',$request->email)->first();


        if($check_user)
        {

            if($request->device_token)
            {
                $check_user->device_token = $request->device_token;
                $check_user->save();
            }

            $token = $check_user->createToken('Laravel Password Grant Client')->accessToken;
            $response = ['status'=>true,"message" => "Login Successfully",'token' => $token,'user'=>$check_user];
            return response($response, 200);
        }
        else
        {
            $new = new User();
            $new->name = $request->name;
            $new->email = $request->email;
            $new->password = Hash::make(uniqid());
            $new->user_type = 'customer';
            $new->platform = $request->platform;
            $new->device_name = $request->device_name;
            $new->device_type = $request->device_type;
            $new->device_token = $request->device_token;
            $new->location = $request->location;
            $new->is_active = 1;
            $new->save();

            Mail::send(
                'email.customer_welcome',
                [
                    'name' => $new->name,
                ], 
                function ($message) use ($new) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to($new->email);
                    $message->subject('Welcome');
                }
            );
            
            $token = $new->createToken('Laravel Password Grant Client')->accessToken;
            $response = ['status'=>true,"message" => "Customer Register Successfully",'token' => $token,'user'=>$new];
            return response($response, 200);
        }
    }


    public function profile_view($id)
    {
      $admin_profile = User::where('id',$id)->first();

      return response()->json(['admin_profile'=>$admin_profile],200);
    }

    public function usercheck(Request $request)
    {
        $user=auth('api')->user();
        return response()->json(['admin_profile'=>$user],200);
    }

    public function profile_update(Request $request){
        
        $id=$request->user_id;
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => "required|email|max:255|unique:users,email,$id,id",
        //     'phone'=>'required|min:10|max:15',
        //     //'password' => 'required|string|min:6|confirmed',
        // ]);
        // if ($validator->fails())
        // {
        //     return response(['errors'=>$validator->errors()->all()], 422);
        // }
        $update=User::find($id);
        $update->name = $request->name;
        $update->email = $request->email;
        $update->address = $request->address;
        $update->city = $request->city;
        $update->state = $request->state;
        $update->country = $request->country;
        $update->postal_code = $request->postal_code;
        $update->phone = $request->phone;
        if($request->file('avatar'))
        {
            


            if($update->avatar)
            {
                unlink(public_path('Profile/'.$update->avatar));
            }
                $file= $request->avatar;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->move(public_path('Profile'),$filename);
                $update->avatar = $filename;
        }
        //$admin->save();
        if($update->save()){
          $response = ['status'=>true,"message" => "Profile Update Successfully","user"=>$update];
          return response($response, 200);
        }
        $response = ['status'=>false,"message" => "Profile Not Update Successfully"];
         return response($response, 422);  
    }

    public function passwordChange(Request $request){
        $controlls = $request->all();
        $id=$request->id;
        $rules = array(
            "old_password" => "required",
            "new_password" => "required|required_with:confirm_password|same:confirm_password",
            "confirm_password" => "required"
        );

        $validator = Validator::make($controlls, $rules);
        if ($validator->fails()) {
            //return redirect()->back()->withErrors($validator)->withInput($controlls);
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('id',$request->id)->first();
        $hashedPassword = $user->password;
 
        if(Hash::check($request->old_password , $hashedPassword )) {
 
            if (!Hash::check($request->new_password , $hashedPassword)) {
                $users =User::find($request->id);
                $users->password = bcrypt($request->new_password);
                $users->save();
                $response = ['status'=>true,"message" => "Password Changed Successfully"];
                return response($response, 200);
            }else{
                $response = ['status'=>true,"message" => "new password can not be the old password!"];
                return response($response, 422);
            }
 
        }else{
            $response = ['status'=>true,"message" => "old password does not matched"];
            return response($response, 422);
        }

    }

    public function delete($id)
    {
        User::find($id)->delete();

        $response = ['status'=>true,"message" => "Account Deleted Successfully!"];
        return response($response, 200);
    }
}
