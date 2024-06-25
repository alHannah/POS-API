<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\ScheduleGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleGroupController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;

            if ($id) {
                $previousData       = ScheduleGroup::where('id', $id)->first();
                $previousName       = $previousData->name;
                $previousMonday     = $previousData->monday;
                $previousTuesday    = $previousData->tuesday;
                $previousWednesday  = $previousData->wednesday;
                $previousThursday   = $previousData->thursday;
                $previousFriday     = $previousData->friday;
                $previousSaturday   = $previousData->saturday;
                $previousSunday     = $previousData->sunday;

                $previousSchedule = [
                    "Mo({$previousMonday})",
                    "Tu({$previousTuesday})",
                    "We({$previousWednesday})",
                    "Th({$previousThursday})",
                    "Fr({$previousFriday})",
                    "Sa({$previousSaturday})",
                    "Su({$previousSunday})"
                ];
            }

            $createUpdate = ScheduleGroup::updateOrCreate([
                'id' => $id
            ], [
                'name'          => $request->name,
                'monday'        => $request->monday,
                'tuesday'       => $request->tuesday,
                'wednesday'     => $request->wednesday,
                'thursday'      => $request->thursday,
                'friday'        => $request->friday,
                'saturday'      => $request->saturday,
                'sunday'        => $request->sunday,
            ]);

            $newSchedule = [
                "Mo({$request->monday})",
                "Tu({$request->tuesday})",
                "We({$request->wednesday})",
                "Th({$request->thursday})",
                "Fr({$request->friday})",
                "Sa({$request->saturday})",
                "Su({$request->sunday})"
            ];

            if ($createUpdate->wasRecentlyCreated) {
                $message     = "New Schedule: '$request->name' " . implode(' ', $newSchedule);
            } else {
                $message    = "Update Schedule: '$previousName' " . implode(' ', $previousSchedule) . " change into '$request->name' " . implode(' ', $newSchedule);
            }

            $request['remarks']  = $message;
            $request['type']     = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $createUpdate,
                //'audit_trail'   => $message
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

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;

            if ($id) {
                $datas      = ScheduleGroup::where('id', $id)->first();
                $name       = $datas->name;
                $monday     = $datas->monday;
                $tuesday    = $datas->tuesday;
                $wednesday  = $datas->wednesday;
                $thursday   = $datas->thursday;
                $friday     = $datas->friday;
                $saturday   = $datas->saturday;
                $sunday     = $datas->sunday;

                $schedule = [
                    "Mo({$monday})",
                    "Tu({$tuesday})",
                    "We({$wednesday})",
                    "Th({$thursday})",
                    "Fr({$friday})",
                    "Sa({$saturday})",
                    "Su({$sunday})"
                ];
            }

            $delete = ScheduleGroup::where("id", $id)->delete();

            if ($delete){
                $message    = "Schedule Deleted: '$name' " . implode(' ', $schedule);
            }

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $delete,
                //'audith_trail'  =>$message
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

    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            if($request->id){
                $datas = ScheduleGroup::where('id',$request->id)->latest()->get();
            } else {
                $datas = ScheduleGroup::latest()->get();
            }

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
