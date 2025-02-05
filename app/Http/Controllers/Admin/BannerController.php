<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Storage;
use Intervention\Image\Facades\Image;


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
    
        if ($request->file('image')) {
            $image = $request->image;
            $filename = date('YmdHis') . uniqid() . $image->getClientOriginalName();

            $fileExtension = $image->getClientOriginalExtension();

            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
            $new->image = $filename . '.webp';
            }
            else
            {
                $image->move(public_path('Banner'),$filename);
                $new->image = $filename;
            }
         
            

        }
    
        $new->mobile_link = $request->mobile_link;
    
        if ($request->file('mobile_image')) {
            $image = $request->mobile_image;
            $filename = date('YmdHis') . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();

            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
            $new->mobile_image = $filename . '.webp';

            }
            else
            {
                $image->move(public_path('Banner'),$filename);
                $new->mobile_image = $filename;
            }
        }
    
        $new->save();
    
        return response(['status' => true, "message" => "Banner Added Successfully!"], 200);
    }
    
    public function update(Request $request)
    {
        $update = Banner::where('id', $request->id)->first();
        $update->link = $request->link;
    
        if ($request->file('image')) {
            $path = public_path('Banner/' . $update->image);
            if (file_exists($path)) {
                unlink($path);
            }
    
            $image = $request->image;
            $filename = date('YmdHis') . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();
            $fileSize = $image->getSize(); // Get original file size in bytes

            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
    
            $update->image = $filename . '.webp';
            }
            else
            {
                $image->move(public_path('Banner'),$filename);
                $update->image = $filename;

            }
            
        }
    
        $update->mobile_link = $request->mobile_link;
    
        if ($request->file('mobile_image')) {
            $path = public_path('Banner/' . $update->mobile_image);
            if (file_exists($path)) {
                unlink($path);
            }
    
            $image = $request->mobile_image;
            $filename = date('YmdHis') . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();
            
            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
    
            
            $update->image = $filename . '.webp';
            }
            else
            {
                $image->move(public_path('Banner'),$filename);
                $update->mobile_image = $filename;

            }
        }
    
        $update->save();
    
        return response(['status' => true, "message" => "Banner Updated Successfully!"], 200);
    }
    

    public function delete($id)
    {
        $file = Banner::find($id);

        $path = public_path('Banner/' . $file->image);
        if (file_exists($path)) {
            unlink($path);
        }
        $path = public_path('Banner/' . $file->mobile_image);
        if (file_exists($path)) {
            unlink($path);
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
            $path = public_path('Banner/' . $item->image);
            if (file_exists($path)) {
                unlink($path);
            }
            $path = public_path('Banner/' . $item->mobile_image);
            if (file_exists($path)) {
                unlink($path);
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Banners Deleted Successfully!"];
        return response($response, 200);
    }
}
