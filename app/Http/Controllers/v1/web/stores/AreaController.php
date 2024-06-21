<?php

namespace App\Http\Controllers\v1\web\stores;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Areas;

class AreaController extends Controller
{
    public function create(Request $request) 
    {
        try {
            DB::beginTransaction();

            $area = Areas::updateOrCreate([
                'id' => $request->id
            ], [
                'name' => $request->name,
                'brand_id' => $request->brand_id
            ]);
            

            if ($area->wasRecentlyCreated) {
                $type = 'Yes it is Recently Created!';
            } else {
                $type = 'It is updated!';
            }

            DB::commit();

            return response()->json([
                'error'     => false,
                'message'   => trans('messages.success'),
                'data'      => $area,
                'type'      => $type
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
