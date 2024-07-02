<?php

namespace App\Http\Controllers\v1\web\products;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductClassification;

class ProductClassificationController extends Controller
{
    public function create_classification(Request $request) {
        try {
            $classificationNames = ProductClassification::get('name');
            dd($classificationNames);
            DB::beginTransaction();

            ProductClassification::create([
                'name'   => $request->name,
                'status' => 1,
            ]);

            DB::commit();

            $message    = "Created Store: " . $request->store_name;
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                "error"             => false,
                "message"           => trans('messages.success'),
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"             => true,
                "message"           => trans("messages.error"),
            ]);
        }
    }

    public function edit_classification(Request $request) {

    }

    public function get_classification(Request $request) {

    }

    public function archive_classification(Request $request) {

    }
}
