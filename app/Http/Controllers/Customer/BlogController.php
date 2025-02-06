<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\Blog;


class BlogController extends Controller
{
    public function index()
    {
        $BlogCategory = BlogCategory::where('status',1)->get();

        $Blogs = Blog::with('user','blog_category')->take(6)->get();

        return response()->json([
            'categories' => $BlogCategory,
            'blogs' => $Blogs
        ]);

    }


    public function load_more($length = null, $catId = null, $searchValue = null)
    {
    
        $Blogs = Blog::with('user','blog_category')->where('status',1);
    
        if ($catId != null) {
            $Blogs->where('cat_id', $catId);
        }
    
        if ($searchValue != null) {
            $Blogs->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', "%$searchValue%")
                      ->orWhere('description', 'like', "%$searchValue%")
                      ->orWhere('content', 'like', "%$searchValue%");
            });
        }
    
        if ($length !== null) {
            $Blogs->skip($length);
        }
    
        $Blogs = $Blogs->take(6)->get();
    
        return response()->json([
            'categories' => $BlogCategory,
            'blogs' => $Blogs
        ]);
    }

    public function detail($id)
    {
        $Blog = Blog::with('user','blog_category')->where('id',$id)->first();

        return response()->json([
            'blogs' => $Blog
        ]);
    }



    
}
