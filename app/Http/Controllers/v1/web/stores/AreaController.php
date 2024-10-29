<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Models\Area;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

class AreaController extends Controller
{
    public function create_update(Request $request)
    {
        try {
            DB::beginTransaction();
            // dd(Crypt::encrypt(13));

            $decryptedId        = !empty($request->id) ? Crypt::decrypt($request->id) : null;
            $name               = $request->name;
            $brandId            = $request->brand_id;

            if (!$name || !$brandId) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.required')
                ]);
            }

            $existingGroup = Area::where('name', $name)
                            ->where('id', '!=', $decryptedId)
                            ->first();

            if ($existingGroup) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.area.existed'),
                ]);
            }

            $previousData       = Crypt::encrypt($decryptedId) ? Area::with('brand_areas')
                                                                ->where('id', $decryptedId)
                                                                ->first() : null;
            $previousArea       = $previousData->name ?? 'N/A';
            $previousBrand      = $previousData->brand_areas->brand ?? 'N/A';

            // -------------------------- UPDATE OR CREATE -----------------------------------------
            $createUpdate = Area::updateOrCreate([
                'id'        => $decryptedId
            ], [
                'name'      => $name,
                'brand_id'  => $brandId
            ]);

            $brandName = Brand::find($brandId)->brand;

            $message = $decryptedId
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

            // Flatten the filters in case they are nested
            $brandFilter    = Arr::flatten((array) $request->brandFilter, 1);

            $thisData = Area::with(['brand_areas', 'areas_per_stores']);

            if (!empty($brandFilter)) {
                $thisData->whereIn('brand_id', $brandFilter);
            }

            $getData = $thisData->get();

            $generateData   = $getData->map(function ($items) {
                    $id             = $items->id                                    ?? 'N/A';
                    $created_at     = $items->created_at                            ?? 'N/A';

                    return [
                        'id'            => Crypt::encrypt($id),
                        'area_name'     => $items->name                             ?? 'N/A',
                        'brand'         => $items->brand_areas->brand               ?? 'N/A',
                        'status'        => $items->status                           ?? 'N/A',
                        'created_at'    => $created_at->format('M d, Y h:i A'),
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

    public function archive_activate(Request $request) {
        try {
            DB:: beginTransaction();
            // dd(Crypt::encrypt(820));

            $decryptedId = Crypt::decrypt($request->id);

            $thisData = Area::where('id', $decryptedId)->first();

            if (!$thisData) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Store Group not found.',
                ]);
            }


            $thisData->status == 1 || 'active' ? $thisData->update(['status' => 0]) : $thisData->update(['status' => 1]);

            $name = Area::where('id', $decryptedId)->first()->name;

            // AUDIT TRAIL LOG

            $thisData->status == 0 ? $message = "Archived an category: $name." : $message = "Activated an category: $name.";

            $request['remarks']     = $message;
            $request['type']        = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                "error" => false,
                'message' =>trans('messages.success'),
                'data'      => $thisData
            ]);
        } catch (Exception $e) {
            Log::info("Error $e");
            return response()->json([
                "error"=> true,
                "message"=> trans('messages.error'),
            ]);
        }
    }
}
