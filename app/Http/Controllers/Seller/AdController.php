<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdGallery;


class AdController extends Controller
{
    public function store(Request $request)
    {
        $ad = Ad::create($request->all());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('ad_images', 'public');
                AdGallery::create([
                    'ad_id' => $ad->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Ad created successfully', 'ad' => $ad], 201);
    }

    public function show($id)
    {
        $ad = Ad::with('galleries')->findOrFail($id);
        return response()->json($ad);
    }

    public function destroyImage($imageId)
    {
        $image = AdGallery::findOrFail($imageId);
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $ad->update($request->all());

        return response()->json(['message' => 'Ad updated successfully', 'ad' => $ad], 200);
    }

    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->delete();

        return response()->json(['message' => 'Ad deleted successfully'], 200);
    }

    public function index()
    {
        $ads = Ad::with('galleries')->get();
        return response()->json($ads);
    }

    public function edit($id)
    {
        $ad = Ad::with('galleries')->findOrFail($id);
        return response()->json($ad);
    }

}
