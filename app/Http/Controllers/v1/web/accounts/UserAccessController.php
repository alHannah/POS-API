<?php

namespace App\Http\Controllers\v1\web\accounts;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\UserAccess;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class UserAccessController extends Controller
{
    public function create(Request $request) {
        try {
            DB::beginTransaction();

            $roleName = $request->roleName;
            $add      = Arr::flatten((array) $request->add, 1);
            $edit     = Arr::flatten((array) $request->edit, 1);
            $view     = Arr::flatten((array) $request->view, 1);
            $delete   = Arr::flatten((array) $request->delete, 1);
            $approve  = Arr::flatten((array) $request->approve, 1);

            // Validate the role name
            $validate = Validator::make($request->all(), [
                'roleName' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => trans('messages.required')
                ]);
            }

            $nameExists = Role::where('role_name', $roleName)->exists();

            if ($nameExists) {
                return response()->json([
                    'error' => true,
                    'message' => trans('Role Name already exists'),
                ]);
            }

            // Create Role
            $createRole = Role::create([
                'role_name' => $roleName,
            ]);

            $roleId = $createRole->id;

            // Fetch modules and validate permissions
            $moduleData = Module::get();
            foreach ($moduleData as $index => $module) {
                // create UserAccess
                $userAccess                 = new UserAccess();

                $userAccess->role_id        = $roleId;
                $userAccess->module_id      = $module->id;
                $userAccess->add            = $this->validatePermission($module->add, $add[$index] ?? 0);
                $userAccess->edit           = $this->validatePermission($module->edit, $edit[$index] ?? 0);
                $userAccess->view           = $this->validatePermission($module->view, $view[$index] ?? 0);
                $userAccess->delete         = $this->validatePermission($module->delete, $delete[$index] ?? 0);
                $userAccess->approve        = $this->validatePermission($module->approve, $approve[$index] ?? 0); // Approve cannot be altered

                // save the data
                $userAccess->save();
            }

            $message = "Created User Access: $createRole";
            $request['remarks'] = $message;
            $request['type'] = 2;
            $this->audit_trail($request);

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => trans('messages.success'),
                'data' => $createRole,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info("Error: $e");
            return response()->json([
                'error' => true,
                'message' => trans('messages.error'),
            ]);
        }
    }

    private function validatePermission($currentValue, $newValue) {
        if ($currentValue == 1) {
            if (in_array($newValue, [0, 1])) {
                return $newValue;
            } else {
                throw new \InvalidArgumentException("Invalid value for permission");
            }
        }
        // If current value is 0, retain the current value
        return $currentValue;
    }


    public function update(Request $request){
        try {
            DB::beginTransaction();
            $role           = $request->role_id;
            $modules        = $request->module_id;
            $add            = $request->add;
            $edit           = $request->edit;
            $view           = $request->view;
            $delete         = $request->delete;
            $approve        = $request->approve;
            $index = 0;
            $modules_length = count($modules);

            foreach ($modules as $module) {
                UserAccess::where("role_id", $role)->where('module_id',$module)->update([
                    "add"       => $add[$index],
                    "edit"      => $edit[$index],
                    "view"      => $view[$index],
                    "delete"    => $delete[$index],
                    "approve"   => $approve[$index],
                ]);
                $index++;
                if ($index >= $modules_length) {
                    $index--;
                }
            }

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

    public function get_update(Request $request){
        try {
            DB::beginTransaction();
            $role = $request->role_id;
            $userAccessData = UserAccess::with([
                "user_access_module",
                "user_access_role"
            ])->where('role_id',$role)->get();

            $tableData = $userAccessData->map(function ($items) {
                return [
                    "module_id"         => $items->module_id,
                    "module_name"       => $items->user_access_module->module_name,
                    "module_add"        => $items->user_access_module->add,
                    "module_edit"       => $items->user_access_module->edit,
                    "module_view"       => $items->user_access_module->view,
                    "module_delete"     => $items->user_access_module->delete,
                    "module_approve"    => $items->user_access_module->approve,
                    "add"               => $items->add,
                    "edit"              => $items->edit,
                    "view"              => $items->view,
                    "delete"            => $items->delete,
                    "approve"           => $items->approve,
                    "role_name"         => $items->user_access_role->role_name,
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


    public function displayRole(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = Role::orderByDesc('created_at')
                ->get(['id', 'role_name','created_at'])
                ->map(function ($item) {
                    return [
                        'id'         => Crypt::encryptString($item->id),
                        'role_name'  => $item->role_name,
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

    public function displayModule(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = Module::orderByDesc('created_at')
                ->get(['id','module_name','add','edit','view','delete','approve','created_at'])
                ->map(function ($item) {
                    return [
                        'id'           => Crypt::encryptString($item->id),
                        'module_name'  => $item->module_name,
                        'add'          => $item->add,
                        'edit'         => $item->edit,
                        'view'         => $item->view,
                        'delete'       => $item->delete,
                        'approve'      => $item->approve,
                        'created_at'   => $item->created_at ? $item->created_at->format('M d, Y h:i A') : null,
                        'id_nE'        => $item->id,
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

}
