<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use Auth;

class UserController extends Controller
{
    use ApiActions;

    public function __construct()
    {
        $this->middleware('auth.jwt:api');
    }

    /**
     * Get logged in user's details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return $this->respond(Auth::user());
    }

    /**
     * Update a logged in user's details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        if (!count(request()->all())) {
            return $this->respond('No field provided');
        }

        $username = request('username');

        if (request()->has('email')) {
            return $this->respond('You can\'t update email', 422);
        }

        if (request()->has('username') && User::where('username', $username)) {
            return $this->respond($username.' already taken', 409);
        }

        $result = Auth::user()->update(request()->all());

        if (!$result) {
            return $this->respond('Couldn\'t update your record. Try again', 500);
        }

        return $this->respond('Record updated successfully');
    }
}
