<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\UnexpectedValueException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// use App\Models\MobileUsers as User;
use App\Models\{
    AccessToken,
    Users,
};

class Authentication
{   
    public function handle($request, Closure $next, $guard = null)
    {

        $isJson = $request->headers->get('Content-Type') == "json" ? true : false;
        $authorizationHeader = $request->headers->get('Authorization');
            
        $token = null;
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7); // Remove 'Bearer ' prefix
        }

        $path = $request->getPathInfo();
        $ua = $request->server('HTTP_USER_AGENT');
        $route = $request->route();
        $isLogout = ($path == '/api/v1/account/logout') ? true : false;

        if(!$isLogout) {

            if(!$this->validateToken($token)){
                return response()->json([
                    'error' => true,
                    'msg' => trans('messages.unauthorized'), 
                    'error_details' => [
                        'description' => "Not authorized",
                        'action' => 'request_signin'
                    ],
                    "modal_title" => trans("alerts_title.unauthorized")
                ], 401);
            }
            
            $credentials = JWT::decode($token, new Key(env('AUTH_SECRET'), 'HS256'));
            // $user = $CRUD_REPO->find($USERS, "id = " . $credentials->sub);
            $user = Users::where("id", $credentials->sub)->first();
            if(!$user){
                return response()->json([
                    'error' => true,
                    'msg' => trans('messages.unauthorized'), 
                    'error_details' => [
                        'description' => "Not authorized",
                        'action' => 'request_signin'
                    ],
                    "modal_title" => trans("alerts_title.unauthorized")
                ], 401);
            }
            $credentials = JWT::decode($token, new Key(env('AUTH_SECRET'), 'HS256'));
            // $user = $CRUD_REPO->find($USERS, "id = " . $credentials->sub);
            $user = Users::where("id", $credentials->sub)->first();
            if(!$user){
                return response()->json([
                    'error' => true,
                    'msg' => trans('messages.unauthorized'), 
                    'error_details' => [
                        'description' => "Not authorized",
                        'action' => 'request_signin'
                    ],
                    "modal_title" => trans("alerts_title.unauthorized")
                ], 401);
            }

            /** OTHER VERIFICATION IF ANY */
            // CHECK IF TOKEN EXISTS
            // $checkToken = $CRUD_REPO->find($ACCESS_TOKENS, "token = '$token'");
            $checkToken = AccessToken::where("token", $token);
            if(!$checkToken){
                return response()->json([
                    'error' => true,
                    'msg' => trans('messages.unauthorized'),  
                    'error_details' => [
                        'description' => "Not authorized",
                        'action' => 'request_signin'
                    ],
                    "modal_title" => trans("alerts_title.unauthorized")
                ], 401);
            }
            /** OTHER VERIFICATION IF ANY */
            // CHECK IF TOKEN EXISTS
            // $checkToken = $CRUD_REPO->find($ACCESS_TOKENS, "token = '$token'");
            $checkToken = AccessToken::where("token", $token);
            if(!$checkToken){
                return response()->json([
                    'error' => true,
                    'msg' => trans('messages.unauthorized'),  
                    'error_details' => [
                        'description' => "Not authorized",
                        'action' => 'request_signin'
                    ],
                    "modal_title" => trans("alerts_title.unauthorized")
                ], 401);
            }

            // CHECK USER STATUS
            if($user->status != '1') {
                return response()->json([
                    'error' => true,
                    'msg' => trans('messages.unauthorized'),  
                    'error_details' => [
                        'description' => "Not authorized",
                        'action' => 'request_signin',
                        'user' => $user
                    ],
                    "modal_title" => trans("alerts_title.unauthorized")
                ], 401);
            }

            $request->auth = $user;
        }
        // s
        return $next($request);
    }
    private function validateToken($token){
        if(!$token) {
            return false;
        }
        try {
            $credentials = JWT::decode($token, new Key(env('AUTH_SECRET'), 'HS256'));
            return true;
        } catch(\UnexpectedValueException $e){
            return false;
        }catch(SignatureInvalidException $e){
            return false;
        }catch(BeforeValidException $e){
            return false;
        }catch(ExpiredException $e) {
            return false;
        }
    }
}