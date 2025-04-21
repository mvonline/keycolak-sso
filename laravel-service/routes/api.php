<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProtectedController;

Route::middleware('keycloak')->group(function () {
    Route::get('/protected', [ProtectedController::class, 'index']);
});

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
}); 