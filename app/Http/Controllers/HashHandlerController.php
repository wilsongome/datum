<?php

namespace App\Http\Controllers;

use App\Domain\HashHandler;
use Illuminate\Http\Request;

class HashHandlerController extends Controller
{
    public function generate(Request $request)
    {
        //Aqui farei validações

        $hashHandler = new HashHandler($request->str);
        $hashResult = $hashHandler->execute();
        
        return response()->json($hashResult, 200);
    }
}
