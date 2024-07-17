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
use Illuminate\Support\Facades\Validator;

class StoreGroupController extends Controller
{
    public function create_update(Request $request)
    {
        $this->validateRequest($request);

        try {
            DB::beginTransaction();
            $decryptedId = $this->decryptId($request->id);
            $groupName = $request->group_name;
            $brandId = $request->brand_id;

            if ($this->groupExists($groupName, $brandId, $decryptedId)) {
                return $this->jsonError(trans('messages.store.store_group.existed'));
            }

            $previousData = $decryptedId ? StoreGroup::with('storeGroup_brand')->find($decryptedId) : null;
            $previousStore = $previousData->group_name ?? 'N/A';
            $previousBrand = $previousData->storeGroup_brand->brand ?? 'N/A';

            $createUpdate = StoreGroup::updateOrCreate([
                'id' => $decryptedId
            ], [
                'group_name' => $groupName,
                'brand_id' => $brandId
            ]);

            $brandName = Brand::find($brandId)->brand;
            $message = $decryptedId
                ? "Updated Previous: $previousStore (Brand: $previousBrand) New: $groupName (Brand: $brandName)"
                : "Created $groupName (Brand: $brandName)";

            $this->auditTrail($request, $message);
            DB::commit();

            return $this->jsonSuccess($createUpdate);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}");
            return $this->jsonError(trans('messages.error'));
        }
    }

    public function get(Request $request)
    {
        try {
            $areaFilter = Arr::flatten((array) $request->areaFilter);
            $brandFilter = Arr::flatten((array) $request->brandFilter);

            $thisData = StoreGroup::withCount(['storeGroup_brand', 'storeGroup_store as store_count']);
            if (!empty($brandFilter)) {
                $thisData->whereIn('brand_id', $brandFilter);
            }

            if (!empty($areaFilter)) {
                $thisData->whereIn('area_id', $areaFilter);
            }

            $getData = $thisData->latest()->get();

            $generateData = $getData->map(function ($item) {
                return [
                    'store_count' => $item->store_count ?? 'N/A',
                    'id' => Crypt::encrypt($item->id) ?? 'N/A',
                    'store_name' => $item->group_name ?? 'N/A',
                    'brand' => $item->storeGroup_brand->brand ?? 'N/A',
                    'created_at' => $item->created_at->format("M d, Y h:i A") ?? 'N/A',
                ];
            });

            return $this->jsonSuccess($generateData);

        } catch (Exception $e) {
            Log::error("Error: {$e->getMessage()}");
            return $this->jsonError(trans('messages.error'));
        }
    }

    public function delete(Request $request)
    {
        try {
            $decryptedId = Crypt::decrypt($request->id);
            $storeGroup = StoreGroup::find($decryptedId);

            if (!$storeGroup) {
                return $this->jsonError('Store Group not found.');
            }

            if ($this->isGroupUsedInStores($storeGroup->id)) {
                return $this->jsonError('Deletion failed, This Store Group is used in Stores Table.');
            }

            $storeGroup->delete();
            $this->auditTrail($request, "Deleted Store Group: $storeGroup->group_name");

            return $this->jsonSuccess($storeGroup);

        } catch (Exception $e) {
            Log::error("Error: {$e->getMessage()}");
            return $this->jsonError(trans('messages.error'));
        }
    }

    // Helper Methods

    private function validateRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required',
            'brand_id' => 'required',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(trans('messages.required'));
        }
    }

    private function decryptId($id)
    {
        return !empty($id) ? Crypt::decrypt($id) : null;
    }

    private function groupExists($groupName, $brandId, $decryptedId)
    {
        return StoreGroup::where('group_name', $groupName)
            ->where('brand_id', $brandId)
            ->where('id', '!=', $decryptedId)
            ->exists();
    }

    private function isGroupUsedInStores($groupId)
    {
        return Store::where('group_id', $groupId)->exists();
    }

    private function auditTrail(Request $request, $message)
    {
        $request['remarks'] = $message;
        $request['type'] = 2;
        $this->audit_trail($request);
    }

    private function jsonError($message)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
        ]);
    }

    private function jsonSuccess($data)
    {
        return response()->json([
            'error' => false,
            'message' => trans('messages.success'),
            'data' => $data,
        ]);
    }
}
