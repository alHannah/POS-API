<?php

namespace App\Http\Controllers\v1\web\products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Discount;
use App\Models\PosCategory;
use App\Models\PricePerTier;
use App\Models\PriceTier;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PriceTierController extends Controller
{
    public function create(Request $request) //done
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name'         => 'required',
                'brand_id'     => 'required',
                'mop_id'       => 'required',
                'price'        => 'required',
                'product_name' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error'   => true,
                    'message' => $validator->errors()->first(),
                ]);
            }

            $existingPT = PriceTier::where('name', $request->name)
                ->where('brand_id', $request->brand_id)
                ->where('mop_id', $request->mop_id)
                ->first();

            if ($existingPT) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.priceTier.existed'),
                ]);
            }

            $priceTier = PriceTier::create([
                'name'          => $request->name,
                'status'        => 1,
                'sales_channel' => 1,
                'brand_id'      => $request->brand_id,
                'mop_id'        => $request->mop_id,
            ]);

            $priceTier->load('price_tier_per_mop:id,name', 'price_tier_per_brand:id,brand');

            $MOP = $priceTier->price_tier_per_mop->name ?? 'N/A';
            $brand = $priceTier->price_tier_per_brand->brand ?? 'N/A';

            $product = Product::where('name', $request->product_name)->first();
            $product_id = $product ? $product->id : null;

            $pricePerTier = PricePerTier::create([
                'product_id' => $product_id,
                'tier_id'    => $priceTier->id,
                'price'      => $request->price,
            ]);

            $pricePerTier->load('price_per_tier_per_product:id,name,product_code');
            $product_code = $pricePerTier->price_per_tier_per_product->product_code ?? 'N/A';

            $message = "New Price Tiers => Name: {$priceTier->name} | Status: 1 | Sales Channel: 1 | MOP: {$MOP} | Brand: {$brand}"
                . " | Code: {$product_code} | Name: {$request->product_name} | Price: {$pricePerTier->price}";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => [
                    'id'            => $priceTier->id,
                    'name'          => $priceTier->name,
                    'status'        => $priceTier->status,
                    'sales_channel' => $priceTier->sales_channel,
                    'MOP'           => $MOP,
                    'Brand'         => $brand,
                    'product_code'  => $product_code,
                    'product_name'  => $request->product_name,
                    'price'         => $pricePerTier->price,
                    'created_at'    => $priceTier->created_at->format('M d, Y h:i A'),
                ],
                'audit_trail' => $message,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    public function displayPriceTier(Request $request) //done
    {
        try {
            DB::beginTransaction();

            $data = PriceTier::with('price_tier_per_brand:id,brand')
                ->orderByDesc('created_at')
                ->get(['id', 'name', 'status', 'created_at', 'brand_id'])
                ->map(function ($item) {
                    return [
                        'id'         => Crypt::encryptString($item->id),
                        'brand'      => optional($item->price_tier_per_brand)->brand,
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
            Log::error("Error: {$e->getMessage()}");
            return response()->json([
                'error'   => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    public function displayTierProduct(Request $request) //done
    {
        try {
            $brandFilter = Arr::flatten((array) $request->brandFilter, 1);

            $query = Product::with(['product_per_brand:id,brand', 'product_per_price_per_tiers:product_id,price']);

            if (!empty($brandFilter)) {
                $query->whereIn('brand_id', $brandFilter);
            }

            $data = $query->orderByDesc('created_at')
                ->select(['id', 'brand_id', 'product_code', 'name', 'created_at'])
                ->get()
                ->map(function ($item) {
                    return [
                        'brand_id'      => Crypt::encrypt($item->brand_id),
                        'product_code'  => $item->product_code,
                        'name'          => $item->name,
                        'price'         => optional($item->product_per_price_per_tiers->first())->price,
                        'created_at'    => Carbon::parse($item->created_at)->format('M d, Y h:i A'),
                        'brand_id_nE'   => $item->brand_id,
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

    public function update(Request $request) //done
    {
        try {
            DB::beginTransaction();

            /*id
            name
            mop_id
            brand_id
            price
            product_name
            */

            $encryptedId = $request->id ? Crypt::decrypt($request->id) : null;

            if (!$encryptedId) {
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.required')
                ]);
            }

            $previousData = PriceTier::find($encryptedId);

            if (!$previousData) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.priceTier.notFound')
                ]);
            }

            $name = $request->name;
            $mop_id = $request->mop_id;
            $brand_id = $request->brand_id;

            $dataOld = PriceTier::where('name', $name)->first();

            if ($dataOld) {
                $dataOld->load('price_tier_per_mop:id,name', 'price_tier_per_brand:id,brand');
            }

            $previousMOP = $dataOld->price_tier_per_mop->name ?? 'N/A';
            $previousBrand = $dataOld->price_tier_per_brand->brand ?? 'N/A';
            $previousName = $previousData->name;

            $previousData->update([
                'name'     => $name,
                'mop_id'   => $mop_id,
                'brand_id' => $brand_id,
            ]);

            $dataNew = PriceTier::where('name', $name)->first();

            if ($dataNew) {
                $dataNew->load('price_tier_per_mop:id,name', 'price_tier_per_brand:id,brand');
            }

            $newMOP = $dataNew->price_tier_per_mop->name ?? 'N/A';
            $newBrand = $dataNew->price_tier_per_brand->brand ?? 'N/A';
            $newName = $request->name;

            $product = Product::where('name', $request->product_name)->first();
            $product_id = $product ? $product->id : null;
            $product_name = $product ? $product->name : null;
            $product_code = $product ? $product->product_code : null;

            $product = PricePerTier::where('product_id', $product_id)->first();
            $oldPrice = $product ? $product->price : null;

            $previousProduct = PricePerTier::where('product_id', $product_id)->get();
            $previousProductPrice = null;
            if ($previousProduct->isNotEmpty()) {
                foreach ($previousProduct as $product) {
                    $previousProductPrice = $product->price;
                    $product->update([
                        'price' => $request->price,
                    ]);
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => trans('messages.product.priceTier.notFound')
                ]);
            }

            $message = "Updated Price Tier -> Name: {$previousName}, MOP: {$previousMOP}, Brand: {$previousBrand}, Code: {$product_code}, Product: {$product_name}, Price: {$oldPrice} || changed into || "
                     . "Name: {$newName}, MOP: {$newMOP}, Brand: {$newBrand}, Code: {$product_code}, Product: {$product_name}, New Price: {$request->price}";

            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error'       => false,
                'message'     => trans('messages.success'),
                'data'        => [
                    'id'            => $previousData->id,
                    'name'          => $previousData->name,
                    'status'        => $previousData->status,
                    'sales_channel' => $previousData->sales_channel,
                    'MOP'           => $newMOP,
                    'Brand'         => $newBrand,
                    'product_code'  => $product_code,
                    'product_name'  => $product_name,
                    'price'         => $request->price,
                    'created_at'    => $previousData->created_at->format('M d, Y h:i A'),
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

    public function archivePriceTier(Request $request) //done
    {
        try {
            $id = Crypt::decrypt($request->id);

            $priceTier = PriceTier::find($id);
            if (!$priceTier) {
                return response()->json([
                    'error'     => true,
                    'message'   => trans('messages.product.priceTier.notFound'),
                ]);
            }

            DB::beginTransaction();

            $newStatus = $priceTier->status == 1 ? 0 : 1;
            $priceTier->update([
                'status' => $newStatus,
            ]);

            $action = $newStatus == 0 ? 'archived' : 'reactivated';
            $message = "{$priceTier->name} has been $action.";

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

    public function displayDetails(Request $request)
    {
        try {
            $tierFilter = Arr::flatten((array) $request->tierFilter, 1);

            $query = PricePerTier::with(['price_per_tier_per_product:id,name,product_code']);

            if (!empty($tierFilter)) {
                $query->whereIn('tier_id', $tierFilter);
            }

            $data = $query->select(['id', 'product_id', 'tier_id', 'price', 'created_at'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($item) {
                    $category = Product::with(['product_category', 'product_per_posCategories'])->where('id', $item->product_id)->first();
                    return [
                        'tier_id'               => $item->tier_id,
                        'product_id'            => $item->product_id,
                        'price'                 => $item->price,
                        'product_name'          => $item->price_per_tier_per_product->name ?? null,
                        'product_code'          => $item->price_per_tier_per_product->product_code ?? null,
                        'pos_category_name'     => $category->product_category->name,
                        'product_category'      => $category->product_per_posCategories->pos_category_name,
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

    // public function displayDetails(Request $request)
    // {
    //     try {
    //         $tierFilter = Arr::flatten((array) $request->tierFilter, 1);

    //         $query = PricePerTier::join('products', 'price_per_tier.product_id', '=', 'products.id')
    //             ->leftJoin('pos_categories', 'products.pos_category_id', '=', 'pos_categories.id')
    //             ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
    //             ->select(
    //                 'price_per_tier.id',
    //                 'price_per_tier.product_id',
    //                 'price_per_tier.tier_id',
    //                 'price_per_tier.price',
    //                 'price_per_tier.created_at',
    //                 'products.name as product_name',
    //                 'products.product_code as product_code',
    //                 'pos_categories.pos_category_name as pos_category_name',
    //                 'categories.name as product_category_name'
    //             );

    //         if (!empty($tierFilter)) {
    //             $query->whereIn('price_per_tier.tier_id', $tierFilter);
    //         }

    //         $data = $query->orderByDesc('price_per_tier.created_at')
    //             ->get()
    //             ->map(function ($item) {
    //                 return [
    //                     'tier_id'           => $item->tier_id,
    //                     'product_id'        => $item->product_id,
    //                     'price'             => $item->price,
    //                     'product_name'      => $item->product_name,
    //                     'product_code'      => $item->product_code,
    //                     'pos_category_name' => $item->pos_category_name,
    //                     'product_category'  => $item->product_category_name,
    //                     'created_at'        => Carbon::parse($item->created_at)->format('M d, Y h:i A'),
    //                 ];
    //             });

    //         return response()->json([
    //             'error'   => false,
    //             'message' => trans('messages.success'),
    //             'data'    => $data,
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error("Error: $e");
    //         return response()->json([
    //             'error'   => true,
    //             'message' => trans('messages.error'),
    //         ]);
    //     }
    // }

}
