<?php

namespace App\Http\Controllers;

use Validator;
use App\Domain\HashHandler;
use App\Models\RequestResult;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HashHandlerController extends Controller
{
    public function generate(Request $request)
    {
        $request->merge(['str' => $request->route('str')]);
        $validator = Validator::make(
            $request->all(), 
            [
            'str' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return response([], 400);
        }

        try{
            $hashHandler = new HashHandler($request->str);
            $hashResult = $hashHandler->execute();
            return response()->json($hashResult, 200);
        }catch(Exception $e){
            Log::error($e);
            return response()->json([], 500);
        }
        
    }

    public function results(Request $request)
    {
        $request->merge(['page' => $request->route('page')]);
        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric|gt:0',
            'tries' => 'numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return response([], 400);
        }
       
        $columns = ['batch', 'order_number', 'str_in', 'key_found'];
        $registersPerPage = 20;

        try{
            if($request->tries){
                $result = RequestResult::where('tries', '<', $request->tries)->paginate($registersPerPage, $columns, 'page', $request->page);
            }else{
                $result = RequestResult::paginate($registersPerPage, $columns, 'page', $request->page);
            }
            return response()->json($result->items(), 200);
        }catch(Exception $e){
            Log::error($e);
            return response()->json([], 500);
        }
    }

}
