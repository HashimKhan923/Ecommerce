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

        
        if($request->file('banner1')){

            $file= $request->banner1;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('HomeBanner'),$filename);
            $new->banner1 = $filename;
        }

        if($request->file('banner2')){

            $file= $request->banner2;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('HomeBanner'),$filename);
            $new->banner2 = $filename;
        }

        if($request->file('banner3')){

            $file= $request->banner3;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('HomeBanner'),$filename);
            $new->banner3 = $filename;
        }

        $new->save();

        
        $response = ['status'=>true,"message" => "Home Banners Added Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = HomeBanner::first();

        if($request->file('banner1')){

            // if($update->banner1)
            // {
            //     unlink(public_path('HomeBanner/'.$update->banner1));
            // }

            $file= $request->banner1;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('HomeBanner'),$filename);
            $update->banner1 = $filename;
        }

        if($request->file('banner2')){

            // if($update->banner2)
            // {
            //     unlink(public_path('HomeBanner/'.$update->banner2));
            // }

            $file= $request->banner2;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('HomeBanner'),$filename);
            $update->banner2 = $filename;
        }

        if($request->file('banner3')){

            // if($update->banner3)
            // {
            //     unlink(public_path('HomeBanner/'.$update->banner3));
            // }

            $file= $request->banner3;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('HomeBanner'),$filename);
            $update->banner3 = $filename;
        }

        $update->save();

        
        $response = ['status'=>true,"message" => "Home Banners Added Successfully!"];
        return response($response, 200);
    }

    public function delete()
    {
        $file = HomeBanner::first();

        if($file->banner1)
        {
            unlink(public_path('HomeBanner/'.$file->banner1));
        }

        if($file->banner2)
        {
            unlink(public_path('HomeBanner/'.$file->banner2));
        }

        if($file->banner3)
        {
            unlink(public_path('HomeBanner/'.$file->banner3));
        }

        $file->delete();

        $response = ['status'=>true,"message" => "Home Banners Deleted Successfully!"];
        return response($response, 200);
    }
}
