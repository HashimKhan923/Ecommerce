<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\EmailBatch;
use App\Jobs\SendEmailJob;
use DB;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;


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
        
        $users = Subscriber::where('status',null)->take($userLimit)->get();




                // Create a batch of email jobs
        $batchJob = Bus::batch([])->then(function (Batch $batch) {
            // All jobs completed...
        })->catch(function (Batch $batch, Throwable $e) {
            // Handle failure...
        })->finally(function (Batch $batch) {
            // Final cleanup...
        })->dispatch();


        $batch = EmailBatch::create([
            'batch_id' => $batchJob->id,
            'total_emails' => $users->count(),
            'from_id' => $users->first()->id,
            'start_at' => now()
        ]);
        
        

        
    
        foreach ($users as $user) {
            $batchJob->add(new SendEmailJob($user, $details, $batch->id));
        }
    
        return response()->json(['message' => 'Emails are being sent.'], 200);
    }
}
