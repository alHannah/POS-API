<?php

namespace App\Http\Controllers\v1\web\accounts;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OIC extends Controller
{
    public function create_update(Request $request) {
        try {
            DB::beginTransaction();

            $data = "sample";

            // $message = $decryptedId
            //         ? "Update Previous: $previousName New: $name"
            //         : "Created $name";

            $message = "message";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $data,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'     => true,
                'message'   => trans('messages.error'),
            ]);
        }
    }

    public function get(Request $request) {
        try {
            DB::beginTransaction();

            $data = "sample";

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $data,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'     => true,
                'message'   => trans('messages.error'),
            ]);
        }
    }

    public function archive_activate(Request $request) {
        try {
            DB::beginTransaction();

            $data = "sample";


            // $thisData->status == 0 ? $message = "Archived an category: $name." : $message = "Activated an category: $name.";

            $message = "message";

            $request['remarks']     = $message;
            $request['type']        = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $data,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'     => true,
                'message'   => trans('messages.error'),
            ]);
        }
    }
}
