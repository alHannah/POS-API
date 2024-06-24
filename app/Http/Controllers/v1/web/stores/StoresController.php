<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Models\Store;
use App\Models\Device;
use App\Models\OicPerStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StoresController extends Controller
{
    public function create_update_store(Request $request) {
        try {
            DB::beginTransaction();

            $store = Store::updateOrCreate([
                'id'=>$request->id
            ], [
                'brand_id'=>$request->brand_id,
                'store_code'=>$request->store_code,
                'store_name'=>$request->store_name,
                'store_address'=>$request->store_address,
                'group_id'=>$request->group_id,
                'vat_type'=>$request->vat_type,
                'tier_id'=>$request->price_tier,
                'pos_enabled'=>$request->pos_access,
                'status'=>1,
                'tablet_serial_no'=>$request->tablet_serial_no,
                'tin'=>$request->tin,
                'area_id'=>$request->area_id,
            ]);

            $device = Device::updateOrCreate([
                'id'=>$request->id
            ],[
                'device_id'=>$request->device_id,
                'store_id'=>$store->id,
                'status'=>"active",
            ]);

            /*$oic = OicPerStore::updateOrCreate([
                'id'=>$request->id
            ],[
                'oic'=>$request->oic,
            ]);*/

            DB::commit();

            return response()->json([
                "error"=>false,
                "message"=>trans('messages.success'),
                "data"=>$store,
                "data2"=>$device,
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"     => true,
                "message"   => trans("messages.error"),
            ]);
        }
    }

    public function delete_store_device (Request $request) {
        try {
            DB::beginTransaction();
            $store = Store::where('id', $request->id)->first()->update([
                'status'=>0,
            ]);
            $device = Device::where('store_id', $request->id)->first()->update([
                'status'=>"inactive",
            ]);


            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'     => true,
                'message'   => trans('messages.error'),
            ]);
        }
    }

    public function get_stores_devices() {
        try {
            DB::beginTransaction();
            $brandfilter = [6];
            $areafilter = [16];

            $getstores = Store::where('status', 1)->whereIn('brand_id', $brandfilter)->whereIn('area_id',$areafilter)->get();
            $storeid = $getstores->pluck('id')->toArray();
            $getdevices = Device::where('status', "active")->whereIn('store_id', $storeid)->get();



            DB::commit();
            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'storesdata' => $getstores,
                'devicesdata' => $getdevices,
            ]);


        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'     => true,
                'message'   => trans('messages.error'),
            ]);
        }
    }
}
