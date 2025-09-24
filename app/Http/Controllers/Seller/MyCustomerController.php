<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyCustomer;

class MyCustomerController extends Controller
{
  public function index($seller_id)
  {


    $data = MyCustomer::with(['customer.time_line' => function ($query) use ($seller_id) {
      $query->where('seller_id', $seller_id);
        }, 'orders' => function ($query) use ($seller_id) {
            $query->where('sellers_id', $seller_id);
        },'orders.order_refund'])
        ->where('seller_id', $seller_id)
    ->get();
  
      return response()->json(['data' => $data]);
  }

  public function detail($id,$seller_id)
  {
      $data = MyCustomer::with(['customer.time_line' => function ($query) use ($seller_id) {
        $query->where('seller_id', $seller_id);
          }, 'orders' => function ($query) use ($seller_id) {
              $query->where('sellers_id', $seller_id);
          },'orders.order_refund'])
          ->where('id', $id)
      ->first();
    
        return response()->json(['data' => $data]);
  }

}
