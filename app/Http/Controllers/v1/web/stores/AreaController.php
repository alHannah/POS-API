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

            $id         = $request->id;
            $name       = $request->name;
            $brandId    = $request->brand_id;

            if (!$name || !$brandId) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.required')
                ]);
            }

            $existingGroup = Area::where('name', $name)
                            ->where('id', '!=', $id)
                            ->first();

            if ($existingGroup) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.area.existed'),
                ]);
            }

            $previousData       = $id ? Area::with('brand_areas')->where('id', $id)->first() : null;
            $previousArea       = $previousData->name ?? 'N/A';
            $previousBrand      = $previousData->brand_areas->brand ?? 'N/A';

            // --------------------------updateOrCreate-----------------------------------------

            $createUpdate = Area::updateOrCreate([
                'id'        => $id
            ], [
                'name'      => $name,
                'brand_id'  => $brandId
            ]);

            $brandName = Brand::find($brandId)->brand;

            $message = $id
                    ? "Updated ID No. $id Previous: $previousArea (Brand: $previousBrand) New: $name (Brand: $brandName)"
                    : "Created ID No. $id $name (Brand: $brandName)";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $createUpdate,
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

            $brandIds = $request->input('brand_id', []);

            if (is_array($brandIds) && !empty($brandIds)) {
                $getData = Area::whereIn('brand_id', $brandIds)
                            ->where('status', 1)
                            ->latest()
                            ->get();
            } else {
                $getData = Area::where('status', 1)
                            ->latest()
                            ->get();
            }

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $getData,
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

            $id = $request->id;

            $area = Area::where('id', $id)
                  -> delete();

            $message = "Deleted ID No. $id Successfully!";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $area
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
