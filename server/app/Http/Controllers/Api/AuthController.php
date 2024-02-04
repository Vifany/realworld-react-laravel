<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\json\CurrentUserResource;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{

    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->input('user.email'),
            'password' => $request->input('user.password'),
        ];
        $token = Auth::attempt($credentials);
        if ($token) {
            /**
             * @var App\Models\User $user
             */
            $user = Auth::user();

            return new CurrentUserResource(
                (object) [
                'user' => $user,
                'token' => $token,
                 ]
            );
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
