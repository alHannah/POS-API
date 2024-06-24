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

            $id = $request->id;

            if ($id) {
                $previousData = GeneralTimeSetting::find($id);

                if ($previousData) {
                    $previousStart  = $previousData->start_time;
                    $previousEnd    = $previousData->end_time;
                }
            }

            $store_hours = GeneralTimeSetting::updateOrCreate(
                ['id' => $request->id],
                [
                    'start_time' => $request->start_time,
                    'end_time'   => $request->end_time
                ]
            );

            if ($store_hours->wasRecentlyCreated || !$id) {
                $message = "New Time: {$request->start_time}";
            } else {
                $message = "Update Time: $previousStart ($previousEnd) change into {$request->start_time} ({$store_hours->end_time})";
            }

            DB::commit();

            return response()  -> json([
                'error'        => false,
                'message'      => trans('messages.success'),
                'data'         => $store_hours,
                'audit_trail'  => $message
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response() -> json([
                'error'       => true,
                'message'     => trans('messages.error'),
            ]);
        }
    }


    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;
            $datas = GeneralTimeSetting::find($id)->latest()->get();

            DB::commit();

            return response() -> son([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $datas,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response() -> json([
                'error'       => true,
                'message'     => trans('messages.error'),
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $store_hours = GeneralTimeSetting::where('id', $request->id)->delete();

            DB::commit();

            return response() -> json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $store_hours
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response() -> json([
                'error'       => true,
                'message'     => trans('messages.error'),
            ]);
        }
    }
}
