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
    AccessToken,
    AreaAssignment,
    Brand,
    UserAccess,
    Users,
    Area,
    BrandAssignment,
    ScheduleGroup,
    Store,
    StorePerSchedule
};

class LoginController extends Controller
{
    protected $request;
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
  
            AccessToken::insert([
                'user_id'        => $user->id,
                'token'          => $token,
                'type'           => 1,
                'created_at'     => $today,
                'updated_at'     => $today,
            ]);


            $access = UserAccess::with([
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

            return response()->json([
                'error' => false,
                'msg' => trans('messages.success'),
                'modal_title' => trans("alerts_title.success"),
                'user' => [
                    'name'           => $user->name,
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
            AccessToken::where("token", $request->token)->delete();

            DB::commit();

            return response()->json([
                'error' => false,
                'msg'   => trans('messages.success')
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

