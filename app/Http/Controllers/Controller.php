<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\QrCodes;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function generateAuthToken($user)
    {
        $payload = [
            'iss' => "Intel Hunter", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'user' => [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            // 'tag' => $tag, // signup_verification/guest/user
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60 * 60 // Expiration time
        ];
        $secret = env('AUTH_SECRET');
        return JWT::encode($payload, $secret, 'HS256');
    }

}