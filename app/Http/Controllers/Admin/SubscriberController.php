<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Jobs\SendEmailJob;
use App\Jobs\SendEmailBatchJob;
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
    $userLimit = $request->input('user_limit'); // Number of users to email

    // Select random users up to the specified limit
    $users = Subscriber::where('status', '!=', 'sent')->limit($userLimit)->get();
    $userIds = $users->pluck('id')->toArray();

    // Insert the batch record with the total number of emails
    $batchId = DB::table('email_batches')->insertGetId([
        'total_emails' => $users->count(),
        'from_id' => $users->first()->id ?? null,
        'start_at' => now()
    ]);

    // Schedule the batch creation at a specified time
    $sendDateTime = $request->input('send_date_time'); // Example: '2024-10-22 10:00:00'
    $delay = Carbon::parse($sendDateTime)->diffInSeconds(now());

    // Dispatch a job to create and dispatch the batch later
    SendEmailBatchJob::dispatch($userIds, $details, $batchId)->delay($delay);

    // Return the batch ID for tracking
    return response()->json(['message' => 'Emails scheduled.', 'batch_id' => $batchId], 200);
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
