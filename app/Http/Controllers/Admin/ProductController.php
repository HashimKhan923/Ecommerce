<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $Products = Product::all();

        return response()->json(['Products'=>$Products]);
    }

    public function create(Request $request)
    {
        $new = new Product();
        $new->name = $request->name;
        $new->added_by = $request->added_by;
        $new->user_id = $request->user_id;
        $new->category_id = $request->category_id;
        $new->year = $request->year;
        $new->brand_id = $request->brand_id;
        if($request->file('photos'))
        {
            foreach($request->photos as $photos)
            {
                $file= $photos;
                $filename= date('YmdHi').$file->getClientOriginalName();
                $file->move(public_path('ProductGallery'), $filename);
                $ProductGallery[] = $filename;
                
            }

            $new->photos = $ProductGallery;

        }

        if($request->file('thumbnail_img'))
        {

                $file= $request->thumbnail_img;
                $filename= date('YmdHi').$file->getClientOriginalName();
                $file->move(public_path('ProductThumbnail'), $filename);
                
                $new->thumbnail_img = $filename;

        }
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;
        $new->name = $request->name;

    }
}
