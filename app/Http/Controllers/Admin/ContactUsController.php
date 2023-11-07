<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;


class ContactUsController extends Controller
{
    public function index()
    {
        $data = ContactUs::all();

        return response()->json(['data'=>$data]);
    }
}
