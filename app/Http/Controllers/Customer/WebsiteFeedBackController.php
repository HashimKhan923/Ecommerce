<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebsiteFeedback;

class WebsiteFeedBackController extends Controller
{
    public function create(Request $request)
    {
        $new = new WebsiteFeedback();
        $new->rating = $request->rating;
        $new->feedback = $request->feedback;
        $new->device_type = $request->device_type;
        $new->device_name = $request->device_name;
        $new->save();

        $response = ['status' => true, "message" => "Sent Successfully!"];
        return response($response, 200);
    }
}
