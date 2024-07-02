<?php

namespace App\Http\Controllers\v1\web\products;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ModeOfPayment;
use Illuminate\Http\Request;


class ModeOfPaymentController extends Controller
{
    public function create(Request $request)
    {
        try{
            DB::beginTransaction();

            $createMop = ModeOfPayment::create([
                'name'      => $request->name,
            ]);

            if ($createMop->wasRecentlyCreated) {
                $message    = "Created MOP: '$request->name'";
            }

            DB::commit();

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $createMop,
                //'audit_trail'       => $message
            ]);

        }catch (Exception $e) {
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
        try{
            DB::beginTransaction();

            $mopName = ModeOfPayment::find($request->id);

            DB::commit();

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $mopName,
            ]);
        }catch (Exception $e) {
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
        try{
            DB::beginTransaction();

            $previousName = ModeOfPayment::find($request->id);
            $updateMop = ModeOfPayment::where('id', $request->id)->update([
                'name' => $request->name
            ]);

            DB::commit();

            $message    = "Updated MOP: '$previousName->name' change into $request->name";
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);


            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $updateMop,
                //'audit_trail'       =>$message
            ]);
        }catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }

    public function archived(Request $request)
    {
        try{
            DB::beginTransaction();

            $mopStatus = ModeOfPayment::find($request->id);

            if($mopStatus->status==1){
                $mopArchived = ModeOfPayment::where('id',$request->id)->update([
                    'status' => 0
                ]);
                $message = "Archived MOP: '$mopStatus->name' archived";
            } else {
                $mopArchived = ModeOfPayment::where('id',$request->id)->update([
                    'status' => 1
                ]);
                $message = "Archived MOP: '$mopStatus->name' reactivated";
            }

            DB::commit();

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $mopArchived,
                //'audit_trail'       => $message
            ]);
        }catch (Exception $e) {
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
        try{
            DB::beginTransaction();

            $mopDetails = ModeOfPayment::get();

            $tableDetails = $mopDetails->map(function($item){
                return[
                    'id'            => $item->id,
                    'name'          => $item->name,
                    'created_at'    => $item->created_at,
                    'status'        => $item->status,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableDetails,
            ]);
        }catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'             => true,
                'message'           => trans('messages.error'),
            ]);
        }
    }
}
