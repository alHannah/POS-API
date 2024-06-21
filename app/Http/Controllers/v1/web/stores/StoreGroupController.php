<?php

namespace App\Http\Controllers\v1\web\stores;

use App\Http\Controllers\Controller;
use App\Models\StoreGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreGroupController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $store_group = StoreGroup::updateOrCreate([
                    'id' => $request->id
            ], [
                    'group_name' => $request->group_name,
                    'brand_id' => $request->group_id
            ]);

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $store_group
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

            $store_group = StoreGroup::where('id', $request->id)->delete();

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $store_group
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
