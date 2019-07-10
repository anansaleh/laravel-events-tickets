<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected function returnError($message, $code=404){
        return response()->json([
            'success' => false,
            'message' =>  $message
        ], $code);
    }
}
