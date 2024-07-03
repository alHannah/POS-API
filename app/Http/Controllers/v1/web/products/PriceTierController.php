<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Discount;
use App\Models\PosCategory;
use App\Models\PriceTier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PriceTierController extends Controller
{

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;
            if (!$id) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $existingDis = Discount::where('id', $id)->first();
            if ($existingDis) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.discount.existed'),
                ]);
            }

            $name = $request->name;
            $type = $request->type;
            $amount = $request->amount;
            $with_tax = $request->with_tax;
            $status = 1;

            $discount = Discount::create([
                'id'                => $id,
                'name'              => $name,
                'type'              => $type,
                'amount'            => $amount,
                'with_tax'          => $with_tax,
                'status'            => $status,
            ]);

            $message = "New Discount => Name: {$name} | Type: {$type} | Amount: {$amount} | Mode: {$with_tax} | Status: {$status}";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => [
                    'id'                => $discount->id,
                    'name'              => $discount->name,
                    'type'              => $discount->type,
                    'amount'            => $discount->amount,
                    'mode'              => $discount->with_tax,
                    'status'            => $discount->status,
                    'created_at'        => $discount->created_at->format('M d, Y h:i A'),
                ],
                'audit_trail' => $message
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'      => true,
                'message'    => trans('messages.error'),
            ]);
        }
    }

    public function displayPriceTier(Request $request)
    {
        try {

            DB::beginTransaction();

            $query = PriceTier::with(['price_tier_per_brand' => function ($query) {
                $query->select('id', 'name');
            }]);

            $data = $query->orderByDesc('created_at')->get(['id', 'name', 'status', 'created_at']);

            $data = $data->map(function ($item) {
                return [
                    'id'         => Crypt::encryptString($item->id),
                    'brand'      => $item->price_tier_per_brand->brand,
                    'name'       => $item->name,
                    'status'     => $item->status,
                    'created_at' => $item->created_at->format('M d, Y h:i A'),
                    'id_nE'      => $item->id,
                ];
            });

            DB::commit();

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error:$e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $encryptedId = $request->id ? Crypt::decrypt($request->id) : null;

            if (!$encryptedId) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $previousData = Discount::find($encryptedId);

            if (!$previousData) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.discount.notFound')
                ]);
            }

            $name = $request->name;
            $type = $request->type;
            $amount = $request->amount;
            $with_tax = $request->with_tax;

            if ($previousData->name == $name && $previousData->type == $type && $previousData->amount == $amount && $previousData->with_tax == $with_tax) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.discount.alreadyExist')
                ]);
            }

            $previousName = $previousData->name;
            $previousType = $previousData->type;
            $previousAmount = $previousData->amount;
            $previousMode = $previousData->with_tax;
            $name = $request->name;
            $type = $request->type;
            $amount = $request->amount;
            $with_tax = $request->with_tax;

            $previousData->update([
                'name'              => $name,
                'type'              => $type,
                'amount'            => $amount,
                'with_tax'          => $with_tax,
            ]);

            $newName = $request->name;
            $newType = $request->type;
            $newAmount = $request->amount;
            $newMode = $request->with_tax;

            $message = "Updated Discount -> Name: {$previousName}, Type: {$previousType}, Amount: {$previousAmount}, Mode: {$previousMode} || changed into || "
                                         . "Name: {$newName}, Type: {$newType}, Amount: {$newAmount}, Mode: {$newMode} ";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => [
                    'id'                => $previousData->id,
                    'name'              => $previousData->name,
                    'type'              => $previousData->type,
                    'amount'            => $previousData->amount,
                    'mode'              => $previousData->with_tax,
                    'status'            => $previousData->status,
                    'created_at'        => $previousData->created_at->format('M d, Y h:i A'),
                ],
                'audit_trail' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    public function archiveDiscount(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);

            $discount = Discount::find($id);
            if (!$discount) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.product.discount.notFound'),
                ]);
            }

            DB::beginTransaction();

            $newStatus = $discount->status == 1 ? 0 : 1;
            $discount->update([
                'status' => $newStatus,
            ]);

            $action = $newStatus == 0 ? 'archived' : 'reactivated';
            $message = "{$discount->name} has been $action.";

            $request->merge([
                'remarks' => $message,
                'type' => 2,
            ]);
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'remarks' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }
}
