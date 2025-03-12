<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;

class StateController extends Controller
{
    public function index()
    {
          $data = State::all();

          return response()->json(["data"=>$data]);

    }

    public function create(Request $request)
    {
        $new = new State();
        $new->country = $request->country;
        $new->name = $request->name;
        $new->tax = $request->tax;
        $new->shipping = $request->shipping;
        $new->code = $request->code;
        $new->save();

        $response = ['status'=>true,"message" => "New State Added Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = State::where('id',$request->id)->first();
        $update->country = $request->country;
        $update->name = $request->name;
        $update->tax = $request->tax;
        $update->shipping = $request->shipping;
        $update->code = $request->code;
        $update->save();

        $response = ['status'=>true,"message" => "State Updated Successfully!"];
        return response($response, 200);
    }

    public function changeStatus($id)
    {
        $status = State::where('id',$id)->first();

        if($status->status == 1)
        {
            $status->status = 0;
        }
        else
        {
            $status->status = 1;
        }
        $status->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        State::find($id)->delete();

        $response = ['status'=>true,"message" => "State Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        State::whereIn('id',$request->ids)->delete();

        $response = ['status'=>true,"message" => "States Deleted Successfully!"];
        return response($response, 200);
    }
}
