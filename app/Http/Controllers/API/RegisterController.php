<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use Exception;


class RegisterController extends Controller
{
    //
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    //use RegistersUsers;

    // protected $redirectTo = '/coupleDashboard';

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct() //NA
    // {
    //     $this->middleware('guest');
    // }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    // protected function validator(Request $request)
    // {

    //     //return $validator;
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $request
     * @return \App\User
     */

    protected function create(Request $request)
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
    public function User()
    {
        $user = auth()->user();
        $userdata = User::where('id', $user->id)->first();

        return response(['data' => $userdata, 'message' => 'data recieved', 'status' => 'success'], 200);
    }
}
