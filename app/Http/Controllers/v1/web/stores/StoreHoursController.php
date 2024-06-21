<?php

namespace App\Http\Controllers\v1\web\stores;

use App\Http\Controllers\Controller;
use App\Models\GeneralTimeSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreHoursController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $store_hours = GeneralTimeSetting::updateOrCreate([
                'id' => $request->id
            ], [
                'start_time'    => $request->start_time,
                'end_time'      => $request->end_time
            ]);


            // if ($area->wasRecentlyCreated) {
            //     $type = 'Yes it is Recently Created!';
            // } else {
            //     $type = 'It is updated!';
            // }

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $store_hours
                // 'type'      => $type
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

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $store_hours = GeneralTimeSetting::where('id', $request->id)->delete();

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $store_hours
                // 'type'      => $type
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
