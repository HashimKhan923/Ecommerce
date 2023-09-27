<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        Review::create([
            'product_id' => $request->product_id,
            'user_id' => $request->user_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);


        $response = ['status'=>true,"message" => "Review submited successfully!"];
        return response($response, 200);
    }
}
