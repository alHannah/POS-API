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
            $brand                      = $request->brand;
            $productCode                = $request->product_code;
            $productName                = $request->product_name;
            $productTag                 = $request->product_tag;
            $packaging                  = 0;
            $image                      = $request->imageFile;
            $uom                        = $request->uom;
            $min_uom                    = $request->min_uom;
            $productClassification      = $request->product_classification;
            $category                   = $request->category;

            $validator = Validator::make($request->all(), [
                'brand'                     => 'required',
                'productCode'               => 'required',
                'productName'               => 'required',
                'productTag'                => 'required',
                'packaging'                 => 'nullable',
                'uom'                       => 'required',
                'min_uom'                   => 'required',
                'product_classification'    => 'required',
                'category'                  => 'required',
                'image'                     => 'nullable|image|max:1024' // validate image file, max 1MB
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
            ? Product::with('product_category', 'product_brand', 'product_uom')->find($decryptedId)
            : null;
            $previousBrand                      = $previousData->brand                   ?? 'N/A';
            $previousProductCode                = $previousData->product_code            ?? 'N/A';
            $previousProductName                = $previousData->name                    ?? 'N/A';
            $previousProductTag                 = $previousData->product_tag             ?? 'N/A';
            $previousPackaging                  = $previousData->for_packaging           ?? 'N/A';
            $previousImage                      = $previousData->product_image           ?? 'N/A';
            $previousUom                        = $previousData->uom                     ?? 'N/A';
            $previousMinUom                     = $previousData->min_level_uom           ?? 'N/A';
            $previousProductClassification      = $previousData->product_classification  ?? 'N/A';
            $previousCategory                   = $previousData->category                ?? 'N/A';


            // Handling image upload
            $imageBase64 = null;
            if ($image->hasFile('image')) {
                $image = $request->file('image');
                $imageBase64 = base64_encode(file_get_contents($image->getRealPath()));
            }

            $createUpdate = Product::updateOrCreate([
                'id'            => $decryptedId
            ], [
                'brand'                     => $brand,
                'productCode'               => $productCode,
                'productName'               => $productName,
                'productTag'                => $productTag,
                'packaging'                 => $packaging,
                'image'                     => $imageBase64,
                'uom'                       => $uom,
                'min_uom'                   => $min_uom,
                'product_classification'    => $productClassification,
                'category'                  => $category
            ]);

            $brand = Brand::find($brand)->brand;

            $previousDetails = "Brand: $previousBrand, Product Code: $previousProductCode, Product Name: $previousProductName, "
                             . "Product Tag: $previousProductTag, Packaging: $previousPackaging, Image: $previousImage, "
                             . "UOM: $previousUom, Min UOM: $previousMinUom, Classification: $previousProductClassification, "
                             . "Category: $previousCategory";

            $newDetails = "Brand: $brand, Product Code: $productCode, Product Name: $productName, Product Tag: $productTag, "
                        . "Image: $imageBase64, Packaging: $packaging, UOM: $uom, Min UOM: $min_uom, "
                        . "Classification: $productClassification, Category: $category";

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

            $categoryFilter     = (array) $request->category;
            $tagFilter          = (array) $request->tag;
            $statusFilter       = (array) $request->status;

            // Flatten the filters in case they are nested
            $categoryFilter     = Arr::flatten($categoryFilter, 1);
            $tagFilter          = Arr::flatten($tagFilter, 1);
            $statusFilter       = Arr::flatten($statusFilter, 1);

            $thisData = Product::with('product_category', 'product_per_brand', 'product_uom');

            if (!empty($categoryFilter)) {
                $thisData->whereIn('category_id', $categoryFilter);
            }

            if (!empty($tagFilter)) {
                $thisData->whereIn('product_tag', $tagFilter);
            }
            if (!empty($statusFilter)) {
                $thisData = $thisData->where('status', $statusFilter);
            }

            $getData = $thisData->get();

            $generateData = $getData->map(function ($items) {
                $id                     = $items->id                            ?? 'N/A';
                $product_code           = $items->product_code                  ?? 'N/A';
                $product_name           = $items->name                          ?? 'N/A';
                $brand                  = $items->product_per_brand->brand      ?? 'N/A';
                $category               = $items->product_category->name        ?? 'N/A';
                $uom                    = $items->product_uom->name             ?? 'N/A';
                $min_uom                = $items->min_level_uom                 ?? 'N/A';
                $tag                    = $items->product_tag                   ?? 'N/A';
                $status                 = $items->status                        ?? 'N/A';

                return [
                    'id'               => Crypt::encrypt($id),
                    'product_code'     => $product_code,
                    'product_name'     => $product_name,
                    'brand'            => $brand,
                    'category'         => $category,
                    'uom'              => $uom,
                    'min_uom'          => $min_uom,
                    'tag'              => $tag,
                    'status'           => $status
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
            // dd(Crypt::encrypt(820));

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
