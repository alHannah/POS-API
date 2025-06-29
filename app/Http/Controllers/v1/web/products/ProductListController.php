<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductListController extends Controller
{
    public function create_update(Request $request) {
        try {
            DB::beginTransaction();

            $decryptedId                = !empty($request->id) ? Crypt::decrypt($request->id) : null;
            $productName                = $request->product_name;
            $productCode                = $request->product_code;
            $Category                   = $request->category;
            $productClassification      = $request->product_classification;
            $posCategory                = $request->pos_category;
            $uom                        = $request->uom;
            $min_uom                    = $request->min_uom;
            $productTag                 = $request->product_tag;
            $image                      = $request->imageFile;
            $packaging                  = 0;
            $brand                      = $request->brand;

            $validator = Validator::make($request->all(), [
                'product_name'               => 'required',
                'product_code'               => 'required',
                'category'                   => 'required',
                'uom'                        => 'required',
                'min_uom'                    => 'required',
                'product_tag'                => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => trans('messages.required')
                ]);
            }

            $existingGroup = Product::where('name', $productName)
                            ->where('brand_id', $brand)
                            ->where('id', '!=', $decryptedId)
                            ->first();

            if ($existingGroup) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.store_group.existed'),
                ]);
            }

            $previousData = Crypt::encrypt($decryptedId)
                            ? Product::with('product_per_posCategories', 'product_brand', 'product_uom')->find($decryptedId)
                            : null;

            // ----------------------------- PREVIOUS DATA FROM DATABASE --------------------------
            $previousProductName                = $previousData->name                    ?? 'N/A';
            $previousProductCode                = $previousData->product_code            ?? 'N/A';
            $previousCategory                   = $previousData->category_id             ?? 'N/A';
            $previousProductClassification      = $previousData->product_classification  ?? 'N/A';
            $previousPosCategory                = $previousData->pos_category_name       ?? 'N/A';
            $previousUom                        = $previousData->uom                     ?? 'N/A';
            $previousMinUom                     = $previousData->min_level_uom           ?? 'N/A';
            $previousProductTag                 = $previousData->product_tag             ?? 'N/A';
            $previousImage                      = $previousData->product_image           ?? 'N/A';
            $previousPackaging                  = $previousData->for_packaging           ?? 'N/A';
            $previousBrand                      = $previousData->product_brand->brand    ?? 'N/A';

            // ----------------------- IMAGE STORING --------------------------
            if ($image) {
                $fileName = $image->getClientOriginalName();
                $imageURL = url('/') . '/storage/uploads/products/' . $fileName;
                $image->storeAs('/uploads/products/', $fileName, 'public');
            } else {
                $imageURL = 'N/A';
            }

            // --------------------- CREATE OR UPDATE ---------------------------
            $createUpdate = Product::updateOrCreate([
                'id'                        => $decryptedId
            ], [
                'name'                          => $productName,
                'product_code'                  => $productCode,
                'category_id'                   => $Category,
                'product_classification_id'     => $productClassification,
                'pos_category_id'               => $posCategory,
                'uom_id'                        => $uom,
                'min_level_uom'                 => $min_uom,
                'product_tag'                   => $productTag,
                'product_image'                 => $imageURL,
                'for_packaging'                 => $packaging,
                'brand_id'                      => $brand,
            ]);

            // --------------------------- AUDIT TRAILING ------------------------------------------
            $brand = Brand::find($brand)->brand;

            $previousDetails = "Brand: $previousBrand, Product Code: $previousProductCode, Product Name: $previousProductName, "
                             . "Product Tag: $previousProductTag, Packaging: $previousPackaging, Image: $previousImage, "
                             . "UOM: $previousUom, Min UOM: $previousMinUom, Classification: $previousProductClassification, "
                             . "Pos Category: $previousPosCategory, Category: $previousCategory";

            $newDetails = "Brand: $brand, Product Code: $productCode, Product Name: $productName, Product Tag: $productTag, "
                        . "Image: $imageURL, Packaging: $packaging, UOM: $uom, Min UOM: $min_uom, "
                        . "Classification: $productClassification, Category: $Category, POS Category: $posCategory";

            $message = $decryptedId
                    ? "Updated Previous $previousDetails to New: $newDetails"
                    : "Created Product Name: $productName";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $createUpdate
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");

            return response()->json([
                "error"=> true,
                "message"=> trans('messages.error'),
            ]);
        }
    }
    public function get(Request $request) {
        try {
            DB::beginTransaction();

            $thisData = Product::with('product_category', 'product_per_brand', 'product_uom');

            $filters = [
                // Flatten the filters in case they are nested
                'pos_category_id' => Arr::flatten((array) $request->category, 1),
                'product_tag'     => Arr::flatten((array) $request->tag, 1),
                'status'          => Arr::flatten((array) $request->status, 1),
            ];

            foreach ($filters as $filterType => $filterApplied) {
                if (!empty($filterApplied)) {
                    if ($filterType === 'status') {
                        $thisData->where($filterType, $filterApplied);
                    } else {
                        $thisData->whereIn($filterType, $filterApplied);
                    }
                }
            }

            $getData = $thisData->get();


            $generateData = $getData->map(function ($items) {
                $id           = $items->id                                          ?? 'N/A';
                $picture      = url($items->product_image);

                return [
                    'id'                     => Crypt::encrypt($id)                 ?? 'N/A',
                    'product_code'           => $items->product_code                ?? 'N/A',
                    'product_name'           => $items->name                        ?? 'N/A',
                    'product_image'          => $picture                            ?? 'N/A',
                    'brand'                  => $items->product_per_brand->brand    ?? 'N/A',
                    'category'               => $items->product_category->name      ?? 'N/A',
                    'uom'                    => $items->product_uom->name           ?? 'N/A',
                    'min_uom'                => $items->min_level_uom               ?? 'N/A',
                    'tag'                    => $items->product_tag                 ?? 'N/A',
                    'status'                 => $items->status                      ?? 'N/A',
                ];
            });

            DB::commit();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                'data'          => $generateData,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");

            return response()->json([
                "error"=> true,
                "message"=> trans('messages.error'),
            ]);
        }
    }
    public function archive_activate(Request $request) {
        try {
            DB:: beginTransaction();

            $decryptedId = Crypt::decrypt($request->id);

            $thisData = Product::where('id', $decryptedId)->first();

            $thisData->status == 'active' ? $thisData->update(['status' => 0]) : $thisData->update(['status' => 1]);

            $name = Product::where('id', $decryptedId)->first()->name;

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
