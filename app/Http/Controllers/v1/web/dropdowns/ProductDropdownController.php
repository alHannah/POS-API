<?php

namespace App\Http\Controllers\v1\web\dropdowns;

use App\Http\Controllers\Controller;
use App\Models\BillOfMaterial;
use App\Models\Brand;
use App\Models\ModeOfPayment;
use App\Models\Category;
use App\Models\Store;
use App\Models\Uom;
use App\Models\UomCategory;
use App\Models\Product;
use App\Models\ProductPerStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductDropdownController extends Controller
{
    public function uom_category_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $uomCategoryName = UomCategory::get();

            $tableDetails = $uomCategoryName->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'name'          => $item->name,
                    'created_at'    => $item->created_at,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableDetails,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }

    public function brand_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $branddropdown = Brand::where('status', 1)->get(['id','brand']);

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $branddropdown,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }

    public function mop_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $mopdropdown = ModeOfPayment::where('status', '1')->get();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $mopdropdown,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }
    public function category_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $uomCategoryName = Category::get();

            $tableDetails = $uomCategoryName->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'name'          => $item->name,
                    'tag'           => $item->tag,
                    'status'        => $item->status,
                    // 'created_at'    => $item->created_at,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableDetails,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }

    public function store_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $Store = Store::where('status', 1)->get();

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $Store,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }

    public function product_s_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $productIds = Product::where('product_tag', 's')
                ->where('status', 1)
                ->doesntHave('product_per_bom')
                ->get(['id', 'name']);

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $productIds,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }

    public function product_w_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $productIds = Product::where('product_tag', 'w')->where('status', 1)->get();

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $productIds,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }
    public function for_packaging_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $productIds = Product::where('product_tag', 'w')
                ->where('status', 1)
                ->where('for_packaging', 1)
                ->get();

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $productIds,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }

    public function uom_per_product(Request $request)
    {
        try {
            DB::beginTransaction();

            $products = Product::find($request->product_id);

            $uom = Uom::where('id', $products->uom_id)->first();

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $uom,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }

    public function order_type_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $products = Product::find($request->product_id);

            $uom = Uom::where('id', $products->uom_id)->first();

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $uom,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }
}
