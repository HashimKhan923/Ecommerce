<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;
use Storage;
use Carbon\Carbon;
use App\Models\DealShop;

class DealController extends Controller
{
    public function index()
    {
        $data = Deal::with('deal_product','deal_shop.shop')->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Deal();
        $new->name = $request->name;
        $new->page_link = $request->page_link;

        if($request->file('banner')){

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('DealBanner'),$filename);
            $new->banner = $filename;
        }

        $new->discount_start_date = Carbon::parse($request->discount_start_date);
        $new->discount_end_date = Carbon::parse($request->discount_end_date);
        $new->save();

        $response = ['status'=>true,"message" => "New Deal Added Successfully!"];
        return response($response, 200);
    }



    public function update(Request $request)
    {
        $update = Deal::where('id',$request->id)->first();;
        $update->name = $request->name;
        $update->page_link = $request->page_link;

        if($request->file('banner')){


            $fileToDelete = public_path('DealBanner/'.$update->banner);

            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            } 

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('DealBanner'),$filename);
            $update->banner = $filename;
        }

        $update->discount_start_date = Carbon::parse($request->discount_start_date);
        $update->discount_end_date = Carbon::parse($request->discount_end_date);
        $update->save();

        $response = ['status'=>true,"message" => "Deal Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
      $file = Deal::find($id);

      if($file->banner)
      {
          unlink(public_path('DealBanner/'.$file->banner));
      }

        $file->delete();

        $response = ['status'=>true,"message" => "Deal Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = Deal::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {
            if($item->banner)
            {
                unlink(public_path('DealBanner/'.$item->banner));
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Deals Deleted Successfully!"];
        return response($response, 200);
    }

    public function deal_shop(Request $request)
    {
        DealShop::updateOrCreate(
            ['deal_id' => $request->deal_id, 'shop_id' => $request->shop_id], 
            ['deal_id' => $request->deal_id, 'shop_id' => $request->shop_id]
        );

        $response = ['status'=>true,"message" => "Save Successfully!"];
        return response($response, 200);
    }

    public function delete_deal_shop($id)
    {
        DealShop::find($id)->delete();

        $response = ['status'=>true,"message" => "Deleted Successfully!"];
        return response($response, 200);
    }


    
}
