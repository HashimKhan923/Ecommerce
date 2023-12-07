<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Storage;

class BannerController extends Controller
{
    public function index()
    {
        $data = Banner::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Banner();
        $new->link = $request->link;

        if($request->file('image'))
        {
            $file= $request->image;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Banner'),$filename);
            $new->image = $filename;
        }
        $new->save();

        $response = ['status'=>true,"message" => "Banner Added Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = Banner::where('id',$request->id)->first();
        $update->link = $request->link;

        if($request->file('image'))
        {

            // if($update->image)
            // {
            //     unlink(public_path('Banner/'.$update->image));
            // }

            $file= $request->image;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Banner'),$filename);
            $update->image = $filename;
        }
        
        $update->save();

        $response = ['status'=>true,"message" => "Banner Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        $file = Banner::find($id);

        if($file->image)
        {
            unlink(public_path('Banner/'.$file->image));
        }

      $file->delete();

        $response = ['status'=>true,"message" => "Banner Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = Banner::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {
            if($item->image)
            {
                unlink(public_path('Banner/'.$item->image));
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Banners Deleted Successfully!"];
        return response($response, 200);
    }
}
