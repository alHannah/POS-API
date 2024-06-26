<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\ScheduleGroup;
use App\Models\Store;
use App\Models\StorePerSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleGroupController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $storeNames  = $request->store_name;
            $storeIds    = [];
            $id          = $request->id;

            //Get store ids based on store names
            foreach ($storeNames as $storeName) {
                $store              = Store::where('store_name', $storeName)->first();
                if ($store) {
                    $storeIds[]     = $store->id;
                } else {
                    return response()->json([
                        'error'     => true,
                        'store'     => 'Store Not Found: ' . $storeName,
                    ]);
                }
            }

            //For update and create
            if ($request->has('id')) {
                // Update existing schedule group
                $scheduleGroup        = ScheduleGroup::find($id);
                if (!$scheduleGroup) {
                    return response()->json([
                        'error'       => true,
                        'message'     => 'Schedule Group not found.',
                    ]);
                }

                // Retrieve previous data for audit_trail
                $previousStoreNames   = $scheduleGroup->stores()->pluck('store_name')->toArray();
                $previousData         = ScheduleGroup::where('id', $id)->first();
                $previousName         = $previousData->name;

                //Update schedule group details
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
                //Delete existing store associations
                $scheduleGroup->schedule_groups_per_store()->delete();
            } else {
                // Create new schedule group
                $scheduleGroup = ScheduleGroup::create([
                    'name'            => $request->name,
                    'monday'          => $request->monday,
                    'tuesday'         => $request->tuesday,
                    'wednesday'       => $request->wednesday,
                    'thursday'        => $request->thursday,
                    'friday'          => $request->friday,
                    'saturday'        => $request->saturday,
                    'sunday'          => $request->sunday,
                ]);
            }
            // Create new store associations for the schedule group
            foreach ($storeIds as $storeId) {
                $storeSchedule       =  StorePerSchedule::create([
                    'schedule_id'    => $scheduleGroup->id,
                    'store_id'       => $storeId,
                ]);
            }

            // Prepare new schedule details for audit_trail
            $newSchedule = [
                " Mo({$request->monday})",
                "Tu({$request->tuesday})",
                "We({$request->wednesday})",
                "Th({$request->thursday})",
                "Fr({$request->friday})",
                "Sa({$request->saturday})",
                "Su({$request->sunday})"
            ];
            // Generate message for audit trail based on create or update
            if ($scheduleGroup->wasRecentlyCreated) {
                $message  = "New Schedule: '$request->name' (" .
                            implode(', ', $storeNames). ")" .
                            implode(' ', $newSchedule);
            } else {
                $message  = "Update Schedule: '$previousName' (" .
                            implode(', ', $previousStoreNames) . ")" .
                            " change into '$request->name' (" .
                            implode(', ', $storeNames) . ")";
            }
            //insert audit_trail to database
            $request['remarks']     = $message;
            $request['type']        = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $scheduleGroup,
                'store'             => $storeSchedule,
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
            // Find schedule group by ID
            $scheduleGroup          = ScheduleGroup::find($id);

            if (!$scheduleGroup) {
                return response()->json([
                    'error'         => true,
                    'message'       => 'Schedule Group not found.',
                ]);
            }
            // Get store names associated with the schedule group
            $storeNames             = $scheduleGroup->stores()->pluck('store_name')->toArray();

            // Delete all store associations for the schedule group
            $scheduleGroup->schedule_groups_per_store()->delete();

             // Retrieve schedule group name for audit_trail
            $scheduleGroupName      = $scheduleGroup->name;

             // Prepare deleted schedule details for audit
            $deletedSchedule = [
                " Mo({$scheduleGroup->monday})",
                "Tu({$scheduleGroup->tuesday})",
                "We({$scheduleGroup->wednesday})",
                "Th({$scheduleGroup->thursday})",
                "Fr({$scheduleGroup->friday})",
                "Sa({$scheduleGroup->saturday})",
                "Su({$scheduleGroup->sunday})"
            ];
            // Delete the schedule group record
            $scheduleGroup->delete();

            // Generate message for audit trail
            $message = "Deleted Schedule: '{$scheduleGroupName}' (" .
                       implode(', ', $storeNames) . ")" .
                       implode(' ', $deletedSchedule);

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
            $id = $request->id;

            // Retrieve schedule group data based on ID or latest if ID is not provided
            !$id ? $datas = ScheduleGroup::latest()->get()
                : $datas = ScheduleGroup::where('id',$id)->first();

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
