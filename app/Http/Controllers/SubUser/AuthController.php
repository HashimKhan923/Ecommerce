<?php

namespace App\Http\Controllers\SubUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {

            $user = User::where('email',$request->email)->first();
            if($user)
            {
                $user->seller_id = $request->seller_id;
                $user->permissions = $request->permissions;
                $user->password = Hash::make($request->password);
            }
            else
            {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->user_type = 'customer';
                $user->seller_id = $request->seller_id;
                $user->permissions = $request->permissions;
                $user->is_active = 1;
            }
            $user->save();
    
            Mail::send(
                'email.Staff.registration',
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password'=> $request->password
                ], 
                function ($message) use ($request) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to($request->email);
                    $message->subject('Staff Registration');
                }
            );
    
            $response = ['status' => true, 'message' => 'Staff Created Successfully.'];
            return response($response, 200);
    
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



    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $new = User::where('id',$request->id)->first();
        $new->permissions = $request->permissions;
        $new->save();


        $response = ['status' => true, 'message' => 'Staff Updated Successfully.'];
        return response($response, 200);
    }

}
