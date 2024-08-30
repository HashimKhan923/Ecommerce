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
        $new->user_id = $request->user_id;
        $new->title = $request->title;
        $new->description = $request->description;
        $new->content = $request->content;
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
        $update->description = $request->description;
        $update->content = $request->content;
        $update->save();

        $response = ['status'=>true,"message" => "Blog Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
      Blog::find($id)->delete();
        

        $response = ['status'=>true,"message" => "Blog Deleted Successfully!"];
        return response($response, 200);
    }


    public function multi_delete(Request $request)
    {
        $data = Blog::whereIn('id',$request->ids)->delete();

        $response = ['status'=>true,"message" => "Blogs Deleted Successfully!"];
        return response($response, 200);
    }


    
}
