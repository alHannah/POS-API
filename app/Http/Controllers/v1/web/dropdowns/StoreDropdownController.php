<?php

namespace App\Http\Controllers\v1\web\dropdowns;

use Exception;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Store;
use App\Models\Users;
use App\Models\PriceTier;
use App\Models\MobileUser;
use App\Models\StoreGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StoreDropdownController extends Controller
{
    /*--------------STORES DROPDOWN---------------*/
    public function brand_dropdown(Request $request) {
        try {
            DB::beginTransaction();

            $brandDropdown = Brand::where('status', 1 || '1' || 'active')->get();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $brandDropdown,
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

            $brandId = $request->brand_id;
            $storeGroupDropdown = StoreGroup::where('brand_id', $brandId)->get();

            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$storeGroupDropdown,
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

    public function area_dropdown(Request $request)
    {
        try {
            DB::beginTransaction();

            $brandId = $request->brand_id;
            $areaDropdown = Area::where('brand_id', $brandId)->get();

            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$areaDropdown,
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

    public function area_dropdown_get(Request $request)
    {
        try {
            DB::beginTransaction();

            $areaDropdown = Area::where('status', 1 || '1' || 'active')->get();

            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$areaDropdown,
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

    public function price_tier_dropdown(Request $request) {
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

    public function user_dropdown(Request $request) {
        try {
            DB::beginTransaction();

            $userDropdown = Users::where('status', "1")->get();

            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$userDropdown,
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

    public function mobile_user_dropdown(Request $request) {
        try {
            DB::beginTransaction();

            $mobDropdown = MobileUser::where('status', "active")->get();

            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$mobDropdown,
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

    /*--------------STORES DROPDOWN---------------*/
}
