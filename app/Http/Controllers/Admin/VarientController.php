<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVarient;

class VarientController extends Controller
{
    public function delete($id)
    {
      $file = ProductVarient::where('id',$request->id)->where('product_id',$request->product_id)->first();


        if($file->image)
        {
            unlink(public_path('ProductVarient/'.$file->image));
        }
      

        $response = ['status'=>true,"message" => "Varient Deleted Successfully!"];
        return response($response, 200);
    }
}
