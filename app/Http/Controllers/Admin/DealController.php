<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;

class DealController extends Controller
{
    public function index()
    {
        $data = Deal::with('deal_product')->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Deal();
        $new->name = $request->name;
        $new->page_link = $request->page_link;

        if($request->file('banner')){
            $file= $request->file('banner');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $new->banner = $filename;
        }

        $new->discount_start_date = $request->discount_start_date;
        $new->discount_end_date = $request->discount_end_date;
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
            $file= $request->file('banner');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $update->banner = $filename;
        }

        $update->discount_start_date = $request->discount_start_date;
        $update->discount_end_date = $request->discount_end_date;
        $update->save();

        $response = ['status'=>true,"message" => "Deal Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        Deal::find($id)->delete();

        $response = ['status'=>true,"message" => "Deal Deleted Successfully!"];
        return response($response, 200);
    }
}
