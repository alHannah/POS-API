<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\BillOfMaterial;
use App\Models\Packaging;
use App\Models\PackagingDetail;
use App\Models\Product;
use App\Models\ProductPerStore;
use App\Models\Store;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillOfMaterialController extends Controller
{
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $productIds = $request->product_id;
            $quantity = $request->qty;
            //$bomId = $request->bom_id;

            foreach ($productIds as $key => $productId) {
                $qty = $quantity[$key];
                //$bom = $bomId[$key];

                $getUom = Product::where('id', $productId)->value('uom_id');
                $createBom = BillOfMaterial::create([
                    'product_id' => $productId,
                    'uom_id' => $getUom,
                    'qty' => $qty,
                    //'bom_id' => $bom
                ]);
            }

            $orderTypeIds = $request->order_type;
            $packagingProducts = $request->packaging_product;
            $packagingQuantities = $request->packaging_qty;
            $packagingUoms = Product::whereIn('id', $packagingProducts)->pluck('uom_id');

            foreach ($packagingProducts as $key => $packagingProduct) {
                foreach ($orderTypeIds as $orderTypeId){
                    $createPackaging = Packaging::create([
                        'product_id'    => $packagingProduct,
                        'order_type_id' => $orderTypeId
                    ]);
                }
                /* PackagingDetail::create([
                    'packaging_id' => $createPackaging->id,
                    'product_id'   => $packagingProduct,
                    'qty'          => $packagingQuantities[$packagingProduct],
                    'uom_id'       => $packagingUoms[$packagingProduct]
                ]); */
            }
            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $createBom,
                'data2'             => $createPackaging
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
