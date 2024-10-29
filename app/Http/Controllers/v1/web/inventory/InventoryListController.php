<?php

namespace App\Http\Controllers\v1\web\inventory;

use App\Http\Controllers\Controller;
use App\Models\BrandAssignment;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\MobileUser;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class InventoryListController extends Controller
{
    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            $userId                     = $request->user_id;

            $brand      = BrandAssignment::where('user_id', $userId)->pluck('brand_id');
            $storeIds   = Store::whereIn('brand_id', $brand)->pluck('id');
            $data       = Inventory::with([
                'inventory_stores',
                'inventory_details'
            ])->whereIn('store_id', $storeIds)
                ->orderBy('store_id', 'desc');

            if ($request->has('storeGroupsFilter') || $request->has('storeFilter') || $request->has('warehouseFilter')) {
                if ($request->has('storeGroupsFilter')) {
                    $storeGroupsFilter = $request->storeGroupsFilter;
                    $data->whereHas('inventory_stores', function ($item) use ($storeGroupsFilter) {
                        $item->whereIn('group_id', $storeGroupsFilter);
                    });
                }

                if ($request->has('warehouseFilter')) {
                    $warehouseFilter = $request->warehouseFilter;
                    $data->whereHas('inventory_details', function ($item) use ($warehouseFilter) {
                        $item->whereIn('product_id', $warehouseFilter);
                    });
                }

                if ($request->has('storeFilter')) {
                    $storeFilter = $request->storeFilter;
                    $data->whereIn('store_id', $storeFilter);
                }
            }

            $data       = $data->get();

            $tableData  = $data->map(function ($item) {
                $storeGroup     = StoreGroup::find($item->inventory_stores->group_id)->group_name;
                $inventoryDate  = $item->date_created instanceof Carbon ? $item->date_created : Carbon::parse($item->date_created);
                return [
                    'inventory_id'      => $item->id,
                    'store_id'          => $item->store_id,
                    'store_name'        => $item->inventory_stores->store_name,
                    'store_code'        => $item->inventory_stores->store_code,
                    'store_group'       => $storeGroup,
                    'inventory_date'    => $inventoryDate->format("M d, Y"),
                ];
            });

            DB::commit();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $tableData,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }
    public function view(Request $request)
    {
        try {
            DB::beginTransaction();

            $id     = $request->inventoryID;
            $data   = Inventory::with([
                'inventory_stores',
                'inventory_details'
            ])
                ->where('id', $id)
                ->get();

            $tableData = $data->map(function ($item) {
                $storeGroup = StoreGroup::find($item->inventory_stores->group_id)->group_name;
                $details    = $item->inventory_details->map(function ($detail) {
                    return [
                        'product_id'      => $detail->product_id,
                        'inventory_id'    => $detail->inventory_id
                    ];
                });
                $productIds     = $details->pluck('product_id');
                $inventoryId    = $details->pluck('inventory_id');


                $inventoryDate      = $item->date_created instanceof Carbon ? $item->date_created : Carbon::parse($item->date_created);
                $inventoryDetails   = InventoryDetail::with(['inventoryDetails_uom', 'inventoryDetails_product'])
                    ->whereIn('inventory_id', $inventoryId)
                    ->whereIn('product_id', $productIds)
                    ->get();
                $productInventoryDetails = $inventoryDetails->map(function ($data) {
                    $categoryId     = Product::find($data->product_id)->category_id;
                    $categoryName   = Category::find($categoryId)->name;
                    return [
                        'product_id'            => $data->product_id,
                        'product_code'          => $data->inventoryDetails_product->product_code,
                        'product_name'          => $data->inventoryDetails_product->name,
                        'inventory_Category'    => $categoryName,
                        'beginning'             => $data->qty,
                        'delivered'             => $data->delivered,
                        'usage'                 => $data->usage,
                        'pullouts'              => $data->pullouts,
                        'ending'                => $data->ending,
                        'uom'                   => $data->inventoryDetails_uom->name,
                        'product_tag'           => $data->inventoryDetails_product->product_tag,
                    ];
                });

                return [
                    'user_id'                   => $item->user_id,
                    'store_id'                  => $item->store_id,
                    'store_name'                => $item->inventory_stores->store_name,
                    'store_code'                => $item->inventory_stores->store_code,
                    'store_group'               => $storeGroup,
                    'inventory_date'            => $inventoryDate->format("M d, Y"),
                    'last_update'               => $item->updated_at->format("M d, Y h:i A"),
                    'Product_Inventory_Details' => $productInventoryDetails
                ];
            });

            DB::commit();

            return response()->json([
                "error"         => false,
                "message"       => trans('messages.success'),
                "data"          => $tableData,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::info("Error: $e");
            return response()->json([
                "error"         => true,
                "message"       => trans("messages.error"),
            ]);
        }
    }
}
