<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Models\EmailBatch;

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
        
        $users = Subscriber::where('status',null)->limit($userLimit)->get();

        if ($users->isEmpty()) {
            // Handle the case where there are no users
            // You might want to log this or return an appropriate response
            return response()->json(['message' => 'No subscribers available to send emails.'], 404);
        }

        $firstId = $users->first();

          $batch = EmailBatch::create([
                'total_emails'=>$users->count(),
                'from_id'=>$firstId->id,
                'start_at'=>now()
            ]);

            $batchId = $batch->id;
    
    // Prepare jobs for each user
    $jobs = [];
    foreach ($users as $user) {
        $jobs[] = new SendEmailJob($user, $details);
    }

    // Dispatch the batch of jobs and store the batch ID
    $batch = Bus::batch($jobs)->dispatch();
        // ->then(function (Batch $batch)  use ($batchId) {
        //     EmailBatch::where('id', $batchId)->update(['completed_at' => now(),'status'=>'completed']);
        // })
        // ->catch(function (Batch $batch, Throwable $e) {
            
        //     return response()->json(['errors'=>$e->getMessage()]);
        // })
        // ->finally(function (Batch $batch) {
        //     // Called when the batch has finished executing
        // })
        // ->dispatch(); // No delay, send immediately

        // EmailBatch::where('id', $batchId)->update(['batch_id' => $batch->id]);

    
        return response()->json(['message' => 'Emails are being sent.'], 200);
    }
}
