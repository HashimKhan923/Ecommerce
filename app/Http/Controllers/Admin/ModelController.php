<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models;
use App\Models\Product;
use Storage;
use Validator;


class ModelController extends Controller
{
    public function index()
    {
      $Models = Models::orderBy('order', 'asc')->get();

      return response()->json(['Models'=>$Models]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'models' => 'required|array'
        ]);
    
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
    
        $errors = [];
    
        foreach ($request->models as $modelData) {
            $modelValidator = Validator::make($modelData, [
                'name' => 'required|unique:models,name',
                'brand_id' => 'required|integer',
                // 'slug' => 'required|string',
                'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'banner' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                // 'meta_title' => 'nullable|string',
                // 'meta_description' => 'nullable|string',
                // 'meta_keywords' => 'nullable|string',
            ]);
    
            if ($modelValidator->fails()) {
                $errors[] = $modelValidator->errors()->all();
                continue;
            }
    
            // Create and save the model
            $new = new Models();
            $new->brand_id = $modelData['brand_id'];
            $new->name = $modelData['name'];
            
            if (isset($modelData['logo']) && $modelData['logo']) {
                $file = $modelData['logo'];
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $file->move(public_path('Model'), $filename);
                $new->logo = $filename;
            }
    
            if (isset($modelData['banner']) && $modelData['banner']) {
                $file = $modelData['banner'];
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $file->move(public_path('Model'), $filename);
                $new->banner = $filename;
            }
    
            // $new->slug = $modelData['slug'];
            // $new->meta_title = $modelData['meta_title'];
            // $new->meta_description = $modelData['meta_description'];
            // $new->meta_keywords = $modelData['meta_keywords'];
            $new->save();
        }
    
        if (!empty($errors)) {
            return response(['errors' => $errors], 422);
        }
    
        $response = ['status' => true, "message" => "New Models Added Successfully!"];
        return response($response, 200);
    }
    

    public function update(Request $request)
    {



        $update = Models::where('id',$request->id)->first();
        $update->brand_id = $request->brand_id;
        $update->name = $request->name;
        if($request->file('logo')){

            $logoPath = public_path('Model/' . $update->logo);
            if (file_exists($logoPath) && is_file($logoPath)) {
                unlink($logoPath);
            }

            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Model'),$filename);
            $update->logo = $filename;
        }

        if($request->file('banner')){

            $bannerPath = public_path('Model/' . $update->banner);
            if (file_exists($bannerPath) && is_file($bannerPath)) {
                unlink($bannerPath);
            }

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Model'),$filename);
            $update->banner = $filename;
        }
        $update->slug = $request->slug;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        $update->meta_keywords = $request->meta_keywords;
        $update->save();

        $response = ['status'=>true,"message" => "Models Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Models::find($id);

        $checkProduct = Product::where('model_id',$id)->first();
        if($checkProduct)
        {
            $response = ['status'=>true,"message" => "first delete the products under this model!"];
            return response($response, 200);
        }

        $logoPath = public_path('Model/' . $file->logo);
        if (file_exists($logoPath) && is_file($logoPath)) {
            unlink($logoPath);
        }

        $bannerPath = public_path('Model/' . $file->banner);
        if (file_exists($bannerPath) && is_file($bannerPath)) {
            unlink($bannerPath);
        }



      $file->delete();

        $response = ['status'=>true,"message" => "Models Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = Models::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {
            $checkProduct = Product::where('model_id',$item->id)->first();

            if($checkProduct)
            {
                $response = ['status'=>true,"message" => "first delete the products under '$item->name' model!"];
                return response($response, 200);
            }

            $logoPath = public_path('Model/' . $item->logo);
            if (file_exists($logoPath) && is_file($logoPath)) {
                unlink($logoPath);
            }
    
            $bannerPath = public_path('Model/' . $item->banner);
            if (file_exists($bannerPath) && is_file($bannerPath)) {
                unlink($bannerPath);
            }

            $item->delete();
        }

        $response = ['status'=>true,"message" => "Models Deleted Successfully!"];
        return response($response, 200);
    }

    public function change_order(Request $request)
    {
        foreach($request->model_order as $index => $model_id) 
        {
            $model = Models::find($model_id);
            if ($model) {
                $model->update(['order' => $index + 1]);
            }
        }
    }
}
