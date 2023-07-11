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
                $file->storeAs('public', $filename);
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
                $file= $request->image;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->image = $filename;
        }
        
        $update->save();

        $response = ['status'=>true,"message" => "Banner Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        $file = Banner::find($id);

        $bannerpath = 'app/public'.$file->image;
        if (Storage::exists($bannerpath)) {
            // Delete the file
            Storage::delete($bannerpath);
        }

      $file->delete();

        $response = ['status'=>true,"message" => "Banner Deleted Successfully!"];
        return response($response, 200);
    }
}
