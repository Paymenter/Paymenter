<?php
namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use App\Http\Controllers\Controller;

class ClientApiController extends Controller
{ 
    public function successResponse($data, $code = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $code);
    }

    public function error($message, $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}