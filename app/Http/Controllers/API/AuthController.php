<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Socialite;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use session;

use function PHPUnit\Framework\isNan;

class AuthController extends Controller
{
    protected function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|string|regex:/^[a-zA-Z]+$/u|max:50',
                'last_name' => 'required|string|regex:/^[a-zA-Z]+$/u|max:50',
                'email' => 'required|string|email|max:255|',
                'password' => 'required|string|min:6|confirmed',
            ]
        );
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->all(), 'status' => 'error'], 422);
        }
        $userExist = User::where('email', $request->email)->first();
        if ($userExist) {

            $response = ['message' => 'User exists', 'status' => 'error'];
            return response($response, 200);
        }

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $userData['token'] = $token;
        $response = ['data' => $userData, 'status' => 'success', 'message' => 'User registered!'];
        return response($response, 200);
    }
    public function login(Request $request)
    {

        if ($request->email) {

            $verify_email = $request->validate([
                'email' => 'required|email'
            ]);
            if ($verify_email) {

                $user = User::where('email', $request->email)->first();

                if ($user) {

                    if (Hash::check($request['password'], $user->password)) {

                        $user_Data = User::where('email', $request->email)->first();

                        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                        $userData['user_data'] =  $user_Data;
                        $userData['token'] = $token;
                        $response = ['status' => 'success', 'data' => $userData, 'message' => 'data received '];
                        return response($response, 200);
                    } else {
                        $response = ["message" => "Password mismatch", "status" => "error"];
                        return response($response, 200);
                    }
                } else {
                    $response = ["message" => 'User does not exist', 'status' => 'error'];
                    return response($response, 200);
                }
            } else {

                return response(['message' => 'Invalid email.', 'status' => 'error'], 200);
            }
        } else {
            return response(['message' => 'Enter email', 'status' => 'error'], 422);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['status' => 'success', 'message' => 'logged out'];
        return response($response, 200);
    }


    public function ResetPassword(Request $request)
    {

        $user = auth()->user();
        $current_email = $user->email;
        $validator = Validator::make($request->all(), [
            // 'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',

        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->all(), 'status' => 'error'], 200);
        }

        //   $UserEmail = $request->user()->email;
        $request['remember_token'] = Str::random(10);
        $request['new_password'] = Hash::make($request['new_password']);


        $userdetail = User::where('email', $current_email)->first();
        if ($userdetail) {
            if (Hash::check($request->password, $userdetail->password)) {
                //update password
                User::where('email', $current_email)->update([
                    'password' => $request['new_password'],
                    'remember_token' => $request['remember_token']
                ]);
                $token = $userdetail->createToken('Laravel Password Grant Client')->accessToken;
                $userdetail['token'] = $token;
                //success response
                $response = ['data' => $userdetail, 'status' => 'success', 'message' => 'data received'];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch", 'status' => 'error'];
                return response($response, 200);
            }
        }
    }
    public function User()
    {
        $user = auth()->user();
        $userdata = User::where('id', $user->id)->first();

        return response(['data' => $userdata, 'message' => 'data recieved', 'status' => 'success'], 200);
    }
}
