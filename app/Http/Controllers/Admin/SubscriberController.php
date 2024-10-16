<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Jobs\SendEmailJob;
use DB;

class SubscriberController extends Controller
{
    public function index()
    {
        
        $data = Subscriber::all();

        return response()->json(['data' => $data]);
    }

    public function bulk_create(Request $request)
    {
        foreach($request->email as $email)
        {
            Subscriber::create([
                'email' => $email
            ]);
        }
    }

    public function delete($id)
    {
        Subscriber::find($id)->delete();

        $response = ['status'=>true,"message" => "Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        Subscriber::whereIn('id',$request->ids)->delete();

        $response = ['status'=>true,"message" => "Deleted Successfully!"];
        return response($response, 200);
    }

    public function sendEmail(Request $request)
    {
        $details = $request->only('body');
        $userIds = $request->input('user_ids'); 
    
        
        $users = Subscriber::whereIn('id', $userIds)->get();
    
        foreach ($users as $user) {
            SendEmailJob::dispatch($user, $details);
        }
    
        return response()->json(['message' => 'Emails are being sent.'], 200);
    }
}
