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
        BlogCategory::create([
            'name' => $request->name,
        ]);


        $response = ['status'=>true,"message" => "Blog Category Created Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        BlogCategory::where('id',$request->id)->update([
            'name' => $request->name
        ]);

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
