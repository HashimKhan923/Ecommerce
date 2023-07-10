<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RefundTime;

class RefundTimeController extends Controller
{
    public function index()
    {
        $data = RefundTime::first();

        return response()->json(['data'=>$data]);
    }

    public function createOrupdate(Request $request)
    {
        $createOrupdate = RefundTime::first();

        if($createOrupdate)
        {
            $createOrupdate->days = $request->days;

            if($request->file('sticker')){
                $file= $request->file('sticker');
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $createOrupdate->sticker = $filename;
            }
            else
            {
                $createOrupdate = new RefundTime();
                $createOrupdate->days = $request->days;
                if($request->file('sticker')){
                    $file= $request->file('sticker');
                    $filename= date('YmdHis').$file->getClientOriginalName();
                    $file->storeAs('public', $filename);
                    $createOrupdate->sticker = $filename;
                }

            }

            $createOrupdate->save();

            $response = ['status'=>true,"message" => "Refund Time Save Successfully!"];
            return response($response, 200);
        }
    }
}
