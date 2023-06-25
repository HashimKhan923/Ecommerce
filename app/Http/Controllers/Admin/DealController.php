<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;

class DealController extends Controller
{
    public function index()
    {
        $data = Deal::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Deal();
        $new->name = $request->name;
        $new->page_link = $request->page_link;

        if($request->file('banner')){
            $file= $request->file('banner');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $new->banner = $filename;
        }

        $new->discount_start_date = $request->discount_start_date;
        $new->descount_end_date = $request->descount_end_date;
    }
}
