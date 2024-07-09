<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use App\Models\Store;
use App\Models\Device;
use App\Models\Product;
use App\Models\Category;
use App\Models\PriceTier;
use App\Models\OicPerStore;
use Illuminate\Http\Request;
use App\Models\ProductPerStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ManagerInCharge;
use Illuminate\Support\Facades\Crypt;

class StoresController extends Controller
{
    public function create_store(Request $request) {
        try {

            $createName = $request->name;
            if ($createName) {
                $checkDupe = Store::where('store_name', $createName)->count();

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
                    "error"             => true,
                    "message"           => trans('messages.store.stores.missing_mob_id'),
                ]);
            }

            if ($request->has('user_id')) {
                $manager = ManagerInCharge::create([
                    'store_id' => $store->id,
                    'user_id'  => $request->user_id,
                ]);
            } else {
                return response()->json([
                    "error"             => true,
                    "message"           => trans('messages.store.stores.missing_manager_id'),
                ]);
            }

            DB::commit();

            if ($store->wasRecentlyCreated) {
                $message    = "Created Store: " . $request->store_name;
            }

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

    public function update_store(Request $request) {
        try {
            $id = Crypt::decrypt($request->id);

            $storeCount = Store::where('status', 1)->where('store_name', $request->store_name)->whereNot('id', $id)->get()->count();

            if ($id) {
                $previousStore = Store::with(['store_oic', 'store_devices'])->where('status', 1)->where('id', $id)->get();

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

            $previousStoreMobileUserId = $previousStore[0]->store_oic->pluck('mobile_user_id');
            $oldOic = OicPerStore::with('oic_mobile_user')->whereIn('mobile_user_id', $previousStoreMobileUserId)->where('store_id', $id)->get();

            DB::beginTransaction();

            $store =  Store::where('id', $id)->update([
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

            $manager = ManagerInCharge::with('manager_user')->where('store_id', $id)->first();
            $previousManager = $manager->manager_user->name;
            $newManager = ManagerInCharge::with('manager_user')->where('user_id',$request->user_id)->first()->manager_user->name;
            $manager->update(['user_id' => $request->user_id]);

            Device::where('store_id', $id)->update([
                'device_id' => $request->device_id
            ]);

            $mobile_user_id = $request->mobile_user_id;

            OicPerStore::where('store_id', $id)->delete();

            foreach ($mobile_user_id as $mob_id) {
                OicPerStore::create([
                    'mobile_user_id' => $mob_id,
                    'store_id'       => $id
                ]);
            }

            $newStore = Store::with(['store_oic', 'store_devices'])->where('id', $id)->get();

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

            $newOic = OicPerStore::with('oic_mobile_user')->whereIn('mobile_user_id', $request->mobile_user_id)->where('store_id', $id)->get();

            foreach ($newOic as $newOics) {
                $newOicName[] = $newOics->oic_mobile_user->name;
            }

            foreach ($oldOic as $oldOics) {
                $oldOicName[] = $oldOics->oic_mobile_user->name;
            }

            $newOics = collect($newOicName)->implode(', ');
            $oldOics = collect($oldOicName)->implode(', ');

            $oldOics == $newOics ? $message = "Updated the following in store: $updated" :
                $message = "Updated the following in store: $updated, and OICS from $oldOics to $newOics";

            $previousManager == $newManager ? $message = $message :
                $message = $message." and manager from $previousManager to $newManager";

            $request["remarks"] = $message;
            $request["type"] = 2;
            $this->audit_trail($request);

            DB::commit();

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

    public function archive_store_device(Request $request) {
        try {
            $id = Crypt::decrypt($request->id);

            if ($id) {
                $store = Store::where('id', $id)->first();
                $previousName = $store->store_name;
            } else {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.store.stores.notexist'),
                ]);
            }

            DB::beginTransaction();

            if($store->status==1) {
                $store = Store::where('id', $id)->first()->update([
                    'status' => 0,
                ]);
                $device = Device::where('store_id', $id)->first()->update([
                    'status' => 0,
                ]);
                $message = "$previousName has been archived.";

            } else {
                $store = Store::where('id', $id)->first()->update([
                    'status' => 1,
                ]);
                $device = Device::where('store_id', $id)->first()->update([
                    'status' => 1,
                ]);
                $message = "$previousName has been reactivated.";
            }

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

            $brandFilter        = $request->brandFilter;
            $storeGroupsFilter  = $request->storeGroupsFilter;
            $areaFilter         = $request->areaFilter;
            $statusFilter       = $request->statusFilter;

            $data = Store::with([
                'store_brands',
                'store_storeGroup',
                'store_per_area',
                'store_per_area',
                'store_price_tier',
                'store_devices',
                'store_oic',
            ])
                ->whereIn('group_id', $storeGroupsFilter)
                ->whereIn('area_id', $areaFilter);
                //->whereIn('status', $statusFilter);

            $data = $data->get();

            $tableData = collect($data)->map(function ($items) {

             $array = $items->store_oic->pluck('mobile_user_id');
                $oic = OicPerStore::with('oic_mobile_user')
                    ->whereIn('mobile_user_id', $array)
                    ->where('store_id', $items->id)->get();

                foreach ($oic as $oics) {
                    $oicname[] = $oics->oic_mobile_user->name;
                }

                $mobileUserIdArray = [];
                foreach ($items->store_oic as $oic) {
                    array_push($mobileUserIdArray, $oic->mobile_user_id);
                }

                $brand       = $items->store_brands->brand;
                $code        = $items->store_code;
                $name        = $items->store_name;
                $group       = $items->store_storeGroup->group_name;
                $area        = $items->store_per_area->name;
                $priceTier   = $items->store_price_tier->name;
                $status      = $items->status;
                $device      = $items->store_devices->first()->device_id;
                $encryptedId = Crypt::encrypt($items->id);

                return [
                    'brand'          => $brand,
                    'code'           => $code,
                    'name'           => $name,
                    'group_name'     => $group,
                    'area_name'      => $area,
                    'status'         => $status,
                    'oic'            => $oicname,
                    'price_tier'     => $priceTier,
                    'device_id'      => $device,
                    'id'             => $items->id,
                    'encrypted_id'   => $encryptedId,
                    'mobile_user_id' => $mobileUserIdArray,
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

    public function edit_store($id) {
        try {
            DB::beginTransaction();

            $storeId = Crypt::decrypt($id);

            $storeData = Store::with([
                'store_brands',
                'store_storeGroup',
                'store_per_area',
                'store_per_area',
                'store_price_tier',
                'store_devices',
                'store_oic',
            ])
                ->where('id', $storeId);

            if (isset($status)) {
                $storeData = $storeData->where('status', $status);
            }

            $storeData = $storeData->get();

            $tableData = collect($storeData)->map(function ($items) {

                $manager = ManagerInCharge::with('manager_user')->where('store_id', $items->id)->first();
                $managerName = $manager->manager_user->name;
                $array = $items->store_oic->pluck('mobile_user_id');
                $oic = OicPerStore::with('oic_mobile_user')
                    ->whereIn('mobile_user_id', $array)
                    ->where('store_id', $items->id)->get();

                foreach ($oic as $oics) {
                    $oicname[] = $oics->oic_mobile_user->name;
                }

                $mobileUserIdArray = [];
                foreach ($items->store_oic as $oic) {
                    array_push($mobileUserIdArray, $oic->mobile_user_id);
                }

                $brand       = $items->store_brands->brand;
                $code        = $items->store_code;
                $name        = $items->store_name;
                $group       = $items->store_storeGroup->group_name;
                $area        = $items->store_per_area->name;
                $priceTier   = $items->store_price_tier->name;
                $status      = $items->status;
                $device      = $items->store_devices->first()->device_id;
                $encryptedId = Crypt::encrypt($items->id);

                return [
                    'brand'          => $brand,
                    'code'           => $code,
                    'name'           => $name,
                    'group_name'     => $group,
                    'area_name'      => $area,
                    'manager_name'   => $managerName,
                    'status'         => $status,
                    'oic'            => $oicname,
                    'price_tier'     => $priceTier,
                    'device_id'      => $device,
                    'encrypted_id'   => $encryptedId,
                    'mobile_user_id' => $mobileUserIdArray,
                    'group_id'       => $items->group_id,
                    'area_id'        => $items->area_id,
                    'tier_id'        => $items->tier_id,
                    'brand_id'       => $items->brand_id,
                    'user_id'        => $manager->user_id,
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

    public function show_product($id) {
        try {

            DB::beginTransaction();

            $storeFilter = Crypt::decrypt($id,);

            $storeData = Store::with('store_product_per_store')->where('id', $storeFilter)->where('status', 1)->first();
            $productId = $storeData->store_product_per_store->pluck('product_id');
            $products = Product::with('product_category')->whereIn('id', $productId)->get();

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
                    "product_id"   => $items->id,

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

    public function add_product($id) {
        try {
            DB::beginTransaction();

            $storeFilter = decrypt($id);

            $storeData = Store::with('store_product_per_store')->where('id', $storeFilter)->where('status', 1)->first();
            $productId = $storeData->store_product_per_store->pluck('product_id');
            $productData = Product::with('product_category')->whereNotIn('id', $productId)->where('brand_id', $storeData->brand_id)
                ->where('product_tag', 's')->get();

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
                    "product_id"   => $items->id,
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

    public function save_product(Request $request) {
        try {
            DB::beginTransaction();

            $productId = $request->product_Id;
            $productData = Product::whereIn('id', $productId)->get();
            $storeData = ProductPerStore::with('store_product_per_store')->whereIn('product_id', $productId)->first();
            $storeName = $storeData->store_product_per_store->store_name;

            foreach ($productId as $productIds) {
                $productDatas = $productData->where('id', $productIds)->first();
                $productNames[] = $productDatas->name;
                $storeId = $storeData->store_product_per_store->id;
                $hey = ProductPerStore::create([
                    "product_id" => $productDatas->id,
                    "store_id"   => $storeId,
                    "status"     => 1,
                ]);
            }

            DB::commit();

            $message = "$productNames has been added to $storeName";
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

            $storeData = Store::where('id', $storeFilter)->first();
            $storeName = $storeData->store_name;
            $product = ProductPerStore::with('products_product_per_store')->where('product_id', $productId)
                ->where('store_id', $storeFilter)->first();
            $productName = $product->products_product_per_store->name;

            if($product->status == 1) {
                $product->update([
                    'status' => 0,
                ]);
                $message = "$productName in $storeName has been deactivated";
            } else {
                $product->update([
                    'status' => 1,
                ]);
                $message = "$productName in $storeName has been activated";
            }

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
}
