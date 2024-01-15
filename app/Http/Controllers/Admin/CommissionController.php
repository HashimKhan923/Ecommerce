<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commission;

class CommissionController extends Controller
{
    public function index()
    {
        $data = Commission::first();

        return response()->json(['data'=>$data]);
    }


    public function createOrUpdate()
    {
        $createOrUpdate = Commission::first();

        if($createOrUpdate == null)
        {
            $createOrUpdate = new Commission();
        }

        $createOrUpdate->value = $request->value;
        $createOrUpdate->type = $request->type;
        $createOrUpdate->save();

        return response()->json(['message'=>'saved successfully!']);
    }
}
