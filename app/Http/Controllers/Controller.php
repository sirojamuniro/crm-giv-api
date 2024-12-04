<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function handleResponse($isSuccess = true, $message = null, $data = null, $httpStatus = 200)
    {
        $response = [
            'isSuccess' => $isSuccess,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $httpStatus);
    }
}
