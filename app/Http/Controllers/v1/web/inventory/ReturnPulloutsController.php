<?php

namespace App\Http\Controllers\v1\web\inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ReturnPullout;
use App\Models\ReturnPulloutDetail;
use App\Models\Store;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ReturnPulloutsController extends Controller
{
    public function getReturnPullouts(Request $request)
    {
        try {
            $statusFilter = Arr::flatten((array) $request->statusFilter);
            $storeGroupFilter = Arr::flatten((array) $request->storeGroupFilter);
            $storeFilter = Arr::flatten((array) $request->storeFilter);
            $dateFrom = $request->dateFrom;
            $dateTo = $request->dateTo;

            if (!empty($dateFrom)) {
                $dateFrom = Carbon::createFromFormat('d/m/Y', $dateFrom)->format('Y-m-d');
            }
            if (!empty($dateTo)) {
                $dateTo = Carbon::createFromFormat('d/m/Y', $dateTo)->format('Y-m-d');
            }

            $query = ReturnPullout::with(['returnPullout_stores', 'returnPullout_details']);

            if (!empty($statusFilter)) {
                $query->whereIn('status', $statusFilter);
            }

            if (!empty($storeFilter)) {
                $query->whereIn('store_id', $storeFilter);
            }

            if (!empty($storeGroupFilter)) {
                $storeIds = Store::whereIn('group_id', $storeGroupFilter)->pluck('id');
                $query->whereIn('store_id', $storeIds);
            }

            if (!empty($dateFrom)) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if (!empty($dateTo)) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $data = $query->orderByDesc('created_at')
                ->select(['id', 'ref_no', 'status', 'created_at', 'store_id'])
                ->get()
                ->map(function ($item) {
                    $store = $item->returnPullout_stores->first();
                    $storeGroup = $store->store_storeGroup->first();
                    return [
                        'id'            => Crypt::encrypt($item->id),
                        'status'        => $item->status,
                        'ref_no'        => $item->ref_no,
                        'store_name'    => $store->store_name,
                        'store_group'   => $storeGroup->group_name,
                        'created_at'    => Carbon::parse($item->created_at)->format('M d, Y h:i A'),
                    ];
                });

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            Log::error("Error: {$e->getMessage()}");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    public function displayDetails(Request $request)
    {
        try {
            $returnFilter = Arr::flatten((array) $request->returnFilter, 1);

            $query = ReturnPulloutDetail::with(['details_returnPullouts','details_products','details_uom']);

            if (!empty($returnFilter)) {
                $query->whereIn('return_id', $returnFilter);
            }

            $data = $query->select(['id', 'return_id', 'product_id', 'uom_id', 'qty', 'status', 'remarks','created_at'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($item) {
                    $details = ReturnPullout::with(['returnPullout_stores','returnPullout_users','returnPullout_mobileUsers'])->find($item->return_id);
                    return [
                        'ref_no'                => $item->details_returnPullouts->ref_no,
                        'product_id'            => $item->product_id,
                        'product_name'          => $item->details_products->name,
                        'qty'                   => $item->qty,
                        'uom'                   => $item->details_uom->name,
                        'remarks'               => $item->remarks,
                        'store_code'            => $details->returnPullout_stores->store_code,
                        'store_name'            => $details->returnPullout_stores->store_name,
                        'created_by'            => $details->returnPullout_mobileUsers->name,
                        'approved_by'           => $details->returnPullout_users->name,
                        'status'                => $item->status,
                        'approved_date'         => Carbon::parse($item->details_returnPullouts->approved_date)->format('M d, Y h:i A'),
                        'created_at'            => Carbon::parse($item->created_at)->format('M d, Y h:i A'),
                    ];
                });

            return response()->json([
                'error'   => false,
                'message' => trans('messages.success'),
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            Log::info("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

}
