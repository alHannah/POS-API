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
            $decryptedId    = !empty($request->id) ? Crypt::decrypt($request->id) : null;
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
                            ->where('id', '!=', $decryptedId)
                            ->first();

            if ($existingGroup) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.store_group.existed'),
                ]);
            }

            $previousData       = Crypt::encrypt($decryptedId)
                                ? StoreGroup::with('storeGroup_brand')->find($decryptedId)
                                : null;
            $previousStore      = $previousData->group_name ?? 'N/A';
            $previousBrand      = $previousData->storeGroup_brand->brand ?? 'N/A';

            $createUpdate = StoreGroup::updateOrCreate([
                'id'            => $decryptedId
            ], [
                'group_name'    => $groupName,
                'brand_id'      => $brandId
            ]);

            $brandName = Brand::find($brandId)->brand;

            $message = $decryptedId
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

            $thisData = StoreGroup::withCount(['storeGroup_brand', 'storeGroup_store as store_count']);
            if (!empty($brandFilter)) {
                $thisData->whereIn('brand_id', $brandFilter);
            }

            if (!empty($areaFilter)) {
                $thisData->whereIn('area_id', $areaFilter);
            }

            $getData = $thisData->latest()->get();

            $generateData = $getData->map(function ($item) {
                $id                    = Crypt::encrypt($item->id);
                $created_at            = $item->created_at;
                return [
                    'store_count'      => $item->store_count                        ?? 'N/A',
                    'id'               => $id                                       ?? 'N/A',
                    'store_name'       => $item->group_name                         ?? 'N/A',
                    'brand'            => $item->storeGroup_brand->brand            ?? 'N/A',
                    'created_at'       => $created_at->format("M d, Y h:i A")       ?? 'N/A',
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
            // dd(Crypt::encrypt(40));

            $decryptedId = Crypt::decrypt($request->id);

            $storeGroup = StoreGroup::find($decryptedId);

            if (!$storeGroup) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Store Group not found.',
                ]);
            }

            $storeData = Store::with('store_storeGroup')->get();

            foreach ($storeData as $item) {
                if ($item->group_id == $storeGroup->id) {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Deletion failed, This Store Group is used in Stores Table.',
                    ]);
                }
            }

            // Proceed with the deletion if no match is found
            $storeGroup->delete();


            $message = "Deleted Store Group: $storeGroup->group_name";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);
            // dd(DB::getQueryLog());

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $storeGroup,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            // dd(DB::getQueryLog());
            Log::info("Error: $e");
            return response()->json([
                'error'     => true,
                'message'   => trans('messages.error'),
            ]);
        }
    }
}
