<?php

namespace App\Http\Controllers\v1\web\dropdowns;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Crypt;
use PDO;

class InventoryDropdownController extends Controller
{
    public function store_group_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $storeGroup = StoreGroup::get();
            $data = $storeGroup->map(function ($item) {
                return [
                    'id'            => $item->id,
                    /* 'encrypted_id'  => Crypt::encrypt($item->id), */
                    'group_name'    => $item->group_name,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $data,
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

            $storeGroup = $request->storeGroup;
            $storeDropdown = Store::whereIn('group_id', $storeGroup)->where('status', 1)->get();
            $data = $storeDropdown->map(function ($item) {
                return [
                    'id'            => $item->id,
                    /* 'encrypted_id'  => Crypt::encrypt($item->id), */
                    'store_name'    => $item->store_name,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $data,
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

    public function warehouse_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $productIds = Product::where('product_tag', 'w')->where('status', 1)->get();
            $data = $productIds->map(function ($item) {
                return [
                    'id'             => $item->id,
                    'name'           => $item->name,
                    'product_tag'    => $item->product_tag,
                    'brand_id'       => $item->brand_id,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $data,
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

    public function store_return_dropdown(Request $request)
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

    public function storeGroup_dropdown(Request $request)
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

    public function store_group_dropdown_get (Request $request)
    {
        try {
            DB::beginTransaction();

            $storeGroupDropdown = StoreGroup::get();
            $storeGroupData = $storeGroupDropdown->map(function ($items) {
                return [
                    'id'            => $items->id,
                    'encrypted_id'  => Crypt::encrypt($items->id),
                    'group_name'    => $items->group_name,
                ];
            });


            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$storeGroupData,
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
