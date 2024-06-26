<?php

namespace App\Http\Controllers\v1\web\stores;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreGroup;
use App\Models\Area;
use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreGroupController extends Controller
{
        public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id             = $request->id;
            $groupName      = $request->group_name;
            $brandId        = $request->brand_id;

            if (!$groupName || !$brandId) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.required')
                ]);
            }

            $existingGroup = StoreGroup::where('group_name', $groupName)
                            ->where('brand_id', $brandId)
                            ->where('id', '!=', $id)
                            ->first();

            if ($existingGroup) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.store_group.existed'),
                ]);
            }

            $previousData = $id ? StoreGroup::with('brand_storeGroup')->find($id) : null;
            $previousStore = $previousData->group_name ?? 'N/A';
            $previousBrand = $previousData->brand_storeGroup->brand ?? 'N/A';

            $createUpdate = StoreGroup::updateOrCreate([
                'id'            => $id
            ], [
                'group_name'    => $groupName,
                'brand_id'      => $brandId
            ]);

            $brandName = Brand::find($brandId)->brand;

            $message = $id
                ? "Updated ID No. $id Previous: $previousStore (Brand: $previousBrand) New: $groupName (Brand: $brandName)"
                : "Created ID No. $id $groupName (Brand: $brandName)";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $createUpdate
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

            $brandId = $request->brand_id;

            !$brandId ? $getData = StoreGroup::latest()->get()
                      : $getData = StoreGroup::where('brand_id', $brandId)
                                ->latest()
                                ->get();

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

    // public function filter(Request $request) {
    //     try {
    //         DB::beginTransaction();

    //         $areaFilter         = $request->areaFilter;
    //         $brandFilter        = $request->brandFilter;
    //         $startDateFilter    = $request->startDateFilter;
    //         $endDateFilter      = $request->endDateFilter;

    //         $areaFilter = StoreGroup::

    //         return response()->json([
    //             'error'             => false,
    //             'message'           => trans('messages.success'),
    //             'areaData'          => $areaFilter,
    //             'brandData'         => $brandFilter,
    //             'startDateData'     => $startDateFilter,
    //             'endDateData'       => $endDateFilter
    //             // 'type'      => $type
    //         ]);

    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::info("Error: $e");
    //         return response()->json([
    //             'error'     => true,
    //             'message'   => trans('messages.error'),
    //         ]);
    //     }
    // }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;

            $storeGroup = StoreGroup::where('id', $id)
                            ->delete();

            $message = "Deleted ID No. $id Successfully!";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

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
