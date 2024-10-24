<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\EmailBatch;
use App\Jobs\SendEmailJob;
use Carbon\Carbon;
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
        $userLimit = $request->input('user_limit'); // Get number of users (e.g., 500)
        $scheduledTime = $request->input('send_at');
        
        $users = Subscriber::where('status',null)->take($userLimit)->get();

        $batch = EmailBatch::create([
            'total_emails' => $users->count(),
            'from_id' => $users->first()->id,
            'start_at' => now()
        ]);
        
        
            // Calculate delay if a scheduled time is provided
        $delay = $scheduledTime ? now()->diffInSeconds(Carbon::parse($scheduledTime), false) : 0;
        
    
        foreach ($users as $user) {
            if ($delay > 0) {
                SendEmailJob::dispatch($user, $details, $batch->id)->delay(now()->addSeconds($delay));
            } else {
                SendEmailJob::dispatch($user, $details, $batch->id);
            }
        }
    
        return response()->json(['message' => 'Emails are being sent.'], 200);
    }
}
