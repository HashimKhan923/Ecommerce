<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerFandQ;
use App\Models\SellerGuideVideo;

class HelpCenterController extends Controller
{
    public function index()
    {
        $SellerFandQ = SellerFandQ::all();
        $SellerGuideVideo = SellerGuideVideo::all();

        return response()->json(['SellerFandQ'=>$SellerFandQ,'SellerGuideVideo'=>$SellerGuideVideo]);
    }
}
