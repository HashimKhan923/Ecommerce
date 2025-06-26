<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplete;

class EmailTempleteController extends Controller
{

    public function index($seller_id)
    {
      $data = EmailTemplete::where('seller_id',$seller_id)->get();

        return response()->json(['data'=>$data]);

    }

    public function create(Request $request)
    {
        EmailTemplete::create([
            'seller_id' => $request->seller_id,
            'name' => $request->name,
            'content' => $request->content,
        ]);

        return response()->json(['message'=>'Created Successfully!']);
    }

    public function detail($id)
    {
        $data = EmailTemplete::find($id);

        return response()->json(['data'=>$data]);
    }

    public function update(Request $request)
    {
        EmailTemplete::where('id',$request->template_id)->update([
            'name' => $request->name,
            'content' => $request->content,
        ]);

        return response()->json(['message'=>'Updated Successfully!']);
    }

    public function delete($id)
    {
        EmailTemplete::find($id)->delete();

        return response()->json(['message'=>'Deleted Successfully!']);

    }

    public function multi_delete(Request $request)
    {
        EmailTemplete::whereIn('id',$request->ids)->delete();


        $response = ['status'=>true,"message" => "Templets Deleted Successfully!"];
        return response($response, 200);
    }
}
