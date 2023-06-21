<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

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
        $new->name = $request->name;

        if($request->file('banner'))
        {
                $file= $request->banner;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->banner = $filename;
        }

        if($request->file('icon'))
        {
                $file= $request->icon;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->icon = $filename;
        }

        $new->slug = $request->slug;
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        $new->save();

        $response = ['status'=>true,"message" => "Banner Added Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = Banner::where('id',$request->id)->first();
        $update->name = $request->name;

        if($request->file('banner'))
        {
            $bannerpath = 'app/public'.$update->banner;
            if (Storage::exists($bannerpath))
            {
                Storage::delete($bannerpath);
            }

                $file= $request->banner;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->banner = $filename;
        }

        if($request->file('icon'))
        {

            $bannerpath = 'app/public'.$update->icon;
            if (Storage::exists($iconpath))
            {
                Storage::delete($iconpath);
            }

                $file= $request->icon;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->icon = $filename;
        }

        $update->slug = $request->slug;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        $update->save();

        $response = ['status'=>true,"message" => "Banner Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        $file = Banner::find($id);

        $bannerpath = 'app/public'.$file->logo;
        if (Storage::exists($bannerpath)) {
            // Delete the file
            Storage::delete($bannerpath);
        }

      $file->delete();

        $response = ['status'=>true,"message" => "Banner Deleted Successfully!"];
        return response($response, 200);
    }
}
