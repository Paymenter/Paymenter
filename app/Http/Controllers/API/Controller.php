<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Return a JSON response with a success message.
     */
    public function success($message = 'Success', $data = [], $code = 200)
    {
        if (!isset($data['data'])) {
            $data = ['data' => $data];
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ] + $data, $code);
    }

    /**
     * Return a JSON response with an error message.
     */
    public function error($message = 'Error', $code = 400, $data = [])
    {
        if (!isset($data['data'])) {
            $data = ['data' => $data];
        }

        return response()->json([
            'success' => false,
            'message' => $message,
        ] + $data, $code);
    }

    /**
     * Return a JSON response with a not found message.
     */
    public function notFound($message = 'Not found', $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    /**
     * Return a JSON response with an unauthorized message.
     */
    public function unauthorized($message = 'Unauthorized', $code = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
