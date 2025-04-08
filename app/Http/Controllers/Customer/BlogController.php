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

        $Blogs = Blog::with('user','blog_category')->where('status',1)->orderBy('order', 'asc')->take(6)->get();

        return response()->json([
            'categories' => $BlogCategory,
            'blogs' => $Blogs
        ]);

    }


    public function load_more($length = null, $catId = null, $searchValue = null)
    {
    
        $Blogs = Blog::with('user','blog_category')->where('status',1);
    
        if ($catId != 0) {
            $Blogs->where('cat_id', $catId);
        }
    
        if ($searchValue != 0) {
            $Blogs->where(function ($query) use ($searchValue) {
                $query->where('title', 'like', "%$searchValue%")
                      ->orWhere('description', 'like', "%$searchValue%")
                      ->orWhere('content', 'like', "%$searchValue%")
                      ->orWhereJsonContains('tags', $searchValue);
            });
        }
    
        if ($length !== null) {
            $Blogs->skip($length);
        }
    
        $Blogs = $Blogs->take(6)->get();
    
        return response()->json([
            'blogs' => $Blogs
        ]);
    }

    public function detail($id)
    {
        $Blog = Blog::with('user','blog_category')->where('id',$id)->first();

        $Blog->view += 1;
        $Blog->save();

        return response()->json([
            'blogs' => $Blog
        ]);
    }



    
}
