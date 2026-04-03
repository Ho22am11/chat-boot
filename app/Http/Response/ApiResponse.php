<?php

namespace App\Http\Response;

class ApiResponse {

     public static function success(
        string $message = null,
        $data = [],
        int $status = 200
    ) {
        return response()->json([
            'result'  => true,
            'errNum'  => $status,
            'message' => $message ?? "Operation completed successfully",
            'data'    => $data,
        ], $status);

    }



    public static function error(
        string $message,
        $data = null,
        int $status = 400
    ) {
        return response()->json([
            'result'  => false,
            'errNum'  => $status,
            'message' => $message,
            'data'    => $data,
        ], $status);

    }

}
