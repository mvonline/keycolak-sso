<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/public', function () {
    return response()->json([
        'message' => 'This is a public endpoint',
        'service' => 'Laravel Service'
    ]);
});

Route::middleware('auth:api')->get('/protected', function (Request $request) {
    return response()->json([
        'message' => 'This is a protected endpoint',
        'service' => 'Laravel Service',
        'user' => $request->user()
    ]);
});

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
}); 