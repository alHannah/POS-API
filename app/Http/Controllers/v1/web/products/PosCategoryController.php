<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\PosCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosCategoryController extends Controller
{

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;
            $brand_id = $request->brand_id;

            if (!$id) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $posCategory = Brand::find($brand_id);
            if (!$posCategory) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.posCat.notFound')
                ]);
            }

            $brand_name = $posCategory->brand;

            $existingCat = PosCategory::where('id', $id)->first();
            if ($existingCat) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.posCat.existed'),
                ]);
            }

            $pos_category = PosCategory::create([
                'id'               => $id,
                'brand_id'         => $brand_id,
                'pos_category_name' => $request->pos_category_name,
                'status'           => 1,
            ]);

            $message = "{$brand_name} New POS Category => Category Name: {$request->pos_category_name} & Brand: {$brand_name}";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => [
                    'id'                => $pos_category->id,
                    'brand_id'          => $pos_category->brand_id,
                    'brand_name'        => $brand_name,
                    'pos_category_name' => $pos_category->pos_category_name,
                    'status'            => $pos_category->status,
                    'created_at'        => $pos_category->created_at->format('M d, Y h:i A'),
                ],
                'audit_trail' => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'      => true,
                'message'    => trans('messages.error'),
            ]);
        }
    }


    public function displayCategory(Request $request)
    {
        try {
            $statusFilter = (array) $request->statusFilter;
            $brandFilter  = (array) $request->brandFilter;

            $statusFilter = Arr::flatten($statusFilter, 1);
            $brandFilter  = Arr::flatten($brandFilter, 1);

            $query = PosCategory::with(['pos_category_brand' => function ($query) {
                $query->select('id', 'brand');
            }]);

            if (!empty($brandFilter)) {
                $query->whereIn('brand_id', $brandFilter);
            }

            if (!empty($statusFilter)) {
                $query->whereIn('status', $statusFilter);
            }

            $data = $query->orderByDesc('created_at')
                ->select(['brand_id', 'pos_category_name', 'status', 'created_at'])
                ->get()
                ->map(function ($item) {
                    return [
                        'brand_id'      => Crypt::encrypt($item->brand_id),
                        'brand_name'    => $item->pos_category_brand->brand ?? null,
                        'category_name' => $item->pos_category_name,
                        'status'        => $item->status,
                        'created_at'    => Carbon::parse($item->created_at)->format('M d, Y h:i A'),
                        'brand_id_nE'      => $item->brand_id,
                    ];
                });

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            Log::error("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }


    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $encryptedId = $request->id ? Crypt::decrypt($request->id) : null;
            $encryptedB_Id = $request->brand_id ? Crypt::decrypt($request->brand_id) : null;

            if (!$encryptedId) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $previousData = PosCategory::find($encryptedId);

            if (!$previousData) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.posCat.notFound')
                ]);
            }

            $brand = Brand::find($encryptedB_Id);
            $previousBrand = $brand ? $brand->brand : null;

            if ($previousData->pos_category_name == $request->pos_category_name && $previousData->brand_id == $encryptedB_Id) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.posCat.alreadyExist')
                ]);
            }

            $brand = Brand::find($encryptedB_Id);
            $brand_name = $brand ? $brand->brand : null;

            $previousCat = $previousData->pos_category_name;

            $previousData->update([
                'pos_category_name' => $request->pos_category_name,
                'brand_id'          => $encryptedB_Id,
            ]);

            $newPos = $request->pos_category_name;
            $newBrand = $brand_name;

            $message = "{$brand_name} Updated POS Category -> (Category Name): $previousCat & (Brand): $previousBrand || change into || (Category Name): $newPos & (Brand): $newBrand";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => [
                    'id'                => $previousData->id,
                    'brand_id'          => $previousData->brand_id,
                    'brand_name'        => $brand_name,
                    'pos_category_name' => $previousData->pos_category_name,
                    'status'            => $previousData->status,
                    'created_at'        => $previousData->created_at->format('M d, Y h:i A'),
                ],
                'audit_trail' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    public function archiveCategory(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);

            $cat = PosCategory::find($id);
            if (!$cat) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.product.posCat.notFound'),
                ]);
            }

            DB::beginTransaction();

            $newStatus = $cat->status == 1 ? 0 : 1;
            $cat->update([
                'status' => $newStatus,
            ]);

            $action = $newStatus == 0 ? 'archived' : 'reactivated';
            $message = "{$cat->pos_category_name} has been $action.";

            $request->merge([
                'remarks' => $message,
                'type' => 2,
            ]);
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'remarks' => $message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }
}
