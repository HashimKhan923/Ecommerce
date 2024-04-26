<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSearchingKeyword;


class SearchingKeywordController extends Controller
{
    public function index()
    {
       $data = UserSearchingKeyword::all();

       return response()->json(['data'=>$data]);
    }
}
