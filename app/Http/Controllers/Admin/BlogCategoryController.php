<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Intervention\Image\Facades\Image;


class BlogCategoryController extends Controller
{
    public function index()
    {
        $data = BlogCategory::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new BlogCategory();
        $new->name = $request->name;
        if ($request->file('thumbnail')) {
            $image = $request->thumbnail;
            $filename = date('YmdHis') . uniqid() . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();
            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('BlogCategoryThumbnail') . '/' . $filename . '.webp');
                
            $new->thumbnail = $filename . '.webp';
            }
        }
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        $new->save();


        $response = ['status'=>true,"message" => "Blog Category Created Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        BlogCategory::where('id',$request->id)->update([
            'name' => $request->name
        ]);

        $update = BlogCategory::where('id',$request->id)->first();
        $update->name = $request->name;
        if ($request->file('thumbnail')) {


            $logoPath = public_path('BlogCategoryThumbnail/' . $update->thumbnail);
            if (file_exists($logoPath) && is_file($logoPath)) {
                unlink($logoPath);
            }
            
    
            $image = $request->thumbnail;
            $filename = date('YmdHis') . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();

            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('BlogCategoryThumbnail') . '/' . $filename . '.webp');
    
            $update->thumbnail = $filename . '.webp';
            }
            else
            {
                $image->move(public_path('BlogCategoryThumbnail'),$filename);
                $update->thumbnail = $filename;
            }
        }
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        $update->save();

        $response = ['status'=>true,"message" => "Blog Category Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        $file = BlogCategory::find($id);  

        $logoPath = public_path('BlogCategoryThumbnail/' . $file->thumbnail);
        if (file_exists($logoPath) && is_file($logoPath)) {
            unlink($logoPath);
        }
  
        $file->delete();

        $response = ['status'=>true,"message" => "Blog Category Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = BlogCategory::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {

            $logoPath = public_path('BlogCategoryThumbnail/' . $item->thumbnail);
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }

            $item->delete();
        }


        $response = ['status'=>true,"message" => "Blog Categories Deleted Successfully!"];
        return response($response, 200);
    }
}
