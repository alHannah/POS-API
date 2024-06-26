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
        // try {
        //     DB::beginTransaction();

        //     $id = $request->id;

        //     if ($id) {
        //         $previousData = GeneralTimeSetting::find($id);

        //         if ($previousData) {
        //             $previousStart  = $previousData->start_time;
        //             $previousEnd    = $previousData->end_time;
        //         }
        //     }

        //     $store_hours = GeneralTimeSetting::updateOrCreate(
        //         ['id' => $id],
        //         [
        //             'id'         => $id,
        //             'start_time' => $request->start_time,
        //             'end_time'   => $request->end_time
        //         ]
        //     );

        //     if ($store_hours->wasRecentlyCreated || !$id) {
        //         $message = "New Time: Opening: {$request->start_time} & Closing: {$request->end_time}";
        //     } else {
        //         $message = "Update Time: $previousStart(Opening) & $previousEnd(Closing) change into
        //         {$request->start_time}(Opening) & {$store_hours->end_time}(Closing)";
        //     }

        //     $request['remarks'] = $message;
        //     $request['type']    = 2;
        //     $this->audit_trail($request);

        //     DB::commit();

        //     return response()  -> json([
        //         'error'        => false,
        //         'message'      => trans('messages.success'),
        //         'data'         => $store_hours,
        //         'audit_trail'  => $message
        //     ]);

        // } catch (Exception $e) {
        //     DB::rollBack();
        //     Log::info("Error: $e");
        //     return response() -> json([
        //         'error'       => true,
        //         'message'     => trans('messages.error'),
        //     ]);
        // }

        try {
            DB::beginTransaction();

            $id = 1;

            $previousData = GeneralTimeSetting::find($id);

            if ($previousData) {
                $existingData = GeneralTimeSetting::where('start_time', $request->start_time)
                                                  ->where('end_time', $request->end_time)
                                                  ->first();

                if ($existingData) {
                    DB::rollBack();
                    return response() -> json([
                        'error'       => true,
                        'message'     => trans('messages.store.store_hours.alreadyExist'),
                    ]);
                }

                $previousStart = $previousData->start_time;
                $previousEnd   = $previousData->end_time;

                $previousData    -> update([
                    'start_time' => $request->start_time,
                    'end_time'   => $request->end_time,
                ]);

                $start = $request->start_time;
                $end = $request->end_time;

                $message = "Update Time: $previousStart (Opening) & $previousEnd (Closing) change into {$start} (Opening) & {$end} (Closing)";

                $request['remarks'] = $message;
                $request['type'] = 2;
                $this->audit_trail($request);

                DB::commit();

                return response() -> json([
                    'error'       => false,
                    'message'     => trans('messages.success'),
                    'data'        => $previousData,
                    'audit_trail' => $message,
                ]);
            } else {
                DB::rollBack();
                return response() -> json([
                    'error'       => true,
                    'message'     => trans('messages.store.store_hours.notFound'),
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error' => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    // public function get(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $id = $request->id;

    //         if ($id) {
    //             $data = GeneralTimeSetting::find($id);
    //             $datas = [$data];
    //         } else {
    //             $datas = GeneralTimeSetting::latest()->get();
    //         }
    //         DB::commit();

    //         return response() -> json([
    //             'error'       => false,
    //             'message'     => trans('messages.success'),
    //             'data'        => $datas,
    //         ]);

    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::info("Error: $e");
    //         return response() -> json([
    //             'error'       => true,
    //             'message'     => trans('messages.error'),
    //         ]);
    //     }
    // }

    // public function delete(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $store_hours = GeneralTimeSetting::where('id', $request->id)->delete();

    //         $message = "Successfully Deleted ID #: {$request->id}";

    //         $request['remarks'] = $message;
    //         $request['type']    = 2;
    //         $this->audit_trail($request);

    //         DB::commit();

    //         return response() -> json([
    //             'error'       => false,
    //             'message'     => trans('messages.success'),
    //             'data'        => $store_hours
    //         ]);

    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::info("Error: $e");
    //         return response() -> json([
    //             'error'       => true,
    //             'message'     => trans('messages.error'),
    //         ]);
    //     }
    // }
}
