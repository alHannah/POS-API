<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\BillOfMaterial;
use App\Models\Brand;
use App\Models\Packaging;
use App\Models\PackagingDetail;
use App\Models\PosCategory;
use App\Models\Product;
use App\Models\ProductPerStore;
use App\Models\Store;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class BillOfMaterialController extends Controller
{
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            //--------------------------CREATE BOM------------------------------------
            $SproductIds    = $request->sproduct_id;
            $WproductIds    = $request->wproduct_id;
            $quantity       = $request->qty;

            foreach ($WproductIds as $key => $wproductId) {
                $qty       = $quantity[$key];

                $getUom = Product::where('id', $wproductId)->value('uom_id');
                $createBom = BillOfMaterial::create([
                    'product_id'    => $SproductIds,
                    'uom_id'        => $getUom,
                    'qty'           => $qty,
                    'bom_id'        => $wproductId
                ]);
            }

            //-------------------------CREATE PACKAGING and PACKAGING DETAILS-------------------------------
            $orderTypeIds       = $request->order_type;
            $quantities         = $request->quantity;
            $for_packaging      = $request->for_packaging;
            $flatProductIds     = Arr::flatten($for_packaging);
            $packagingUoms      = Product::whereIn('id', $flatProductIds)->pluck('uom_id', 'id');

            foreach ($orderTypeIds as $index => $orderTypeId) {
                $createPackaging = Packaging::create([
                    'order_type_id' => $orderTypeId,
                    'product_id'    => $SproductIds,
                ]);
                foreach ($for_packaging[$index] as $productIndex => $productId) {
                    $packagingUom       = $packagingUoms[$productId];
                    $quantity           = $quantities[$index][$productIndex];

                    PackagingDetail::create([
                        'packaging_id'  => $createPackaging->id,
                        'product_id'    => $productId,
                        'qty'           => $quantity,
                        'uom_id'        => $packagingUom,
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $createBom,
                //'data2'           => $createPackaging
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function remove_packaging(Request $request)
    {
        try {
            DB::beginTransaction();

            $remove = PackagingDetail::find($request->id)->delete();

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $remove,
                //'Packaging'         => $tableDetails,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function view(Request $request)
    {
        try {
            DB::beginTransaction();

            $bom = BillOfMaterial::with('bom_per_product')->where('id', $request->id)->get();

            $tableDetails = $bom->map(function ($item) {
                $productComponents = BillOfMaterial::where('product_id', $item->product_id)->get([
                    'bom_id',
                    'qty',
                    'uom_id'
                ]);
                $brandName = Brand::find($item->bom_per_product->brand_id);

                $packaging = Packaging::with('packaging_per_detail')->where('product_id', $item->product_id)->get([
                    'id',
                    'product_id',
                    'order_type_id',
                ]);

                return [
                    'id'                    => $item->id,
                    'product_id'            => $item->product_id,
                    'product_name'          => $item->bom_per_product->name,
                    'product_code'          => $item->bom_per_product->product_code,
                    'brand'                 => $brandName->brand,
                    'Product_Components'    => $productComponents,
                    'Packaging'             => $packaging,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'Product'           => $tableDetails,
                //'Packaging'         => $tableDetails,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            //--------------------------CREATE BOM------------------------------------
            $id             = $request->id;
            $SproductIds    = $request->sproduct_id;
            $WproductIds    = $request->wproduct_id;
            $quantity       = $request->qty;

            foreach ($WproductIds as $key => $wproductId) {
                $qty        = $quantity[$key];
                $getUom     = Product::where('id', $wproductId)->value('uom_id');
                $updateBom  = BillOfMaterial::where('id', $id)->update([
                    'bom_id'        => $wproductId,
                    'uom_id'        => $getUom,
                    'qty'           => $qty
                ]);
            }

            //-------------------------CREATE PACKAGING and PACKAGING DETAILS-------------------------------
            $orderTypeIds       = $request->order_type;
            $quantities         = $request->quantity;
            $for_packaging      = $request->for_packaging;
            $flatProductIds     = Arr::flatten($for_packaging);
            $packagingUoms      = Product::whereIn('id', $flatProductIds)->pluck('uom_id', 'id');

            foreach ($orderTypeIds as $index => $orderTypeId) {
                $updatePackaging = Packaging::where('product_id',$SproductIds)->update([
                    'order_type_id' => $orderTypeId,
                ]);
                dd($updatePackaging);
                foreach ($for_packaging[$index] as $productIndex => $productId) {
                    $packagingUom       = $packagingUoms[$productId];
                    $quantity           = $quantities[$index][$productIndex];

                    PackagingDetail::where('packaging_id',$updatePackaging->id)->update([
                        'product_id'    => $productId,
                        'qty'           => $quantity,
                        'uom_id'        => $packagingUom,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                //'data'              => ,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $bomId = $request->id;

            $bomDetails = BillOfMaterial::with('bom_per_product')->where('id', $bomId)->get();

            $previousDetails = $bomDetails->map(function ($item) {
                $brand = Brand::find($item->bom_per_product->brand_id);
                $posCategory = PosCategory::find($item->bom_per_product->pos_category_id);
                return [
                    'id'            => $item->id,
                    'name'          => $item->bom_per_product->name,
                    'brand'         => $brand->brand,
                    'pos_category'  => $posCategory->pos_category_name,
                    'created_at'    => $item->created_at->format("M d, Y h:i A"),
                ];
            });

            $deleteBom = BillOfMaterial::find($bomId)->delete();

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $previousDetails,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            $brandFilter = (array) $request->brandFilter;
            $brandFilter = Arr::flatten($brandFilter, 1);

            $product = Product::with(['product_per_bom', 'product_per_brand', 'product_per_posCategories']);
            if (!empty($brandFilter)) {
                $product->whereIn('brand_id', $brandFilter)
                    ->where('product_tag', 's');
            }

            $filteredData = $product->get();

            $tableDetails = $filteredData->map(function ($item) {
                return [
                    'name' => $item->name,
                    'brand' => $item->product_per_brand->brand,
                    'pos_category' => $item->product_per_posCategories->pos_category_name,
                    'bill_of_materials' => $item->product_per_bom->map(function ($bom) {
                        return [
                            'id' => $bom->id,
                            'bom_id' => $bom->bom_id,
                            'qty' => $bom->qty,
                            'uom_id' => $bom->uom_id,
                            'created_at' => $bom->created_at->format("M d, Y h:i A")
                        ];
                    }),
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableDetails,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }
}
