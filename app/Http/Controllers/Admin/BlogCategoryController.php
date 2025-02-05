<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;

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
        if($request->file('thumbnail')){

            $file= $request->thumbnail;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('BlogCategoryThumbnail'),$filename);
            $new->thumbnail = $filename;
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
        if($request->file('thumbnail')){

            $logoPath = public_path('BlogCategoryThumbnail/' . $update->thumbnail);
            if (file_exists($logoPath) && is_file($logoPath)) {
                unlink($logoPath);
            }

            $file= $request->thumbnail;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('BlogCategoryThumbnail'),$filename);
            $update->thumbnail = $filename;
        }
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        $update->save();

        $response = ['status'=>true,"message" => "Blog Category Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        BlogCategory::find($id)->delete();

        $response = ['status'=>true,"message" => "Blog Category Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        BlogCategory::whereIn('id',$request->ids)->delete();


        $response = ['status'=>true,"message" => "Blog Categories Deleted Successfully!"];
        return response($response, 200);
    }
}
