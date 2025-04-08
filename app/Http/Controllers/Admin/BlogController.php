<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Storage;
use Intervention\Image\Facades\Image;


class BlogController extends Controller
{
    public function index()
    {
        $data = Blog::with('user','blog_category')->orderBy('order', 'asc')->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Blog();
        $new->blogcat_id = $request->blogcat_id;
        $new->user_id = $request->user_id;
        $new->title = $request->title;
        if ($request->file('thumbnail')) {
            $image = $request->thumbnail;
            $filename = date('YmdHis') . uniqid() . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();
            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('BlogThumbnail') . '/' . $filename . '.webp');
                
            $new->thumbnail = $filename . '.webp';
            }
        }
        $new->description = $request->description;
        $new->content = $request->content;
        $new->tags = $request->tags;
        $new->save();

        $response = ['status'=>true,"message" => "Blog Created Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = Blog::where('id',$request->id)->first();
        $update->blogcat_id = $request->blogcat_id;
        $update->user_id = $request->user_id;
        $update->title = $request->title;
        if ($request->file('thumbnail')) {
            $path = public_path('BlogThumbnail/' . $update->thumbnail);
            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }

    
            $image = $request->thumbnail;
            $filename = date('YmdHis') . $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();

            if ($fileExtension !== 'svg' && $fileExtension !== 'gif' && $fileExtension !== 'webp') {
            $compressedImage = Image::make($image->getRealPath());
            $compressedImage->encode('webp')->save(public_path('BlogThumbnail') . '/' . $filename . '.webp');
    
            $update->thumbnail = $filename . '.webp';
            }
            else
            {
                $image->move(public_path('BlogThumbnail'),$filename);
                $update->thumbnail = $filename;
            }


        }
        $update->description = $request->description;
        $update->content = $request->content;
        $update->tags = $request->tags;
        $update->save();

        $response = ['status'=>true,"message" => "Blog Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {

      $file = Blog::find($id);  

      $logoPath = public_path('BlogThumbnail/' . $file->thumbnail);
      if (file_exists($logoPath) && is_file($logoPath)) {
          unlink($logoPath);
      }

      $file->delete();
        

        $response = ['status'=>true,"message" => "Blog Deleted Successfully!"];
        return response($response, 200);
    }


    public function multi_delete(Request $request)
    {
        $data = Blog::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {

            $logoPath = public_path('BlogThumbnail/' . $item->thumbnail);
            if (file_exists($logoPath) && is_file($logoPath)) {
                unlink($logoPath);
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Blogs Deleted Successfully!"];
        return response($response, 200);
    }

    public function status($id)
    {
      $blog = Blog::where('id',$id)->first();

      if($blog->status == 1)
      {
        $blog->status = 0;
      }
      else
      {
        $blog->status = 1;
      }

      $blog->save();

      
      $response = ['status'=>true,"message" => "Status Changed Successfully!"];
      return response($response, 200);


    }

    public function change_order(Request $request)
    {
        foreach($request->blog_order as $index => $blog_id) 
        {
            $blog = Blog::find($blog_id);
            if ($blog) {
                $blog->update(['order' => $index + 1]);
            }
        }
    }


    
}
