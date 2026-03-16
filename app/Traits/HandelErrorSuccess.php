<?php
namespace App\Traits;

trait HandelErrorSuccess
{
    public function success($message)
    {
        return response()->json([
            "status" => "success",
            "message" => $message,
            "code" => 200,
        ]);
    }

    public function validationError($e,$message)
    {
        return response()->json([
            "status" => "error",
            "message" => $message,
            "error" => $e->errors()
        ],501);
    }

    public function genericError($e,$message)
    {
        return response()->json([
            "status" => "error",
            "message" => $message,
            "error" => $e->getMessage()
        ],500);
    }

    public function notFoundError($message){
        return response()->json([
            "status"=>"error",
            "Message"=>$message,
        ],404);

    }

}