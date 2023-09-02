<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeBanner;
use Storage;


class HomeBannerController extends Controller
{
    public function index()
    {
        $data = HomeBanner::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $check = HomeBanner::first();

        if($check)
        {
            $response = ['status'=>true,"message" => "Already Created!"];
            return response($response, 200);
        }

        $new = new HomeBanner();

        if($request->file('banner1'))
        {
                $file= $request->banner1;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->banner1 = $filename;
        }

        if($request->file('banner2'))
        {
                $file= $request->banner2;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->banner2 = $filename;
        }

        if($request->file('banner3'))
        {
                $file= $request->banner3;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->banner3 = $filename;
        }

        $new->save();

        
        $response = ['status'=>true,"message" => "Home Banners Added Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = HomeBanner::first();

        if($request->file('banner1'))
        {
            $path = 'app/public'.$update->banner1;
            if (Storage::exists($path)) {
                // Delete the file
                Storage::delete($path);
            }
                $file= $request->banner1;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->banner1 = $filename;
        }

        if($request->file('banner2'))
        {

            $path = 'app/public'.$update->banner2;
            if (Storage::exists($path)) {
                // Delete the file
                Storage::delete($path);
            }
                $file= $request->banner2;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->banner2 = $filename;
        }

        if($request->file('banner3'))
        {
            $path = 'app/public'.$update->banner3;
            if (Storage::exists($path)) {
                // Delete the file
                Storage::delete($path);
            }
                $file= $request->banner3;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->banner3 = $filename;
        }

        $update->save();

        
        $response = ['status'=>true,"message" => "Home Banners Added Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        $file = HomeBanner::find($id);

        $path1 = 'app/public'.$file->banner1;
        if (Storage::exists($path1)) {
            // Delete the file
            Storage::delete($path1);
        }

        $path2 = 'app/public'.$file->banner1;
        if (Storage::exists($path2)) {
            // Delete the file
            Storage::delete($path2);
        }

        $path3 = 'app/public'.$file->banner1;
        if (Storage::exists($path3)) {
            // Delete the file
            Storage::delete($path3);
        }

        $file->delete();

        $response = ['status'=>true,"message" => "Home Banners Deleted Successfully!"];
        return response($response, 200);
    }
}
