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
            $fileSize = $image->getSize(); // Get original file size in bytes
    
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
    
            $new->image = $filename . '.webp';
            $new->image_size = filesize(public_path('Banner') . '/' . $filename . '.webp'); // Store compressed file size
        }
    
        $new->mobile_link = $request->mobile_link;
    
        if ($request->file('mobile_image')) {
            $image = $request->mobile_image;
            $filename = date('YmdHis') . uniqid() . $image->getClientOriginalName();
            $fileSize = $image->getSize(); // Get original file size in bytes
    
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
    
            $new->mobile_image = $filename . '.webp';
            $new->mobile_image_size = filesize(public_path('Banner') . '/' . $filename . '.webp'); // Store compressed file size
        }
    
        $new->save();
    
        return response(['status' => true, "message" => "Banner Added Successfully!"], 200);
    }
    
    public function update(Request $request)
    {
        $update = Banner::where('id', $request->id)->first();
        $update->link = $request->link;
    
        if ($request->file('image')) {
            if ($update->image) {
                unlink(public_path('Banner/' . $update->image));
            }
    
            $image = $request->image;
            $filename = date('YmdHis') . uniqid() . $image->getClientOriginalName();
            $fileSize = $image->getSize(); // Get original file size in bytes
    
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
    
            $update->image = $filename . '.webp';
            $update->image_size = filesize(public_path('Banner') . '/' . $filename . '.webp'); // Store compressed file size
        }
    
        $update->mobile_link = $request->mobile_link;
    
        if ($request->file('mobile_image')) {
            if ($update->mobile_image) {
                unlink(public_path('Banner/' . $update->mobile_image));
            }
    
            $image = $request->mobile_image;
            $filename = date('YmdHis') . uniqid() . $image->getClientOriginalName();
            $fileSize = $image->getSize(); // Get original file size in bytes
    
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('Banner') . '/' . $filename . '.webp');
    
            $update->mobile_image = $filename . '.webp';
            $update->mobile_image_size = filesize(public_path('Banner') . '/' . $filename . '.webp'); // Store compressed file size
        }
    
        $update->save();
    
        return response(['status' => true, "message" => "Banner Updated Successfully!"], 200);
    }
    

    public function delete($id)
    {
        $file = Banner::find($id);

        if($file->image)
        {
            unlink(public_path('Banner/'.$file->image));
        }
        if($file->mobile_image)
        {
            unlink(public_path('Banner/'.$file->mobile_image));
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

            if($item->mobile_image)
            {
                unlink(public_path('Banner/'.$item->mobile_image));
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Banners Deleted Successfully!"];
        return response($response, 200);
    }
}
