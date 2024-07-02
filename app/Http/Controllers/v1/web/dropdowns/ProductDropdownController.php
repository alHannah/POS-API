<?php

namespace App\Http\Controllers\v1\web\dropdowns;

use App\Http\Controllers\Controller;
use App\Models\Uom;
use App\Models\UomCategory;
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

            $tableDetails = $uomCategoryName->map(function($item){
                return[
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
}
