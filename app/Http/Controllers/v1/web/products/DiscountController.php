<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Discount;
use App\Models\PosCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $name         = $request->name;
            $type         = $request->type;
            $amount       = $request->amount;
            $with_tax     = $request->with_tax;

            $validator = Validator::make($request->all(), [
                'name'          => 'required',
                'type'          => 'required',
                'amount'        => 'required',
                'with_tax'      => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => trans('messages.required')
                ]);
            }

            $existingDis = Discount::where('name','=', $name)
                         ->where('type', '=',$type)
                         ->where('amount','=', $amount)
                         ->where('with_tax','=', $with_tax)
                         ->where('status','=', 1)
                         ->first();

            if ($existingDis) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.discount.existed'),
                ]);
            }

            $status = 1;

            $discount = Discount::create([
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


    public function displayDiscount(Request $request)
    {
        try {
            $statusFilter = (array) $request->statusFilter;
            $modeFilter  = (array) $request->modeFilter;
            $typeFilter  = (array) $request->typeFilter;

            $statusFilter = Arr::flatten($statusFilter, 1);
            $modeFilter  = Arr::flatten($modeFilter, 1);
            $typeFilter  = Arr::flatten($typeFilter, 1);

            $query = Discount::select('id', 'name');

            if (!empty($modeFilter)) {
                $query->whereIn('with_tax', $modeFilter);
            }

            if (!empty($statusFilter)) {
                $query->whereIn('status', $statusFilter);
            }

            if (!empty($typeFilter)) {
                $query->whereIn('type', $typeFilter);
            }

            $data = $query->orderByDesc('created_at')
                ->select(['id', 'name', 'type', 'amount', 'with_tax', 'status', 'created_at'])
                ->get()
                ->map(function ($item) {
                    return [
                        'id'            => Crypt::encrypt($item->brand_id),
                        'discount_name' => $item->name,
                        'type'          => $item->type,
                        'amount'        => $item->amount,
                        'mode'          => $item->with_tax,
                        'status'        => $item->status,
                        'created_at'    => Carbon::parse($item->created_at)->format('M d, Y h:i A'),
                        'id_nE'         => $item->id,
                    ];
                });

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            Log::error("Error: $e");
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
