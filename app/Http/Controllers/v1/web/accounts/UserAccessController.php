<?php

namespace App\Http\Controllers\v1\web\accounts;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\UserAccess;

class UserAccessController extends Controller
{
    public function create(Request $request){
        dd("Hello World Create");
    }

    public function update(Request $request){
        dd("Hello World Update");
    }

    public function get_update(Request $request){
        try {
            DB::beginTransaction();
            $role = $request->role_id;
            $userAccessData = UserAccess::with([
                "user_access_module",
                "user_access_role"
            ])->where;

            dd($userAccessData[0]);

            DB::commit();

            return response()->json([
                "error"             => false,
                "message"           => trans('messages.success'),
                // "data"              => $tableData,
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"             => true,
                "message"           => trans("messages.error"),
            ]);
        }
    }


    public function get(Request $request){
        dd("Hello World Get");
    }
}
