<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\ScheduleGroup;
use App\Models\Store;
use App\Models\StorePerSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleGroupController extends Controller
{
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $createSchedule = ScheduleGroup::create([
                'name'            => $request->name,
                'monday'          => $request->monday,
                'tuesday'         => $request->tuesday,
                'wednesday'       => $request->wednesday,
                'thursday'        => $request->thursday,
                'friday'          => $request->friday,
                'saturday'        => $request->saturday,
                'sunday'          => $request->sunday,
            ]);

            $storeIds = $request->store_id;

            foreach ($storeIds as $storeId) {
                StorePerSchedule::create([
                    'schedule_id' => $createSchedule->id,
                    'store_id'    => $storeId,
                ]);
            }
            $storeNames =  $createSchedule->stores()->pluck('store_name')->toArray();
            DB::commit();

            if ($createSchedule->wasRecentlyCreated) {
                $message    = "Created Schedule: '$request->name' (" . implode(', ', $storeNames) . ")";
            }

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $createSchedule,
                //'store'             => $storeSchedule,
                //'audit_trail'       => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;
            $storeIds = $request->store_id;

            $scheduleGroup        = ScheduleGroup::find($id);
            if (!$scheduleGroup) {
                return response()->json([
                    'error'       => true,
                    'message'     => trans('messages.store.schedule_group.notexist')
                ]);
            }

            $previousStoreNames   = $scheduleGroup->stores()->pluck('store_name')->toArray();
            $previousData         = ScheduleGroup::where('id', $id)->first();
            $previousName         = $previousData->name;

            $scheduleGroup->update([
                'name'            => $request->name,
                'monday'          => $request->monday,
                'tuesday'         => $request->tuesday,
                'wednesday'       => $request->wednesday,
                'thursday'        => $request->thursday,
                'friday'          => $request->friday,
                'saturday'        => $request->saturday,
                'sunday'          => $request->sunday,
            ]);
            $scheduleGroup->schedule_groups_per_store()->delete();

            foreach ($storeIds as $storeId) {
                StorePerSchedule::create([
                    'schedule_id' => $id,
                    'store_id' => $storeId
                ]);
            }
            $newStoreName   = $scheduleGroup->stores()->pluck('store_name')->toArray();
            DB::commit();

            $message    = "Update Schedule: '$previousName' (" . implode(', ', $previousStoreNames) .
                ") change into '$request->name' " . "(" . implode(', ', $newStoreName);

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $scheduleGroup,
                //'store'             => $newStoreName,
                //'audit_trail'       => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;

            $scheduleGroup          = ScheduleGroup::find($id);
            if (!$scheduleGroup) {
                return response()->json([
                    'error'         => true,
                    'message'       => 'Schedule Group not found.',
                ]);
            }
            $storeNames             = $scheduleGroup->stores()->pluck('store_name')->toArray();
            $scheduleGroupName      = $scheduleGroup->name;

            $scheduleGroup->schedule_groups_per_store()->delete();
            $scheduleGroup->delete();

            $message = "Deleted Schedule: '{$scheduleGroupName}' (" .
                implode(', ', $storeNames) . ")";

            $request['remarks']     = $message;
            $request['type']        = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $scheduleGroup,
                //'audith_trail'      => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            //---------------------Ayusin pa---------------
            $id = $request->id;

            $datas = ScheduleGroup::withCount('schedule_groups_per_store')->get();

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $datas,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'         => true,
                'message'       => trans('messages.error'),
            ]);
        }
    }
}
