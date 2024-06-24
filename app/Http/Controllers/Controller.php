<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\QrCodes;
use App\Models\AuditTrail;
use Exception;
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
            'iss' => "NexPOS", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'user' => [
                'name'     => $user->name,
                'email'    => $user->email,
                'role_id'  => $user->role_id,
            ],
            // 'tag' => $tag, // signup_verification/guest/user
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60 * 60 // Expiration time
        ];

        $secret = env('AUTH_SECRET');
        return JWT::encode($payload, $secret, 'HS256');
    }

    public function audit_trail(Request $request) 
    {
        try {
            DB::beginTransaction();

            $currentlyAuthId = $request->auth->id;

            AuditTrail::create([
                'user_id'   => $currentlyAuthId,
                'remarks'   => $request->remarks,
                'user_type' => $request->type,
                'created_at'=> Carbon::now()
            ]);

            DB::commit();
            return response()->json([
                'error' => false,
                'msg'   => trans('messages.success')
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error'         => true,
                'msg'           => trans("messages.error"),
                'modal_title'   => trans("alerts_title.oops")
            ]);
        }
    }

}