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

            User::where('email',$request->email)->where('user_type','customer')->delete();

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
            $new->password = Hash::make($request->password);
            $new->user_type = 'staff';
            $new->seller_id = $request->seller_id;
            $new->permissions = $request->permissions;
            $new->is_active = 1;
            $new->save();
    
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
