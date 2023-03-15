<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Notifications\SignedIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function singup()
    {
        $validation = signUpRules();
        if ($validation) {
            return response($validation, 400);
        }
        $data = request()->all();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
       // Notification::send($user,new SignedIn($user));
        $resArr = [];
        $resArr['token'] = $user->createToken('e-commerce-2')->accessToken;
        $resArr['name'] = $user->name;
        $resArr['id'] = $user->id;
        return response()->json(['user data' => $resArr], 201);

    }
    public function login()
    {
        $validation = loginRules();
        if ($validation) {
            return response($validation, 400);
        }

        $user = User::where('email', request()->email)->first();
        if (!$user) {
            return response()->json(['error' => ' email not found'], 203);
        }

        if (!Hash::check(request()->password, $user->password)) {
            return response()->json(['error' => 'inncorrect password'], 203);
        }

        $resArr = [];
        $resArr['token'] = $user->createToken('e-commerce-2')->accessToken;
        $resArr['name'] = $user->name;
        $resArr['email'] = $user->email;
        $resArr['id'] = $user->id;
        return response()->json($resArr, 200);
    }

    public function UnAuthorized()
    {
        return response()->json('UnAuthorized', 203);
    }

    public function forgotPassword()
    {

        $validEmail = Validator::make(request()->all(), ['email' => 'required|email|exists:users']);
        if ($validEmail->fails()) {
            return response($validEmail->errors(), 400);
        }
        $data = [];
        $data['email'] = request()->email;
        ResetCodePassword::where('email', request()->email)->delete();
        $data['code'] = mt_rand(100000, 999999);
        $codeData = ResetCodePassword::create($data);

        Mail::to(request()->email)->send(new SendCodeResetPassword($codeData->code));
        return response(['message' => trans('passwords.sent')], 200);
    }
    public function checkCode()
    {
        $validate = Validator::make(request()->all(), ['email' => 'required|email|exists:reset_code_passwords',
            'code' => 'required', 'password' => 'required|confirmed']);
        if ($validate->fails()) {
            return response($validate->errors(), 400);
        }
        $reqdata = request()->all();
        $resetData = ResetCodePassword::where('email', $reqdata['email'])->get();

        if ($reqdata['code'] !== $resetData[0]->code) {
            return response()->json(['inncorect code'], 400);
        }

        if (strtotime($resetData[0]->created_at->addHours(1)) < strtotime(now())) {

            $resetData->each->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        $password = bcrypt($reqdata['password']);
        User::where('email', $reqdata['email'])->update(['password' => $password]);
        $resetData->each->delete();
        return response()->json(['password reset'], 200);

    }

    public function resetPassword()
    {
        $validate = Validator::make(request()->all(), ['id' => 'required',
            'oldPassword' => 'required', 'password' => 'required|confirmed']);
        if ($validate->fails()) {
            return response($validate->errors(), 400);
        }
        $data = request()->all();

        $user = User::find($data['id']);
        if (!Hash::check($data['oldPassword'], $user->password)) {
            return response()->json(['error' => 'inncorrect password'], 203);
        }
        $newPassword = bcrypt($data['password']);
        $user->password = $newPassword;
        $user->save();
        return response()->json(['password reset'], 200);
    }
}
