<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebsiteFeedback;

class WebsiteFeedBackController extends Controller
{
    public function index()
    {
        $data = WebsiteFeedback::all();

        return response()->json(['data'=>$data]);
    }
}
