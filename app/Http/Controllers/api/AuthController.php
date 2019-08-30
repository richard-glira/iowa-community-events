<?php

namespace App\Http\Controllers\api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends APIController {

    /**
     * Register a new user
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return $this->apiResponse([
                'errors' => $validator->errors()->all()
            ], 422, $this->generateStatusMessage(422));
        }
    
        $request['password'] = Hash::make($request['password']);
        $user = User::create($request->toArray());
    
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
    
        return $this->apiResponse([
            'user' => is_array($user) ? $user : $user->toArray(),
            'token' => $token,
        ], 200, $this->generateStatusMessage(200));
    }

    /**
     * Login user
     *
     * @param Request $request
     * @param $email
     * @param $password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login (Request $request) {
        $status = 400;
        $user = array();
        $errors = array();
        $token = '';

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $status = 202;
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            } else {
                $status = 401;
                $errors[] = "Password missmatch";
            }
        } else {
            $status = 422;
            $errors[] = 'User does not exist';
        }

        return $this->apiResponse([
            'user' => $user,
            'token' => $token,
            'errors' => $errors
        ], $status, $this->generateStatusMessage($status));
    }

    /**
     * Register a new user.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
    
        $logout = 'You have been succesfully logged out!';

        return $this->apiResponse([
            'logout' => $logout
        ], 200, $this->generateStatusMessage(200));
    }
}
