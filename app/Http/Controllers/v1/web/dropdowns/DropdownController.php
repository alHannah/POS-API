<?php

namespace App\Http\Controllers\v1\web\dropdowns;

use Exception;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\StoreGroup;

class DropdownController extends Controller
{
    public function brand_dropdown(Request $request) {
        try {
            DB::beginTransaction();

            $branddropdown = Brand::where('status', '1')->get();

            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$branddropdown,
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

    public function store_group_dropdown(Request $request) {
        try {
            DB::beginTransaction();

            $storegroupdropdown = StoreGroup::get();

            return response()->json([
                "error"         =>false,
                "message"       =>trans('messages.success'),
                "data"          =>$storegroupdropdown,
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

    public function stores_dropdown(Request $request) {
        $brand = $this->brand_dropdown($request);
        $storegroup = $this->store_group_dropdown($request);

        return response()->json([
            "error"             =>false,
            "message"           =>trans('messages.success'),
            "brand_dropdown"    =>$brand,
            "store_dropdown"    =>$storegroup,
            ]);
        }
}
