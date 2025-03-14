<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class WordController extends Controller
{
    public function getWords(): JsonResponse
    {
        return response()->json(['message' => 'getWords']);
    }

    public function getDictionaryWords(): JsonResponse
    {
        return response()->json(['message' => 'getDictionaryWords']);
    }

    public function store(): JsonResponse
    {
        return response()->json(['message' => 'store']);
    }

    public function show(): JsonResponse
    {
        return response()->json(['message' => 'show']);
    }

    public function update(): JsonResponse
    {
        return response()->json(['message' => 'update']);
    }

    public function delete(): JsonResponse
    {
        return response()->json(['message' => 'delete']);
    }

    public function move(): JsonResponse
    {
        return response()->json(['message' => 'move']);
    }

    public function progress(): JsonResponse
    {
        return response()->json(['message' => 'progress']);
    }
}
