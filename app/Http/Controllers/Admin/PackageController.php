<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerPackage;
use Illuminate\Support\Facades\Storage;
class PackageController extends Controller
{
    public function index()
    {
        $SellerPackage = SellerPackage::all();

        return response()->json(['SellerPackage'=>$SellerPackage]);  
    }

    public function create(Request $request)
    {


        $new = new SellerPackage();
        $new->name = $request->name;
        $new->amount = $request->amount;
        $new->product_upload_limit = $request->product_upload_limit;

        if($request->file('logo')){
            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Package'),$filename);
            $new->logo = $filename;
        }
        $new->time_name = $request->time_name;
        $new->time_number = $request->time_number;      
        $new->save();

        $response = ['status'=>true,"message" => "New Package Added Successfully!"];
        return response($response, 200);

        }

    public function delete($id)
    {
      $delete = SellerPackage::find($id);

      if($delete->logo)
      {
          unlink(public_path('Package/'.$delete->logo));
      }

      $delete->delete();
      $response = ['status'=>true,"message" => "Package Deleted Successfully!"];
      return response($response, 200);
    }


    public function update(Request $request)
    {


        $update = SellerPackage::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->amount = $request->amount;
        $update->product_upload_limit = $request->product_upload_limit;

        if($request->file('logo')){

            if($update->logo)
            {
                unlink(public_path('Package/'.$update->logo));
            }

            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Package'),$filename);
            $update->logo = $filename;
        }
        $update->time_name = $request->time_name;
        $update->time_number = $request->time_number;      
        $update->save();




        $response = ['status'=>true,"message" => "Package Updated Successfully!"];
        return response($response, 200);    }


    public function changeStatus($id)
    {
        $status = SellerPackage::where('id',$id)->first();

        if($status->status == 1)
        {
            $status->status = 0;
        }
        else
        {
            $status->status = 1;
        }
        $status->save();

        $response = ['status'=>true,"message" => "Package Status Changed Successfully!"];
        return response($response, 200);

    }


    public function multi_delete(Request $request)
    {
        $data = SellerPackage::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {
            if($item->logo)
            {
                unlink(public_path('Package/'.$item->logo));
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Packages Deleted Successfully!"];
        return response($response, 200);
    }
}
