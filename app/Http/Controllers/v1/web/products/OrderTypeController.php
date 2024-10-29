<?php

namespace App\Http\Controllers\v1\web\products;

use Exception;
use App\Models\OrderType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class OrderTypeController extends Controller
{
    public function create_type(Request $request) {
        try {

            $orderNames = OrderType::pluck('name');
            foreach ($orderNames as $name) {
                if ($name == $request->name) {
                    return response()->json([
                        "error"             => true,
                        "message"           => trans('messages.product.orderType.exists'),
                    ]);
                }
            }

            DB::beginTransaction();

            OrderType::create([
                'name'          => $request->name,
                'set_default'   => 0,
                'status'        => 1,
            ]);

            DB::commit();

            $message    = "Created Order Type: " . $request->name;
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

    public function edit_type(Request $request) {
        try {

            $orderCount = OrderType::where('name',$request->name)->whereNot('id',$request->id)->count();

            if ($orderCount > 0)  {
                return response()->json([
                    "error"             => true,
                    "message"           => trans('messages.product.orderType.exists'),
                ]);
            }

            DB::beginTransaction();

            OrderType::where('status', 1)->where('id', $request->id)->update([
                'name'   => $request->name,
            ]);

            DB::commit();

            $message    = "Updated Order Type: " . $request->name;
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

    public function get_type(Request $request) {
        try {
            DB::beginTransaction();

            $statusFilter = $request->status;

            // if ($statusFilter == 0) {
            //     $orderData = OrderType::where('status', 0)->get();
            // } elseif ($statusFilter == 1) {
            //     $orderData = OrderType::where('status', 1)->get();
            // } else {
            //     $orderData = OrderType::get();
            // }
            $orderData = OrderType::whereIn('status', $statusFilter)->get();

            $tableData = $orderData->map(function ($items) {
                return [
                    'id'            => $items->id,
                    'is_default'    => $items->is_default,
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

    public function archive_type(Request $request) {
        try {
            DB::beginTransaction();
            $orderData = OrderType::find($request->id);

            if($orderData->status == 1) {
                OrderType::where('id',$request->id)->update([
                    'status'    => 0,
                ]);
                $message    = "Archived Order Type: " . $orderData->name;
            } else {
                OrderType::where('id',$request->id)->update([
                    'status'    => 1,
                ]);
                $message    = "Activated Order Type: " . $orderData->name;
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

    public function set_default(Request $request) {
        try {
            DB::beginTransaction();

            $orderData = OrderType::where('status', 1)->find($request->id);
            OrderType::whereNot('id', $request->id)->update([
                'is_default'   => 0,
            ]);

            $orderData->update([
                'is_default'   => 1,
            ]);

            DB::commit();

            $message    = "Set default Order Type " . $orderData->name;
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
