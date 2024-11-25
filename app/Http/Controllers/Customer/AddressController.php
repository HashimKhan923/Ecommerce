<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function index($id)
    {
        $data = Address::where('user_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Address();
        $new->user_id = $request->user_id;
        $new->address1 = $request->address1;
        $new->address2 = $request->address2;
        $new->phone_number = $request->phone_number;
        $new->city = $request->city;
        $new->state = $request->state;
        $new->postal_code = $request->postal_code;
        $new->country = $request->country;
        $new->save();

        $response = ['status'=>true,"message" => "Address Added Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = Address::where('id',$request->id)->first();
        $update->address1 = $request->address1;
        $update->address2 = $request->address2;
        $update->phone_number = $request->phone_number;
        $update->city = $request->city;
        $update->state = $request->state;
        $update->postal_code = $request->postal_code;
        $update->country = $request->country;
        $update->save();

        $response = ['status'=>true,"message" => "Address Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        Address::find($id)->delete();

        $response = ['status'=>true,"message" => "Address Deleted Successfully!"];
        return response($response, 200);
    }
}
