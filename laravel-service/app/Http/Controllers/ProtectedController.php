<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProtectedController extends Controller
{
    public function __construct()
    {
        $this->middleware('keycloak');
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'This is a protected endpoint',
            'user' => $request->user,
            'service' => 'Laravel Service'
        ]);
    }
} 