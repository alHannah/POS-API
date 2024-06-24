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

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $createUpdate,
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

            $delete = ScheduleGroup::where("id", $request->id)->delete();

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $delete,
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

            $datas = ScheduleGroup::where('id',$request->id)->latest()->get();

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
