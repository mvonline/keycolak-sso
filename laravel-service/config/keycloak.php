<?php

return [
    'base_url' => env('KEYCLOAK_URL', 'http://localhost:8080'),
    'realm' => env('KEYCLOAK_REALM', 'master'),
    'client_id' => env('KEYCLOAK_CLIENT_ID'),
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
    'redirect_url' => env('KEYCLOAK_REDIRECT_URL', '/auth/callback'),
    'logout_url' => env('KEYCLOAK_LOGOUT_URL', '/auth/logout'),
    'token_endpoint' => '/realms/{realm}/protocol/openid-connect/token',
    'userinfo_endpoint' => '/realms/{realm}/protocol/openid-connect/userinfo',
    'authorization_endpoint' => '/realms/{realm}/protocol/openid-connect/auth',
    'end_session_endpoint' => '/realms/{realm}/protocol/openid-connect/logout',
    'jwks_uri' => '/realms/{realm}/protocol/openid-connect/certs',
    
    'scopes' => [
        'openid',
        'profile',
        'email',
        'roles',
    ],
    
    'cache' => [
        'enabled' => true,
        'ttl' => 300, // 5 minutes
    ],
]; 