<?php

namespace App\Http\Controllers\v1\web\products;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Uom;
use Illuminate\Http\Request;
use App\Models\UomCategory;
use Illuminate\Support\Facades\Crypt;

class UomController extends Controller
{
    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $createUom = Uom::create([
                'name'                => $request->name,
                'uom_category_id'     => $request->uom_category_id,
                'quantity'            => $request->quantity,
            ]);
            $uomCategoryName =  $createUom::with('uom_per_categories')->find($request->uom_category_id);

            if ($createUom->wasRecentlyCreated) {
                $message    = "Created UoM: '$request->name', Categery: $uomCategoryName->name, Quantity: $request->quantity";
            }

            DB::commit();

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $createUom,
                //'audit_trail'       => $message
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function createCategory(Request $request)
    {
        try {
            DB::beginTransaction();

            $createUomCategory = UomCategory::create([
                'name'            => $request->name
            ]);

            if ($createUomCategory->wasRecentlyCreated) {
                $message    = "Created UoM Category: '$request->name'";
            }

            DB::commit();

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $createUomCategory,
                //'audit_trail'       => $message
            ]);
        } catch (Exception $e) {
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
        try {
            DB::beginTransaction();
            $decryptedId = Crypt::decrypt($request->id);
            $uomDetails = Uom::find($decryptedId)->first();

            //$encryptedId = Crypt::encrypt($request->id);

            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $uomDetails,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function editCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $decryptedId = Crypt::decrypt($request->id);
            $uomCategoryDetails = UomCategory::find( $decryptedId);
            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $uomCategoryDetails,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);
        } catch (Exception $e) {
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
        try {
            DB::beginTransaction();
            $decryptedId = Crypt::decrypt($request->id);
            //audit
            $previousDetails = Uom::with('uom_per_categories')->where('id',$decryptedId)->first();
            $previousCategoryName = $previousDetails->uom_per_categories->name;

            $updateUom = Uom::where('id',$decryptedId)->update([
                'name'                => $request->name,
                'uom_category_id'     => $request->uom_category_id,
                'quantity'            => $request->quantity,
            ]);
            //audit
            $newCategoryName = UomCategory::find( $request->uom_category_id);

            DB::commit();

            $message    = "Updated UoM: '$previousDetails->name', $previousCategoryName, $previousDetails->quantity change into '$request->name', $newCategoryName->name, $request->quantity";

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $updateUom,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function updateCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $decryptedId = Crypt::decrypt($request->id);
            $previousName = UomCategory::find($decryptedId);

            $updateUom = UomCategory::where('id',$decryptedId)->update([
                'name'                => $request->name,
            ]);

            DB::commit();

            $message    = "Updated UoM Category: '$previousName->name' change into '$request->name'";
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $updateUom,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);

        } catch (Exception $e) {
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
        try {
            DB::beginTransaction();
            $decryptedId = Crypt::decrypt($request->id);
            $previousDetails = Uom::find($decryptedId);
            $previousCategoryName = $previousDetails->uom_per_categories->name;

            $deleteUom = Uom::where('id', $decryptedId)->delete();

            DB::commit();
            $message    = "Deleted UoM: '$previousDetails->name', $previousCategoryName, $previousDetails->quantity";
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $deleteUom,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function deleteCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $decryptedId = Crypt::decrypt($request->id);
            $previousDetails = UomCategory::find($decryptedId);

            $deleteUom = UomCategory::where('id', $decryptedId)->delete();

            DB::commit();

            $message    = "Deleted UoM Category: '$previousDetails->name'";
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $deleteUom,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);

        } catch (Exception $e) {
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
        try {
            DB::beginTransaction();

            $uomDetails = Uom::with('uom_per_categories')->get();

            $tableDetails = $uomDetails->map(function ($item){
                return[
                    'id'            =>Crypt::encrypt($item->id),
                    'name'          =>$item->name,
                    'category'      =>$item->uom_per_categories->name,
                    'quantity'      =>$item->quantity,
                    'created_at'    =>$item->created_at->format("M d, Y h:i A"),
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableDetails,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function getCategory(Request $request)
    {
        try {
            DB::beginTransaction();

            $uomCategory = UomCategory::get();

            $tableDetails = $uomCategory->map(function ($item){
                return[
                    'id'            =>Crypt::encrypt($item->id),
                    'name'          =>$item->name,
                    'created_at'    =>$item->created_at->format("M d, Y h:i A"),
                ];
            });

            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableDetails,
                //'encryptedId'       => $encryptedId
                //'audit_trail'       => $message
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }
}
