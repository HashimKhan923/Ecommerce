<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class TermConditionController extends Controller
{
    public function web_index()
    {
        $data = TermCondition::select('web_text')->first();

        return response()->json(['data'=>$data]);
    }

    public function app_index()
    {
        $data = TermCondition::select('app_text')->first();

        return response()->json(['data'=>$data]);
    }
}