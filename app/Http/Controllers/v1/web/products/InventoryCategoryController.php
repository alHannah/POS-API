<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryCategoryController extends Controller
{
    public function create_update(Request $request) {
        try {
            DB::beginTransaction();
            // dd(Crypt::encrypt(19));

            $decryptedId        = !empty($request->id) ? Crypt::decrypt($request->id) : null;
            $tag                = $request->tag;
            $name               = $request->name;

            if (!$name) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.required')
                ]);
            }

            $existingCategory = Category::where('name', $name)
                                ->where('id', '!=', $decryptedId)
                                ->first();

            if ($existingCategory) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.product.inventory_category.existed'),
                ]);
            }

            $previousData       = Crypt::encrypt($decryptedId) ? Category::with('category_product')->where('id', $decryptedId)->first() : null;
            $previousName       = $previousData->name ?? 'N/A';

            // --------------------------updateOrCreate-----------------------------------------

            $createUpdate = Category::updateOrCreate([
                'id'        => $decryptedId
            ], [
                'name'      => $name,
                'tag'       => $tag,
            ]);

            $message = $decryptedId
                    ? "Update Previous: $previousName New: $name"
                    : "Created $name";

            $request['remarks'] = $message;
            $request['type']    = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => $createUpdate,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");

            return response()->json([
                "error"=> true,
                "message"=> trans('messages.error'),
            ]);
        }
    }
    public function get(Request $request) {
        try {
            DB::beginTransaction();

            $statusFilter = Arr::flatten((array) $request->status, 1);

            $thisData = Category::with('category_product');

            if (!empty($statusFilter)) {
                $thisData = $thisData->where('status', $statusFilter);
            }

            $getData = $thisData->get();

            $generateData = $getData->map(function ($items) {
                $id                     = $items->id               ?? 'N/A';

                return [
                    'id'                => Crypt::encrypt($id),
                    'category_name'     => $items->name            ?? 'N/A',
                    'status'            => $items->status          ?? 'N/A',
                ];
            });

            DB::commit();

            return response()->json([
                'error'         => false,
                'message'       => trans('messages.success'),
                'data'          => $generateData,

            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");

            return response()->json([
                "error"=> true,
                "message"=> trans('messages.error'),
            ]);
        }
    }
    public function archive_activate(Request $request) {
        try {
            DB:: beginTransaction();

            $decryptedId = Crypt::decrypt($request->id);

            $thisData = Category::where('id', $decryptedId)->first();

            $thisData->status == 1 ? $thisData->update(['status' => 0]) : $thisData->update(['status' => 1]);

            $name = Category::where('id', $decryptedId)->first()->name;

            // AUDIT TRAIL LOG
            $request['remarks']  = "Archived an category: $name.";
            $request['activity'] = "Archived an category: $name.";
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                "error" => false,
                'message' =>trans('messages.success'),
                'data'      => $thisData
            ]);
        } catch (Exception $e) {
            Log::info("Error $e");
            return response()->json([
                "error"=> true,
                "message"=> trans('messages.error'),
            ]);
        }
    }
}
