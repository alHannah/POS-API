<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Models\Area;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AreaController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;

            if ($id) {

                $previousData  = Area::with('brand_areas')->where('id', $id)->first();
                $previousArea  = $previousData->name;
                $previousBrand = $previousData->brand_areas->brand;
            }

            // --------------------------updateOrCreate-----------------------------------------
            $area = Area::updateOrCreate([
                'id'        => $id
            ], [
                'name'      => $request->name,
                'brand_id'  => $request->brand_id
            ]);

            $brandName = Brand::where('id', $request->brand_id)->first()->brand;

            if ($area->wasRecentlyCreated) {
                $message    = "Create Area: $request->name";
            } else {
                $message    = "Update Area: $previousArea ($previousBrand) change into $request->name ($brandName)";
            }

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $area,
                'audit_trail' => $message
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

    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            $datas = Area::whereIn('brand_id', $request->brand_id)->where('status', 1)->latest()->get();

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $datas,
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

            $storeGroup = Area::where('id', $request->id)->delete();

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $storeGroup
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
