<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdGallery;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::with('galleries')->where('published', true)->get();
        return response()->json($ads);
    }

    public function show($id)
    {
        $ad = Ad::with('galleries')->where('published', true)->findOrFail($id);
        return response()->json($ad);
    }

    public function search(Request $request)
    {
        $query = Ad::with('galleries')->where('published', true);

        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        if ($request->has('model_id')) {
            $query->where('model_id', $request->input('model_id'));
        }

        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        $ads = $query->get();
        return response()->json($ads);
    }

    public function filterByTag(Request $request, $tag)
    {
        $ads = Ad::with('galleries')
            ->where('published', true)
            ->whereJsonContains('tags', $tag)
            ->get();

        return response()->json($ads);
    }

    public function featuredAds()
    {
        $ads = Ad::with('galleries')->where('published', true)->where('featured', true)->get();
        return response()->json($ads);
    }

    
}
