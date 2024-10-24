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
    
        $users = Subscriber::where('status', null)->take($userLimit)->get();
    
        $batch = EmailBatch::create([
            'total_emails' => $users->count(),
            'from_id' => $users->first()->id,
            'start_at' => now(),
        ]);
    
        // Collect jobs in an array
        $jobs = [];
        foreach ($users as $user) {
            $jobs[] = new SendEmailJob($user, $details, $batch->id); // Queue each job
        }
    
        // Create a batch of the collected jobs
        $batchJob = Bus::batch($jobs) // Pass the array of jobs
            ->then(function (Batch $batch) {
                // Actions when all jobs succeed...
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // Actions on job failure...
            })
            ->finally(function (Batch $batch) {
                // Final actions after all jobs finish...
            })
            ->dispatch(); // Dispatch the batch of jobs
    
        return response()->json(['message' => 'Emails are being sent.'], 200);
    }
}
