<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

use function PHPUnit\Framework\isEmpty;

class AreaController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();

            $encryptedId = $request ? Crypt::decrypt($request->id) : null;
            $name        = $request->name;
            $brandId     = $request->brand_id;

            if (!$name || !$brandId) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.required')
                ]);
            }

            $existingGroup = Area::where('name', $name)
                            ->where('id', '!=', $encryptedId)
                            ->first();

            if ($existingGroup) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.area.existed'),
                ]);
            }

            $previousData       = Crypt::encrypt($encryptedId) ? Area::with('brand_areas')->where('id', $encryptedId)->first() : null;
            $previousArea       = $previousData->name ?? 'N/A';
            $previousBrand      = $previousData->brand_areas->brand ?? 'N/A';

            // --------------------------updateOrCreate-----------------------------------------

            $createUpdate = Area::updateOrCreate([
                'id'        => $encryptedId
            ], [
                'name'      => $name,
                'brand_id'  => $brandId
            ]);

            $brandName = Brand::find($brandId)->brand;

            $message = $encryptedId
                    ? "Update Previous: $previousArea (Brand: $previousBrand) New: $name (Brand: $brandName)"
                    : "Created $name (Brand: $brandName)";

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

            $brandFilter    = (array) $request->brandFilter;
            $status         = $request->status;

            // Flatten the filters in case they are nested
            $brandFilter    = Arr::flatten($brandFilter, 1);

            $thisData = Store::with(['store_brands', 'store_per_area'])->where('status', 1);

            if (!empty($brandFilter)) {
                $thisData->whereIn('brand_id', $brandFilter);
            }

            $getData = $thisData->get();

            $generateData   = $getData->map(function ($items) {
                    $name       = $items->store_per_area->name ?? 'N/A';
                    $brand      = $items->store_brands->brand ?? 'N/A';
                    $status     = $items->status ?? 'N/A';

                    return [
                        'area_name'     => $name,
                        'brand'         => $brand,
                        'status'        => $status,
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

            $encryptedId = Crypt::decrypt($request->id);

            $area = Area::where('id', $encryptedId)
                  -> delete();

            $message = "Deleted Successfully!";

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
