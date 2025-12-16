<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockNotifyMe;


class NotifyMeController extends Controller
{
    public function index()
    {
        $notifyMeRequests = StockNotifyMe::where('seller_id', auth()->id())
            ->with('product','variant')
            ->orderBy('created_at', 'desc')
            ->get();

        return respose()->json([
            'status' => 'success',
            'data' => $notifyMeRequests
        ]);
    }
}
