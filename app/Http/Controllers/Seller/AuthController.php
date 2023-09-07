<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\SellerInfromation;
use Hash;
use Mail;

class AuthController extends Controller
{
    public function register (Request $request) {
        
        User::where('email',$request->email)->where('user_type','customer')->delete();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            "email" => "required|email|unique:users,email",
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
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
        $token = uniqid();
        $new->remember_token = $token;
        $new->password = Hash::make($request->password);
        $new->user_type = 'seller';
        $new->is_active = 1;
        $new->save();

        $new1 = new SellerInfromation();
        $new1->user_id = $new->id;
        $new1->social_security_number = $request->social_security_number;
        $new1->business_ein_number = $request->business_ein_number;
        $new1->credit_card_number = $request->credit_card_number;
        $new1->paypal_address = $request->paypal_address;
        if($request->file('document'))
        {
                $file= $request->document;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new1->document = $filename;
        }

        if($request->file('social_security_card_front'))
        {
                $file= $request->social_security_card_front;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new1->social_security_card_front = $filename;
        }

        if($request->file('social_security_card_back'))
        {
                $file= $request->social_security_card_back;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new1->social_security_card_back = $filename;
        }

        $new1->save();



        Mail::send(
            'email.seller_verification',
            [
                'token'=>$token,
                'name'=>$new->name,
                //'last_name'=>$query->last_name
            ], 
        
        function ($message) use ($new) {
            $message->from(env('MAIL_USERNAME'));
            $message->to($new->email);
            $message->subject('Email Verification');
        });


        
        
        $response = ['status'=>true,"message" => "we have send the verification email to your gmail please verify your account"];
        return response($response, 200);
    }

    public function login(Request $request) {
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
    
                    if($user->user_type == 'seller')
                    {
                        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                        $response = ['status'=>true,"message" => "Login Successfully",'token' => $token,'user'=>$user];
                        return response($response, 200);
                    }
                    else
                    {
                        $response = ['status'=>false,"message" => "You are not a seller"];
                        return response($response, 422);
                    }
    
    
                    
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
            $token = uniqid();
            $user->remember_token = $token;
            $user->save();

            Mail::send(
                'email.seller_verification',
                [
                    'token'=>$token,
                    'name'=>$user->name,
                    //'last_name'=>$query->last_name
                ], 
            
            function ($message) use ($user) {
                $message->from(env('MAIL_USERNAME'));
                $message->to($user->email);
                $message->subject('Email Verification');
            });

            $response = ['status'=>false,"message" =>'your email is not verified. we have sent a verification link to your email!'];
            return response($response, 422);
         }


        } else {
            $response = ['status'=>false,"message" =>'User does not exist'];
            return response($response, 422);
        }
    }

    public function profile_view($id)
    {
      $admin_profile = User::with('seller_information')->where('id',$id)->first();

      return response()->json(['admin_profile'=>$admin_profile],200);
    }

    public function usercheck(Request $request)
    {
        $user=auth('api')->user();
        return response()->json(['admin_profile'=>$user],200);
    }

    public function profile_update(Request $request){
        $id=$request->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => "required|email|max:255|unique:users,email,$id,id",
            'phone_number'=>'required|min:10|max:15',
            //'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $admin=User::find($id);
        $admin->name=$request->name;
        $admin->email=$request->email;
        $admin->phone_number=$request->phone_number;
        if($request->file('avatar'))
        {
                $file= $request->avatar;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->avatar = $filename;
        }
        $admin->save();

        $admin1 = SellerInformation::where('user_id',$admin->id)->first();
        $admin1->user_id = $admin->id;
        $admin1->social_security_number = $request->social_security_number;
        $admin1->business_ein_number = $request->business_ein_number;
        $admin1->credit_card_number = $request->credit_card_number;
        $admin1->paypal_address = $request->paypal_address;
        if($request->file('document'))
        { 
            $path = 'app/public'.$update->document;
            if (Storage::exists($path)) {
                // Delete the file
                Storage::delete($path);
            }
            
                $file= $request->document;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $admin1->document = $filename;
        }

        if($request->file('social_security_card_front'))
        {
            $path = 'app/public'.$update->social_security_card_front;
            if (Storage::exists($path)) {
                // Delete the file
                Storage::delete($path);
            }
                $file= $request->social_security_card_front;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $admin1->social_security_card_front = $filename;
        }

        if($request->file('social_security_card_back'))
        {
            $path = 'app/public'.$update->social_security_card_back;
            if (Storage::exists($path)) {
                // Delete the file
                Storage::delete($path);
            }
                $file= $request->social_security_card_back;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $admin1->social_security_card_back = $filename;
        }

        
        if($admin1->save()){
          $response = ['status'=>true,"message" => "Profile Update Successfully","user"=>$admin];
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
}
