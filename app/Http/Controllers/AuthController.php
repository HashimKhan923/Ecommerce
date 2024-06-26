<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;

class AuthController extends Controller
{
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
            
            $user = User::with('shop','seller_information')->where('email', $request->email)->first();
            if ($user) {
    
             if($user->remember_token == null)
             {
                if($user->is_active == 1)
                {
                    if (Hash::check($request->password, $user->password)) {
        
                        if($user->user_type == 'seller')
                        {
                            if($user->is_verify != 1)
                            {


                                $response = ['status'=>false,"message" => "Your account is not verify by admin"];
                                return response($response, 422);


                            }
    
                        }


                        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                        $response = ['status'=>true,"message" => "Login Successfully",'token' => $token,'user'=>$user];
                        return response($response, 200);

        
        
                        
                    } else {
                        $response = ['status'=>false,"message" => "Password is incorrect!"];
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
                // $token = uniqid();
                // $user->remember_token = $token;
                // $user->save();
    
                // Mail::send(
                //     'email.seller_verification',
                //     [
                //         'token'=>$token,
                //         'name'=>$user->name,
                //         //'last_name'=>$query->last_name
                //     ], 
                
                // function ($message) use ($user) {
                //     $message->from(env('MAIL_USERNAME'));
                //     $message->to($user->email);
                //     $message->subject('Email Verification');
                // });
    
    
                $response = ['status'=>false,"message" =>'your email is not verified. we have sent a verification link to your email while registration!'];
                return response($response, 422);
             }
    
    
            } else {
                $response = ['status'=>false,"message" =>'User does not exist'];
                return response($response, 422);
            }

        }
        catch (\Exception $e) {
            


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

            return response()->json(['status' => 422, 'message' => $e->getMessage()]);
        }
        
    }

    public function logout ($id) {

        // $offline = User::where('id',$id)->first();
        // $offline->is_online = 0;
        // $offline->save();

        $token = $request->user()->token();
        $token->revoke();
        $response = ['status'=>true,'message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function forgetpassword(Request $req)
    {
        $req->validate([
            'email' => 'required|email'
        ]);
        $query = User::where('email',$req->email)->first();
        if($query == null)
        {
            return response(['status' => false, 'message' => 'Email does not exist',422]);
        }        
        else{
            $token = substr(uniqid(), 0, 6);

            $query->remember_token = $token;
            $query->save();
            Mail::send(
                'email.password-reset',
                [
                    'token'=>$token,
                    'name'=>$query->name,
                    //'last_name'=>$query->last_name
                ], 
            
            function ($message) use ($query) {
                $message->from('support@dragonautomart.com','Dragon Auto Mart');
                $message->to($query->email);
                $message->subject('Forget Password');
            });
            return response(['status' => true, 'message' => 'Token send to your email',200]);

        }

    }

    public function token_check(Request $req)
    {
        $req->validate([
            'token' => 'required'
        ]);
        $query = User::where('remember_token',$req->token)->first();
        if($query == null)
        {
            return response(['status' => false, 'message' => 'Token not match',422]);
        }
        else{
            return response(['status' => true, 'message' => 'Token match','token'=>$req->token,200]);
        }

    }
    public function reset_password(Request $req)
    {
        $req->validate([
            'token'=>'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        $user = User::where('remember_token','=',$req->token)->first();  
        if($user == null)
        {
            return response(['status' => false, 'message' => 'Token not match',422]);
        }
        else
        {
            $user->password = Hash::make($req->password);
            $user->remember_token = null;
            $save = $user->save();
            if($save)
            {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;

                return response(['status' => true, 'message' => 'Success','token' => $token,'user'=>$user,200]);
            }
            else
            {
                return response(['status' => false, 'message' => 'Failed',422]);
            }
        }

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
                //User::where( 'id' , auth::guard('user')->user()->id)->update( array( 'password' =>  $users->password));
                //$request->session()->put('alert', 'success');
                //$request->session()->put('change_passo', 'success');
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


    public function verification($id)
    {
        
   
      $check = User::where('remember_token',$id)->first();

      if($check)
      {
        $check->remember_token = null;
        $check->save();
        $response = ['status' => true, "message" => "Email Verified Successfully!",200];
        $jsonMessage = json_encode($response);
        if($check->user_type == 'seller')
        {
            

            $redirectUrl = "https://seller.dragonautomart.com/login" . urlencode($jsonMessage);
        }
        else
        {
            $redirectUrl = "https://dragonautomart.com/emailverification/?jsonMessage=" . urlencode($jsonMessage);

        }
        
        return redirect($redirectUrl);

      }
      else
      {
        $response = ['status' => true, "message" => "something went wrong!",422];
        $jsonMessage = json_encode($response);
        $redirectUrl = "https://dragonautomart.com/?jsonMessage=" . urlencode($jsonMessage);
        
        return redirect($redirectUrl);
      }


    }


}
