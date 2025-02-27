<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AllBanner;
use Intervention\Image\Facades\Image;


class AllBannerController extends Controller
{
    public function index()
    {
        $data = AllBanner::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new AllBanner();
        $new->name = $request->name;
        $new->link = $request->link;
    
        if ($request->file('image')) {
            $image = $request->image;
            $filename = date('YmdHis') . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();
    
            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
                $compressedImage = Image::make($image->getRealPath());
                $compressedImage->encode('webp')->save(public_path('AllBanners') . '/' . $filename . '.webp');
                $new->image = $filename . '.webp';
            } else {
                $image->move(public_path('AllBanners'), $filename);
                $new->image = $filename;
            }
        }
    
        $new->mobile_name = $request->mobile_name;
        $new->mobile_link = $request->mobile_link;
    
        if ($request->file('mobile_image')) {
            $mobile_image = $request->mobile_image;
            $filename = date('YmdHis') . $mobile_image->getClientOriginalName();
            $fileExtension = $mobile_image->getClientOriginalExtension();
            $fileSize = $mobile_image->getSize(); // Get file size in bytes
    
            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
                $compressedImage = Image::make($mobile_image->getRealPath());
                $compressedImage->encode('webp')->save(public_path('AllBanners') . '/' . $filename . '.webp');
                $new->mobile_image = $filename . '.webp';
                // $new->mobile_image_size = round(filesize(public_path('AllBanners') . '/' . $filename . '.webp') / 1024, 2) . ' KB'; // Get compressed file size
            } else {
                $mobile_image->move(public_path('AllBanners'), $filename);
                $new->mobile_image = $filename;
                // $new->mobile_image_size =  round($fileSize / 1024, 2) . ' KB';
            }
        }
    
        $new->save();
    
        $response = ['status' => true, "message" => "Created Successfully!"];
        return response($response, 200);
    }
    

    public function update(Request $request)
    {
        $update = AllBanner::where('id', $request->id)->first();
        $update->name = $request->name;
        $update->link = $request->link;
    
        if ($request->file('image')) {
            if ($update->image) {
                unlink(public_path('AllBanners/' . $update->image));
            }
    
            $image = $request->image;
            $filename = date('YmdHis') . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();
            $fileSize = $image->getSize(); // Get file size in bytes
    
            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
                $compressedImage = Image::make($image->getRealPath());
                $compressedImage->encode('webp')->save(public_path('AllBanners') . '/' . $filename . '.webp');
    
                $update->image = $filename . '.webp';
                // $update->image_size = round(filesize(public_path('AllBanners') . '/' . $filename . '.webp') / 1024, 2) . ' KB'; // Get compressed file size
            } else {
                $image->move(public_path('AllBanners'), $filename);
                $update->image = $filename;
                // $update->image_size =  round($fileSize / 1024, 2) . ' KB'; // Store original file size
            }
        }
    
        $update->mobile_name = $request->mobile_name;
        $update->mobile_link = $request->mobile_link;
    
        if ($request->file('mobile_image')) {
            if ($update->mobile_image) {
                unlink(public_path('AllBanners/' . $update->mobile_image));
            }
    
            $mobile_image = $request->mobile_image;
            $filename = date('YmdHis') . $mobile_image->getClientOriginalName();
            $fileExtension = $mobile_image->getClientOriginalExtension();
            $fileSize = $mobile_image->getSize(); // Get file size in bytes
    
            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
                $compressedImage = Image::make($mobile_image->getRealPath());
                $compressedImage->encode('webp')->save(public_path('AllBanners') . '/' . $filename . '.webp');
    
                $update->mobile_image = $filename . '.webp';
                // $update->mobile_image_size = round(filesize(public_path('AllBanners') . '/' . $filename . '.webp') / 1024, 2) . ' KB'; // Get compressed file size
            } else {
                $mobile_image->move(public_path('AllBanners'), $filename);
                $update->mobile_image = $filename;
                // $update->mobile_image_size =  round($fileSize / 1024, 2) . ' KB'; // Store original file size
            }
        }
    
        $update->save();
    
        return response(['status' => true, "message" => "Updated Successfully!"], 200);
    }
    
}
