<?php

namespace App\Http\Controllers\v1\web\products;

use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductClassification;

class ProductClassificationController extends Controller
{
    public function create_classification(Request $request) {
        try {

            $classificationNames = ProductClassification::pluck('name');
            foreach ($classificationNames as $name) {
                if ($name == $request->name) {
                    return response()->json([
                        "error"             => true,
                        "message"           => trans('messages.product.productClassification.exists'),
                    ]);
                }
            }

            DB::beginTransaction();

            ProductClassification::create([
                'name'   => $request->name,
                'status' => 1,
            ]);

            DB::commit();

            $message    = "Created Product Classification: " . $request->name;
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
        try {

            $classificationCount = ProductClassification::where('name',$request->name)->whereNot('id',$request->id)->count();

            if ($classificationCount > 0)  {
                return response()->json([
                    "error"             => true,
                    "message"           => trans('messages.product.productClassification.exists'),
                ]);
            }

            DB::beginTransaction();

            ProductClassification::where('id', $request->id)->update([
                'name'   => $request->name,
            ]);

            DB::commit();

            $message    = "Updated Product Classification: " . $request->name;
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

    public function get_classification(Request $request) {
        try {
            DB::beginTransaction();

            $classificationData = ProductClassification::get();
            $tableData = $classificationData->map(function ($items) {
                return [
                    'id'            => $items->id,
                    'name'          => $items->name,
                    'created_at'    => $items->created_at->format('M d, Y h:i A'),
                    'encrypted_id'  => Crypt::encrypt($items->id),
                ];
            });

            DB::commit();

            return response()->json([
                "error"             => false,
                "message"           => trans('messages.success'),
                "data"              => $tableData,
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

    public function archive_classification(Request $request) {
        try {
            DB::beginTransaction();
            $classificationData = ProductClassification::find($request->id);

            if($classificationData->status == 1) {
                ProductClassification::where('id',$request->id)->update([
                    'status'    => 0,
                ]);
                $message    = "Archived Product Classification: " . $classificationData->name;
            } else {
                ProductClassification::where('id',$request->id)->update([
                    'status'    => 1,
                ]);
                $message    = "Activated Product Classification: " . $classificationData->name;
            }

            DB::commit();

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
}
