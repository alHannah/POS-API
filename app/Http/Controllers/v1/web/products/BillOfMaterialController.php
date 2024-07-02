<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\BillOfMaterial;
use App\Models\Product;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillOfMaterialController extends Controller
{
    public function create(Request $request)
    {
        try{
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

            $tableDetails = $productDetails->map(function($item){
                return[
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
        }catch (Exception $e) {
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
        try{
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

            $tableDetails = $productDetails->map(function($item){
                return[
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
        }catch (Exception $e) {
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
        try{
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

            $tableDetails = $productDetails->map(function($item){
                return[
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
        }catch (Exception $e) {
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
        try{
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

            $tableDetails = $productDetails->map(function($item){
                return[
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
        }catch (Exception $e) {
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
        try{
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

            $tableDetails = $productDetails->map(function($item){
                return[
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
        }catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }

    }
}
