<?php

namespace App\Http\Controllers\v1\web\stores;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreGroup;
use App\Models\Area;
use App\Models\Users;
use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreGroupController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();
            $encryptedId    = $request->id ? Crypt::decrypt($request->id) : null;
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
                            ->where('id', '!=', $encryptedId)
                            ->first();

            if ($existingGroup) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.store_group.existed'),
                ]);
            }

            $previousData       = Crypt::encrypt($encryptedId) ? StoreGroup::with('brand_storeGroup')->find($encryptedId) : null;
            $previousStore      = $previousData->group_name ?? 'N/A';
            $previousBrand      = $previousData->brand_storeGroup->brand ?? 'N/A';

            $createUpdate = StoreGroup::updateOrCreate([
                'id'            => $encryptedId
            ], [
                'group_name'    => $groupName,
                'brand_id'      => $brandId
            ]);

            $brandName = Brand::find($brandId)->brand;

            $message = $encryptedId
                    ? "Updated Previous: $previousStore (Brand: $previousBrand) New: $groupName (Brand: $brandName)"
                    : "Created $groupName (Brand: $brandName)";

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

            $areaFilter         = (array) $request->areaFilter;
            $brandFilter        = (array) $request->brandFilter;

            // Flatten the filters in case they are nested
            $areaFilter         = Arr::flatten($areaFilter, 1);
            $brandFilter        = Arr::flatten($brandFilter, 1);

            $thisData = Store::with(['store_brands', 'group_per_store']);
            if (!empty($brandFilter)) {
                $thisData->whereIn('brand_id', $brandFilter);
            }

            if (!empty($areaFilter)) {
                $thisData->whereIn('area_id', $areaFilter);
            }

            $getData = $thisData->get();

            $generateData = $getData->map(function ($items) {
                $name       = $items->group_per_store->group_name ?? 'N/A';
                $brand      = $items->store_brands->brand ?? 'N/A';

                return [
                    'store_name'     => $name,
                    'brand'         => $brand,
                ];
            });

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $generateData,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}");
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

            $encryptedId = $request->id ? Crypt::decrypt($request->id) : null;

            $storeGroup  = StoreGroup::where('id', $encryptedId)
                            ->delete();

            $message = "Deleted Successfully!";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $storeGroup,
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

    // public function archive(Request $request) {
    //     try {
    //         DB:: beginTransaction();

    //         $userId = Crypt::decrypt($request->encryptedId);

    //         $name = Users::where('id', $userId)->first()->name;

    //         Users::where('id', $userId)->update([
    //             'status' => 0
    //         ]);

    //         // AUDIT TRAIL LOG
    //         // $request['remarks'] = "Archived an user: $name.";
    //         $request['activity'] = "Archived an user: $name.";
    //         $this->audit_trail($request);

    //         DB::commit();

    //         return response()->json([
    //             "error" => false,
    //             'message' =>trans('messages.success'),
    //         ]);
    //     } catch (Exception $e) {
    //         Log::info("Error $e");
    //         return response()->json([
    //             "error"=> true,
    //             "message"=> trans('messages.error'),
    //         ]);
    //     }
    // }
}
