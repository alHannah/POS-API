<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Models\Store;
use App\Models\Device;
use App\Models\Product;
use App\Models\OicPerStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PriceTier;
use App\Models\ProductPerStore;

class StoresController extends Controller
{
    public function create_store(Request $request)
    {
        try {

            $createName = $request->name;
            if ($createName) {
                $checkDupe = Store::where('store_name',$createName)->count();

                if ($checkDupe > 0) {
                    return response()->json([
                        'error'     => true,
                        'message'   => trans('messages.store.stores.exists'),
                    ]);
                }
            }

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
                    $oic = OicPerStore::create([
                        'mobile_user_id' => $mob_id,
                        'store_id'       => $store->id,
                    ]);
                }
            } else {
                return response()->json([
                    "error"             =>true,
                    "message"           =>trans('messages.store.stores.missing_mob_id'),
                ]);
            }

            DB::commit();
            // dd($store->wasRecentlyCreated);
            if ($store->wasRecentlyCreated) {
                $message    = "Created Store: ". $request->store_name;
            }

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            return response()->json([
                "error"             =>false,
                "message"           =>trans('messages.success'),
                "data"              =>$store,
                "data2"             =>$device,
                "data3"             =>$oic,
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

            $id = $request->id;
            $storeCount = Store::where('status', 1)->where('store_name', $request->store_name)->whereNot('id', $id)->get()->count();

            if ($id) {
                $previousStore = Store::with(['store_oic','store_devices'])->where('status', 1)->where('id', $id)->get();
            } else {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.stores.notexist'),
                ]);
            }

            if ($storeCount > 0) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.stores.exists'),
                ]);
            }

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
                ]);    }

            $newStore = Store::with(['store_oic','store_devices'])->where('id',$id)->get();

            $previousStoreData = $previousStore->map(function ($items) {
                $brand     = $items->brand_id;
                $code      = $items->store_code;
                $name      = $items->store_name;
                $address   = $items->store_address;
                $vat_type  = $items->vat_type;
                $pos       = $items->pos_enabled;
                $tin       = $items->tin;
                $tablet    = $items->tablet_serial_no;
                $group     = $items->group_id;
                $area      = $items->area_id;
                $priceTier = $items->tier_id;
                $status    = $items->status;
                $device    = $items->store_devices->first()->device_id;

                return [
                    'brand'      => $brand,
                    'code'       => $code,
                    'name'       => $name,
                    'group_name' => $group,
                    'area_name'  => $area,
                    'status'     => $status,
                    'price_tier' => $priceTier,
                    'device_id'  => $device,
                    'address'    => $address,
                    'vat_type'   => $vat_type,
                    'pos'        => $pos,
                    'tin'        => $tin,
                    'tablet'     => $tablet,
                ];
            });

            $newStoreData = $newStore->map(function ($items) {
                $brand     = $items->brand_id;
                $code      = $items->store_code;
                $name      = $items->store_name;
                $group     = $items->group_id;
                $address   = $items->store_address;
                $vat_type  = $items->vat_type;
                $pos       = $items->pos_enabled;
                $tin       = $items->tin;
                $tablet    = $items->tablet_serial_no;
                $area      = $items->area_id;
                $priceTier = $items->tier_id;
                $status    = $items->status;
                $device    = $items->store_devices->first()->device_id;

                return [
                    'brand'      => $brand,
                    'code'       => $code,
                    'name'       => $name,
                    'group_name' => $group,
                    'area_name'  => $area,
                    'status'     => $status,
                    'price_tier' => $priceTier,
                    'device_id'  => $device,
                    'address'    => $address,
                    'vat_type'   => $vat_type,
                    'pos'        => $pos,
                    'tin'        => $tin,
                    'tablet'     => $tablet,
                ];
            });

            $compareStore = array_diff_assoc($newStoreData[0], $previousStoreData[0]);

            $updated = json_encode($compareStore);

            $previousStoreMobileUserId = $previousStore[0]->store_oic->pluck('mobile_user_id');

            $oldOic = OicPerStore::with('oic_mobile_user')->whereIn('mobile_user_id',$previousStoreMobileUserId)->where('store_id', $request->id)->get();

            $newOic = OicPerStore::with('oic_mobile_user')->whereIn('mobile_user_id',$request->mobile_user_id)->where('store_id', $request->id)->get();

            foreach ($newOic as $newOics) {
                $newOicName[] = $newOics->oic_mobile_user->name;
            }

            foreach ($oldOic as $oldOics) {
                $oldOicName[] = $oldOics->oic_mobile_user->name;
            }

            $newOics = collect($newOicName)->implode(', ');
            $oldOics = collect($oldOicName)->implode(', ');

            $oldOics == $newOics ? $message = "Updated the following in store: $updated":
            $message = "Updated the following in store: $updated, and OICS from $oldOics to $newOics";

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);
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
            $id = $request->id;

            if ($id) {
                $previousStore = Store::where('status',1)->where('id', $id)->first();
                $previousName = $previousStore->store_name;
            } else {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.stores.notexist'),
                ]);
            }

            DB::beginTransaction();
            $store = Store::where('id', $request->id)->first()->update([
                'status'=>0,
            ]);
            $device = Device::where('store_id', $request->id)->first()->update([
                'status'=>0,
            ]);

            $message = "$previousName has been archived.";
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

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

            $storeGroupsFilter  = $request->storeGroupsFilter;
            $areaFilter         = $request->areaFilter;
            $status             = $request->status;

            $data = Store::with([
                    'store_brands',
                    'store_per_group',
                    'store_per_area',
                    'store_per_area',
                    'store_price_tier',
                    'store_devices',
                    'store_oic',
                    ])
                ->whereIn('group_id', $storeGroupsFilter)
                ->whereIn('area_id', $areaFilter);

            if (isset($status)) {
                $data = $data->where('status', $status);
            }

            $data = $data->get();

            $tableData = collect($data)->map(function ($items) {

                    $array = $items->store_oic->pluck('mobile_user_id');
                    $oic = OicPerStore::with('oic_mobile_user')->whereIn('mobile_user_id',$array)->where('store_id', $items->id)->get();

                    foreach ($oic as $oics) {
                        $oicname[] = $oics->oic_mobile_user->name;
                    }

                    $brand     = $items->store_brands->brand;
                    $code      = $items->store_code;
                    $name      = $items->store_name;
                    $group     = $items->store_per_group->group_name;
                    $area      = $items->store_per_area->name;
                    $priceTier = $items->store_price_tier->name;
                    $oicName   = collect($oicname)->implode(', ');
                    $status    = $items->status;
                    $device    = $items->store_devices->first()->device_id;

                    return [
                        'brand'      => $brand,
                        'code'       => $code,
                        'name'       => $name,
                        'group_name' => $group,
                        'area_name'  => $area,
                        'status'     => $status,
                        'oic'        => $oicName,
                        'price_tier' => $priceTier,
                        'device_id'  => $device,
                    ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableData,
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

    public function show_product(Request $request) {
        try {

            DB::beginTransaction();

            $storeFilter = $request->storeId;

            $storeData = Store::with('store_product_per_store')->where('id',$storeFilter)->where('status', 1)->first();
            $productId = $storeData->store_product_per_store->pluck('product_id');
            $products = Product::with('product_category')->whereIn('id',$productId)->get();

            $tableData = $products->map(function ($items) {

                $productCode = $items->product_code;
                $productName = $items->name;
                $category    = $items->product_category->name;
                $price       = 100;
                $status      = $items->status;

                return [
                    "product_code" => $productCode,
                    "product_name" => $productName,
                    "category"     => $category,
                    "price"        => $price,
                    "status"       => $status,
                ];
            });

            DB::commit();
            return response()->json([
                'error'             => false,
                'message'           => trans('messages.success'),
                'data'              => $tableData,
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

    public function add_product(Request $request) {
        try {
            DB::beginTransaction();

            $storeFilter = $request->storeId;

            $storeData = Store::with('store_product_per_store')->where('id',$storeFilter)->where('status', 1)->first();
            $productId = $storeData->store_product_per_store->pluck('product_id');
            $productData = Product::with('product_category')->whereNotIn('id', $productId)->where('brand_id', $storeData->brand_id)
            ->where('product_tag','s')->get();

            $tableData = $productData->map(function ($items) {

                $productCode = $items->product_code;
                $productName = $items->name;
                $category    = $items->product_category->name;
                $price       = 100;
                $status      = $items->status;

                return [
                    "product_code" => $productCode,
                    "product_name" => $productName,
                    "category"     => $category,
                    "price"        => $price,
                    "status"       => $status,
                ];
            });

            $productFilter = $request->product_Id;

            foreach($productFilter as $productId) {
                $productDatas = $productData->where('id', $productId)->first();
                $productNames[] = $productDatas->name;
                $storeFilter;
                $hey = ProductPerStore::create([
                    "product_id" => $productDatas->id,
                    "store_id"   => $storeFilter,
                    "status"     => 1,
                ]);
            }

            DB::commit();

            $message = "$productNames has been added to $storeData->store_name";
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

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

    public function activate_product(Request $request) {
        try {
            DB::beginTransaction();

            $storeFilter = $request->storeId;
            $productId = $request->product_Id;

            $storeData = Store::where('id',$storeFilter)->where('status', 1)->first();
            $storeName = $storeData->store_name;
            $product = ProductPerStore::with('products_product_per_store')->where('product_id', $productId)
            ->where('store_id',$storeFilter)->first();
            $productName = $product->products_product_per_store->name;

            $product->update([
                'status' => 0,
            ]);

            DB::commit();

            $message = "$productName in $storeName has been deactivated";
            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

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
}
