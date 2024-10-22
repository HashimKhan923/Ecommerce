<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Carbon\Carbon;

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
        // Get details from the request
        $details = $request->only('body');
        $userLimit = $request->input('user_limit'); // Get number of users (e.g., 500)
        $sendDateTime = $request->input('send_date_time'); // Send date and time in 'Y-m-d H:i:s' format
    
        // Select random users up to the specified limit
        $users = Subscriber::where('status', '!=', 'sent')->limit($userLimit)->get();
        $firstUserId = $users->first()->id ?? null; 
        // Insert the batch record with the total number of emails
        $batchId = DB::table('email_batches')->insertGetId([
            'total_emails' => $users->count(),
            'from_id' => $firstUserId,
            'start_at' => now()
        ]);
    
        // Prepare email jobs for each user
        $jobs = [];
        foreach ($users as $user) {
            $jobs[] = new SendEmailJob($user, $details, $batchId);
        }
    
        // Dispatch the batch of email jobs
        $batch = Bus::batch($jobs)
            ->delay(Carbon::parse($sendDateTime))  // Schedule for the specified date and time
            ->then(function (Batch $batch) use ($batchId) {
                // Mark the batch as completed when all jobs are done
                DB::table('email_batches')->where('id', $batchId)->update(['completed_at' => now()]);
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // Handle failure scenarios if needed
            })
            ->finally(function (Batch $batch) {
                // Finalize the batch process
            })
            ->dispatch();
    
        // Return the batch ID to track the process
        return response()->json(['message' => 'Emails scheduled.', 'batch_id' => $batch->id], 200);
    }

    public function cancel_batch($batch_id)
    {
        $batch = Bus::findBatch($batch_id);

        if ($batch) {
            $batch->cancel();
            return response()->json(['message' => 'Email batch cancelled.']);
        }
    }

    public function trackEmailOpen($batchId, $userId)
    {
        // Increment the seen_emails count in the email_batches table
        DB::table('email_batches')->where('id', $batchId)->increment('seen_emails');


        // Return a 1x1 pixel transparent image
        return response()->file(public_path('transparent.png'));
    }
}
