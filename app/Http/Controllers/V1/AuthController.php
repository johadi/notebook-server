<?php

namespace App\Http\Controllers\V1;

use Validator;
use App\Models\User;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth.jwt:api'], ['except' => ['login', 'register']]);
    }

    /**
     * Registers user to the application.
     *
     * @return \Illuminate\Http\jsonResponse
     */
    public function register()
    {
        $userDetails = request()->all();
        $validator = Validator::make($userDetails, User::$signupRules);

        if ($validator->fails()) {
            return response($validator->errors());
        }

        $user = User::where('email', request('email'))
            ->orWhere('username', request('username'))
            ->first();

        if ($user) {
            return response('User already exists');
        }

        $userDetails['password'] = bcrypt(request('password'));
        $newUser = User::create($userDetails);
        $token = auth()->setTTL(86400)->login($newUser);

        return $this->respondWithToken($token, 201);
    }

    /**
     * Logs in user to the application.
     *
     * @return \Illuminate\Http\jsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        $validator = validator()->make($credentials, User::$signinRules);

        if ($validator->fails()) {
            return response($validator->messages());
        }

        if (!$token = auth()->setTTL(86400)->attempt($credentials)) {
            return response()
                ->json(
                    ['error' =>
                    'This credentials don\'t match our records. Try again with the right credentials'
                    ],
                    401
                );
        }

        return $this->respondWithToken($token);
    }

    /**
     * Logs the user out of the application
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->logout();
        return response('Logout successful', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response(auth()->payload());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $status = 200)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], $status);
    }
}
