<?php

namespace App\Http\Controllers\v1\web\dropdowns;

use Exception;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PriceTier;
use App\Models\Store;
use App\Models\StoreGroup;
use Illuminate\Support\Facades\Crypt;

class DropdownController extends Controller
{
    public function brand_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $branddropdown = Brand::where('status', '1')->get();

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

    public function store_group_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $storegroupdropdown = StoreGroup::get();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $storegroupdropdown,
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

    public function price_tier_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $priceTierDropdown = PriceTier::where('status', "active")->get();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $priceTierDropdown,
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

    public function manager_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $managerDropdown = PriceTier::where('status', "active")->get();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $managerDropdown,
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

    public function add_product_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $managerDropdown = PriceTier::where('status', "active")->get();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $managerDropdown,
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

    public function stores_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $storeData = Store::where('status', 1)->get();

            /* $storeDataEncryptedId = $storeData->map(function ($item){
                $item->encryptedId = Crypt::encrypt($item->id);
                return $item;
            }); */

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $storeData,
                //"data"          => $storeDataEncryptedId,
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
