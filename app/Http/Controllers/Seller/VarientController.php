<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVarient;

class VarientController extends Controller
{
    public function delete(Request $request)
    {
        $file = ProductVarient::where('id',$request->id)->where('product_id',$request->product_id)->first();


        $fileToDelete = public_path('ProductVarient/'.$file->image);

        if (file_exists($fileToDelete) && is_file($fileToDelete)) {
            unlink($fileToDelete);
        } 
        $file->delete();
        $response = ['status'=>true,"message" => "Varient Deleted Successfully!"];
        return response($response, 200);
    }
}
