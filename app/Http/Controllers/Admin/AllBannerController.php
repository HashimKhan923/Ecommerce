<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AllBanner;

class AllBannerController extends Controller
{
    public function index()
    {
        $data = AllBanner::all();

        return response()->json(['data'=>$data]);
    }
}
