<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\BillOfMaterial;
use App\Models\Brand;
use App\Models\Packaging;
use App\Models\PackagingDetail;
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
            $products           = $request->product;
            $flatProductIds     = Arr::flatten($products);
            $packagingUoms      = Product::whereIn('id', $flatProductIds)->pluck('uom_id', 'id');

            foreach ($orderTypeIds as $index => $orderTypeId) {
                foreach ($products[$index] as $productIndex => $productId) {
                    $packagingUom       = $packagingUoms[$productId];
                    $quantity           = $quantities[$index][$productIndex];

                    $createBom = Packaging::create([
                        'order_type_id' => $orderTypeId,
                        'product_id'    => $SproductIds,
                    ]);

                    PackagingDetail::create([
                        'packaging_id'  => $createBom->id,
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

    public function edit(Request $request)
    {
        try {
            DB::beginTransaction();

            $bom = BillOfMaterial::with('bom_per_product')->where('id',$request->id)->get();

            $tableDetails = $bom->map(function ($item) {
                $productComponents = BillOfMaterial::where('product_id', $item->product_id)->get([
                    'bom_id',
                    'qty',
                    'uom_id'
                ]);
                $brandName = Brand::find($item->bom_per_product->brand_id);

                $packaging = Packaging::where('product_id', $item->product_id)->get();


                return [
                    'id'                    => $item->id,
                    'product_id'            => $item->product_id,
                    'product_name'          => $item->bom_per_product->name,
                    'product_code'          => $item->bom_per_product->product_code,
                    'brand'                 => $brandName->brand,
                    'product_components'    => $productComponents,
                    'created_at'            => $item->created_at->format("M d, Y h:i A"),

                    'packaging'             =>$packaging
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

            $bomDetails = BillOfMaterial::with([
                'bom_per_product',
                'bom_per_uom'
            ])
                ->get();

            $productDetails = Product::with([
                'product_per_brand',
                'product_per_posCategories'
            ])->where('status',)->get();

            $tableDetails = $productDetails->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'name'          => $item->name,
                    'brand'         => $item->product_per_brand->brand,

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

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $bomDetails = BillOfMaterial::with([
                'bom_per_product',
                'bom_per_uom'
            ])
                ->get();

            $productDetails = Product::with([
                'product_per_brand',
                'product_per_posCategories'
            ])->where('status',)->get();

            $tableDetails = $productDetails->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'name'          => $item->name,
                    'brand'         => $item->product_per_brand->brand,

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

    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            $bomDetails = BillOfMaterial::with([
                'bom_per_product',
                'bom_per_uom'
            ])
                ->get();

            $productDetails = Product::with([
                'product_per_brand',
                'product_per_posCategories',
            ])->where('status', 'active')->where('product_tag', 's')->get();

            $tableDetails = $productDetails->map(function ($item) {

                return [
                    'id'    => $item->id,
                    'name'  => $item->name,
                    'brand' => $item->product_per_brand->brand,
                    'pos'   => $item->product_per_posCategories->pos_category_name,
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
