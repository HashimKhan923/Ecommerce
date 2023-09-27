<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
         $new = new Review(); 
         $new->product_id = $request->product_id;
         $new->user_id = $request->user_id;
         $new->comment = $request->comment;
         $new->rating = $request->rating;
         $new->save();


        $response = ['status'=>true,"message" => "Review submited successfully!"];
        return response($response, 200);
    }
}
