<?php

namespace App\Http\Controllers\v1\web\stores;

use App\Http\Controllers\Controller;
use App\Models\GeneralTimeSetting;
use App\Models\Store;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreHoursController extends Controller
{
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;
            $store_id = $request->store_id;

            if (!$store_id) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $store = Store::find($store_id);
            if (!$store) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.store.store_hours.notFound')
                ]);
            }

            $store_name = $store->store_name;

            $existingStore = GeneralTimeSetting::where('store_id', $store_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existingStore) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.store.store_hours.existed'),
                ]);
            }

            $store_hours = GeneralTimeSetting::create([
                'id'         => $id,
                'store_id'   => $request->store_id,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time
            ]);

            $message = "{$store_name} => New Time: Opening: {$request->start_time} & Closing: {$request->end_time}";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $store_hours,
                'audit_trail' => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: $e");
            return response()->json([
                'error'      => true,
                'message'    => trans('messages.error'),
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            //$store_id = $request -> store_id;
            $encryptedId = $request->store_id ? Crypt::decrypt($request->store_id) : null;

            if (!$encryptedId) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $previousData = GeneralTimeSetting::where('store_id', $encryptedId)->first();

            if (!$previousData) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.store.store_hours.notFound')
                ]);
            }

            if ($previousData->start_time == $request->start_time && $previousData->end_time == $request->end_time) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.store.store_hours.alreadyExist')
                ]);
            }

            $store = Store::find($encryptedId);
            $store_name = $store->store_name;

            $previousStart = $previousData->start_time;
            $previousEnd   = $previousData->end_time;

            $previousData->update([
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
            ]);

            $start = $request->start_time;
            $end   = $request->end_time;

            $message = "{$store_name} => Update Time: $previousStart (Opening) & $previousEnd (Closing) change into {$start} (Opening) & {$end} (Closing)";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $previousData,
                'audit_trail' => $message,
                //'store_id'    => $store_id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    public function searchStoreHours(Request $request)
    {
        try {
            //$store_id = $request -> store_id;
            $encryptedId = $request->store_id ? Crypt::decrypt($request->store_id) : null;

            $query = GeneralTimeSetting::with(['general_time_store' => function ($query) {
                $query->select('id', 'store_name');
            }])
                ->orderByDesc('created_at')
                ->select(['store_id', 'start_time', 'end_time', 'created_at']);

            if ($encryptedId) {
                $query->where('store_id', $encryptedId);
            }

            $data = $query->get()->map(function ($item) {
                return [
                    'store_id'   => Crypt::encrypt($item->store_id),
                    'store_name' => $item->general_time_store->store_name ?? null,
                    'start_time' => $item->start_time,
                    'end_time'   => $item->end_time,
                    'created_at' => $item->created_at->format('M d, Y h:i A'),
                ];
            });

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'data'    => $data,
                //'store_id'    => $store_id
            ]);
        } catch (Exception $e) {
            Log::info("Error:$e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }


    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            //dd(Crypt::encrypt(152));
            //$store_id = $request -> store_id;
            $encryptedId = $request->store_id ? Crypt::decrypt($request->store_id) : null;

            if (!$encryptedId) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $previousData = GeneralTimeSetting::where('store_id', $encryptedId)->first();

            if (!$previousData) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.store.store_hours.notFound')
                ]);
            }

            $storeHoursDeleted = GeneralTimeSetting::where('store_id', $encryptedId)->delete();

            $store = Store::find($encryptedId);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.store.notFound')
                ]);
            }

            $storeName = $store->store_name;
            $message = "Deleted {$storeName} (store hours)";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $storeHoursDeleted,
                'audit_trail' => $message,
                //'store_id'    => $store_id
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error:$e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }


    public function displayStoreHours(Request $request)
    {
        try {
            DB::beginTransaction();

            $start_time = $request->start_time;
            $end_time = $request->end_time;

            $query = GeneralTimeSetting::with(['general_time_store' => function ($query) {
                $query->select('id', 'store_name');
            }]);

            if ($start_time && $end_time) {
                $query->whereTime('start_time', '>=', $start_time)
                      ->whereTime('end_time', '<=', $end_time);
            }

            $data = $query->orderByDesc('created_at')->get(['store_id', 'start_time', 'end_time', 'created_at']);

            $data = $data->map(function ($item) {
                return [
                    'store_id'   => Crypt::encryptString($item->store_id),
                    'store_name' => $item->general_time_store ? $item->general_time_store->store_name : null,
                    'start_time' => $item->start_time,
                    'end_time'   => $item->end_time,
                    'created_at' => $item->created_at->format('M d, Y h:i A'),
                    'store_id_nE'=> $item->store_id,
                ];
            });

            DB::commit();

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error:$e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

}
