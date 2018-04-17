<?php

namespace App\Http\Controllers\V1;

trait ApiActions
{
    public function respond($data, $status = 200)
    {
        return response()->json($data, $status);
    }
}