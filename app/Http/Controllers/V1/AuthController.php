<?php

namespace App\Http\Controllers\V1;

use Validator;
use App\Models\User;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    use ApiActions;
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

        // Attach confirm validation error message to password_conformation field
        $validator->after(function ($validator) {
            $errors = $validator->errors();

            if (request()->filled(['password', 'password_confirmation'])) {
                if (request('password') != request('password_confirmation')) {
                    $errors->add('password_confirmation', 'The password confirmation does not matched.');
                }
            }
        });

        if ($validator->fails()) {
            return $this->respond($validator->errors(), 400);
        }

        $user = User::where('email', strtolower(request('email')))
            ->orWhere('username', strtolower(request('username')))
            ->first();

        if ($user) {
            return $this->respond('User with this detail already exists', 409);
        }

        $userDetails['password'] = bcrypt(request('password'));
        $newUser = User::create($userDetails);
        $token = auth()->setTTL(10080)->login($newUser);

        return $this->respondWithToken($token, 201);
    }

    /**
     * Logs in user to the application.
     *
     * @return \Illuminate\Http\jsonResponse
     */
    public function login()
    {
        $credentials = [];
        $validator = validator()->make(request()->all(), User::$signinRules);

        if ($validator->fails()) {
            return $this->respond($validator->messages(), 400);
        }

        $credentials['password'] = request('password');
        $credentials['email'] = strtolower(request('email'));
        if (!$token = auth()->setTTL(10080)->attempt($credentials)) {
            return $this->respond(
                'This credentials don\'t match our records.',
                404
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
        return $this->respond('Logout successful');
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
