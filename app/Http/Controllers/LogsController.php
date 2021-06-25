<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index()
    {
        return response()->json(['success'=>true,'message'=>'This is GET api']);
    }

    public function postMethod(Request $request)
    {
        return response()->json(['success'=>true,'message'=>'This is POST api','data'=>$request->all()]);
    }
}
