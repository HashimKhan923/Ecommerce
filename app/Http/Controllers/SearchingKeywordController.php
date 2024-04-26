<?php

namespace App\Http\Controllers;

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
