<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class KeycloakAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized - No token provided'], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get(config('keycloak.url') . '/realms/' . config('keycloak.realm') . '/protocol/openid-connect/userinfo');

            if ($response->successful()) {
                $userInfo = $response->json();
                $request->merge(['user' => $userInfo]);
                return $next($request);
            }

            return response()->json(['error' => 'Unauthorized - Invalid token'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized - Token validation failed'], 401);
        }
    }
} 