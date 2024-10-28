<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\EmailBatch;
use App\Jobs\SendEmailJob;
use DB;


class SubscriberController extends Controller
{
    public function index()
    {
        
        $data = Subscriber::all();

        return response()->json(['data' => $data]);
    }

    public function batches()
    {
        
        $data = EmailBatch::all();

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
       
        $userLimit = $request->input('user_limit'); // Get number of users (e.g., 500)
        
        $users = Subscriber::where('status', null)
        ->orderBy('id', 'desc')
        ->take($userLimit)
        ->get();

        $batch = EmailBatch::create([
            'total_emails' => $users->count(),
            'from_id' => $users->first()->id,
            'start_at' => now()
        ]);
        
    
        foreach ($users as $user) {
            SendEmailJob::dispatch($user, $batch->id);
        }
    
        return response()->json(['message' => 'Emails are being sent.'], 200);
    }


    public function trackEmailOpen($batchId, $userId)
    {
        $check = Subscriber::where('id', $userId)->where('status', '!=', 'seen')->exists();
        if ($check) {
            Subscriber::where('id', $userId)->update(['status' => 'seen']);
            
            DB::table('email_batches')->where('id', $batchId)->increment('seen_emails');
        }
    
        return response()->file(public_path('transparent.png'));
    }

    public function trackVisitor($url,$batchId,$userId)
    {
        $decodedUrl = urldecode($url);
        $completeUrl = 'https://dragonautomart.com/' . $decodedUrl;


        $check = Subscriber::where('id', $userId)->where('status', '!=', 'visit')->exists();
        if ($check) {
            Subscriber::where('id', $userId)->update(['status' => 'visit']);
            
            DB::table('email_batches')->where('id', $batchId)->increment('visitors');
        }

        return redirect($completeUrl);
    }


    public function refresh_subscriber()
    {
        Subscriber::whereNotNull('status')->update(['status' => NULL]);   

        return response()->json(['message' => 'status are successfully reset.'], 200);
    }
}
