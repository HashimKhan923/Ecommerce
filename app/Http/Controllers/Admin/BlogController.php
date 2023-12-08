<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Storage;

class BlogController extends Controller
{
    public function index()
    {
        $data = Blog::with('blog_category')->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Blog();
        $new->blogcat_id = $request->blogcat_id;
        $new->title = $request->title;
        $new->slug = $request->slug;
        $new->short_description = $request->short_description;
        $new->description = $request->description;
        if($request->file('banner')){

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Blog'),$filename);
            $new->banner = $filename;
        }

        $new->meta_title = $request->meta_title;

        if($request->file('meta_img')){

            $file= $request->meta_img;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('BlogMetaImage'),$filename);
            $new->meta_img = $filename;
        }

        $new->meta_description = $request->meta_description;
        $new->meta_keywords = $request->meta_keywords;
        $new->save();

        $response = ['status'=>true,"message" => "Blog Created Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = Blog::where('id',$request->id)->first();
        $update->blogcat_id = $request->blogcat_id;
        $update->title = $request->title;
        $update->slug = $request->slug;
        $update->short_description = $request->short_description;
        $update->description = $request->description;
        if($request->file('banner')){

            if($update->banner)
            {
                unlink(public_path('Blog/'.$update->banner));
            }

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Blog'),$filename);
            $update->banner = $filename;
        }

        $update->meta_title = $request->meta_title;

        if($request->file('meta_img')){


            if($update->meta_img)
            {
                unlink(public_path('BlogMetaImage/'.$update->meta_img));
            }

            $file= $request->meta_img;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('BlogMetaImage'),$filename);
            $update->meta_img = $filename;
        }
        $update->meta_description = $request->meta_description;
        $update->meta_keywords = $request->meta_keywords;
        $update->save();

        $response = ['status'=>true,"message" => "Blog Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
      $file = Blog::find($id);

      if($file->banner)
      {
          unlink(public_path('Blog/'.$file->banner));
      }

      if($file->meta_img)
      {
          unlink(public_path('BlogMetaImage/'.$file->meta_img));
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
            if($item->banner)
            {
                unlink(public_path('Blog/'.$item->banner));
            }
      
            if($item->meta_img)
            {
                unlink(public_path('BlogMetaImage/'.$item->meta_img));
            }

            $item->delete();
        }

       

        $response = ['status'=>true,"message" => "Blogs Deleted Successfully!"];
        return response($response, 200);
    }


    
}
