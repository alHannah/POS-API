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
    public function create_store(Request $request) 
    {
        try {
            DB::beginTransaction();

            $store =  Store::create([
                'brand_id'          => $request->brand_id,
                'store_code'        => $request->store_code,
                'store_name'        => $request->store_name,
                'store_address'     => $request->store_address,
                'group_id'          => $request->group_id,
                'vat_type'          => $request->vat_type,
                'tier_id'           => $request->tier_id,
                'pos_enabled'       => $request->pos_access,
                'status'            => 1,
                'tablet_serial_no'  => $request->tablet_serial_no,
                'tin'               => $request->tin,
                'area_id'           => $request->area_id,
            ]);

            $device = Device::create([
                'device_id'         => $request->device_id,
                'store_id'          => $store->id,
                'status'            => 1,
            ]);

            if ($request->has('mobile_user_id')) {
                foreach ($request->mobile_user_id as $mob_id) {
                    OicPerStore::create([
                        'mobile_user_id' => $mob_id,
                        'store_id'       => $store->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                "error"             =>false,
                "message"           =>trans('messages.success'),
                "data"              =>$store,
                "data2"             =>$device,
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

    public function update_store(Request $request) 
    {
        try {
            DB::beginTransaction();

            $store =  Store::where('id', $request->id)->update([
                'brand_id'          => $request->brand_id,
                'store_code'        => $request->store_code,
                'store_name'        => $request->store_name,
                'store_address'     => $request->store_address,
                'group_id'          => $request->group_id,
                'vat_type'          => $request->vat_type,
                'tier_id'           => $request->tier_id,
                'pos_enabled'       => $request->pos_access,
                'status'            => 1,
                'tablet_serial_no'  => $request->tablet_serial_no,
                'tin'               => $request->tin,
                'area_id'           => $request->area_id,
            ]);

            Device::where('store_id', $request->id)->update([
                'device_id' => $request->device_id
            ]);

            $mobile_user_id = $request->mobile_user_id;

            OicPerStore::whereNotIn('mobile_user_id', $mobile_user_id)
                ->where('store_id', $request->id)->delete();

            foreach ($mobile_user_id as $mob_id) {
                OicPerStore::create([
                    'mobile_user_id' => $mob_id,
                    'store_id'       => $request->id
                ]);
            }
         

            DB::commit();

            return response()->json([
                "error"             =>false,
                "message"           =>trans('messages.success'),
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

    public function delete_store_device (Request $request) 
    {
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
                'error'             => false,
                'message'           => trans('messages.success'),
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

    public function get_stores_devices(Request $request) {
        try {
            DB::beginTransaction();
            // $brandfilter = $request->brandfilter;
            // $areafilter  = $request->areafilter;

            // $getstores = Store::where('status', 1)->whereIn('brand_id', $brandfilter)->whereIn('area_id',$areafilter)->get();
            // $storeid = $getstores->pluck('id')->toArray();
            // $getdevices = Device::where('status', 1)->whereIn('store_id', $storeid)->get();

            $storeGroupsFilter  = $request->storeGroupsFilter;
            $areaFilter         = $request->areaFilter;
            $status             = $request->status;

            $data = Store::with([
                    'store_brands',
                    'store_per_group',
                    'store_per_area',
                    ])
                ->whereIn('group_id', $storeGroupsFilter)
                ->whereIn('area_id', $areaFilter);

            if (isset($status)) {
                $data = $data->where('status', $status);
            }

            $data = $data->get();

            $tableData = $data->map(function ($items) {
                $brand = $items->store_brands->brand;
                $code  = $items->store_code;
                $name  = $items->store_name;

                return [
                    'brand' => $brand,
                    'code'  => $code,
                    'name'  => $name
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                // 'storesdata'        => $getstores,
                // 'devicesdata'       => $getdevices,
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
