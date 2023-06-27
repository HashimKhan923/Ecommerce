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
        $new->slug = $request->slug;
        $new->save();

        $response = ['status'=>true,"message" => "Blog Category Created Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = BlogCategory::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->slug = $request->slug;
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
}
