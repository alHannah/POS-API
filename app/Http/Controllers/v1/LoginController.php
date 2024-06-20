<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\{
    AccessTokens,
    AreaAssignment,
    Brands,
    UserAccesses,
    Users,
    Areas,
    BrandAssignments
};

class LoginController extends Controller
{
    public function __construct(Request $request)
    {
        // SETUP DEFAULT LANGUAGE
        $lang = $request->has('lang') ?
            $request->input('lang') : "en";
        app('translator')->setLocale($lang);

        $this->request = $request;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'                 => 'required|email|bail',
            'password'              => 'required|bail',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'         => true,
                'modal_title'   => trans("alerts_title.oops"),
                'msg'           => $validator->errors()->first(),
                'result'        => $validator->errors()
            ]);
        }


        $email = $request->email ?: "";
        $password = $request->password ?: "";
        $today = Carbon::now();

        // CHECK IF EMAIL ALREADY EXISTS
        // $user = $this->CRUD_REPO->find($this->USERS, "email = BINARY '" . $email . "' AND `status` = '1' AND role = 'company_user'");
        $user = Users::where("email", $email)->where("status", "active")->first();

        if (!$user) {
            return response()->json([
                'error'         => true,
                'modal_title'   => trans("alerts_title.oops"),
                'msg'           => trans('messages.login.incorrect')
            ]);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'error'         => true,
                'modal_title'   => trans("alerts_title.oops"),
                'msg'           => trans('messages.login.incorrect')
            ]);
        }

        try {
            DB::beginTransaction();

            $token = $this->generateAuthToken($user);
            AccessTokens::insert([
                'mobile_user_id' => $user->id,
                'token'          => $token,
                'created_at'     => $today,
                'updated_at'     => $today,
            ]);

            $user_accesses = [];

            //Code for testing ORM
            // $areas = AreaAssignment::with([
            //     // 'brand_per_area'
            //     'area_to_user'
            //     // 'area_assignment'
            // ])->get();

            // dd($areas);



            $access = UserAccesses::with([
                'user_access_module'
            ])->where('role_id', $user->role_id)->get();

            $userAccessMapped = $access->map(function ($item) {
                $moduleAccess = $item->user_access_module;

                return [
                    'role_id'   => $item->role_id,
                    'module'    => $moduleAccess->module_name,
                    'add'       => $item->add,
                    'edit'      => $item->edit,
                    'view'      => $item->view,
                    'delete'    => $item->delete,
                    'approve'   => $item->approve,
                ];
            });

            DB::commit();

            // $userCompleteName = implode(" ", array_filter([
            //     'first_name' => $user->firstname,
            //     'last_name' => $user->lastname,
            // ]));

            return response()->json([
                'error' => false,
                'msg' => trans('messages.success'),
                'modal_title' => trans("alerts_title.success"),
                'user' => [
                    'name'           =>  $user->name,
                    'email'          => $email,
                    'token'          => $token,
                    'role'           => $user->role_id,
                    'access_modules' => $userAccessMapped
                ]
            ]);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
        }
    }

    public function logout(Request $request)
    {
        try {
            DB::beginTransaction();

            AccessTokens::where("token", $request->token)->delete();

            DB::commit();

            return response()->json([
                'error' => false,
                'msg' => trans('messages.success')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error'         => true,
                'msg'           => trans("messages.error"),
                'modal_title'   => trans("alerts_title.oops")
            ]);
        }
    }
}
