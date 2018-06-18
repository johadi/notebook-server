<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Cloudder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Storage;

class UserController extends Controller
{
    use ApiActions;

    public function __construct()
    {
        $this->middleware('auth.jwt:api');
    }

    /**
     * Get the authenticated user's details
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $user = auth()->user();

        if ($user) {
            return $this->respond($user);
        }

        return $this->respond('User not found', 404);
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
        $request = request()->all();
        $username = strtolower($request['username']);

        if (request()->has('email')) {
            return $this->respond('You can\'t update email', 422);
        }

        if (request()->has('username')) {
            $validator = Validator::make($request, ['username' => 'required']);

            if ($validator->fails()) {
                return $this->respond('Username cannot be empty.', 400);
            }

            if (User::where('username', $username)->first() &&
                $username != strtolower(auth()->user()->username)
            ) {
                return $this->respond($username . ' already taken ', 409);
            }
        }

        if (request()->hasFile('avatar') && request('avatar')->isValid()) {
            $validator = Validator::make($request, ['avatar'=>'required|mimes:jpeg,jpg,png|between:1, 2000']);

            if ($validator->fails()) {
                return $this->respond($validator->messages(), 400);
            }

            $storedPictureResult = $this->storeProfilePicture($request);

            if ($storedPictureResult->file_status === 'invalid') {
                return $this->respond('File type not allowed', 400);
            }

            if ($storedPictureResult->file_status !== 'valid') {
                return $this->respond('Internal Server error. File not saved.', 500);
            }

            $request['avatar_path'] = $storedPictureResult->stored_file_path;
        }

        $result = Auth::user()->update($request);

        if (!$result) {
            return $this->respond('Couldn\'t update your record. Try again', 500);
        }

        return $this->respond(Auth::user());
    }

    private function storeProfilePicture($request)
    {
        $file = $request['avatar'];
        $fileExtension = strtolower($file->extension());
        $isAllowedExtension = $fileExtension === 'jpg' || $fileExtension === 'jpeg' || $fileExtension === 'png';

        if (!$isAllowedExtension) {
            return (object)['file_status' => 'invalid'];
        }

        $username = $request['username'] ? $request['username'] : auth()->user()->username;
        $imageFullPath='';

        if (env('APP_ENV') !== 'local') {
            $imageFullPath = $this->uploadToCloudinary($file, $username);
        } else {
            $imageFullPath = $this->savePictureLocally($file, $username);
        }

        return (object)[
            'file_status' => 'valid',
            'stored_file_path' => $imageFullPath
        ];
    }

    private function uploadToCloudinary($file, $username)
    {
        $publicId = strtolower('notebook/profile_pictures/'.$username);
        Cloudder::upload($file, $publicId, ['width' => 250, 'height'=>250]);
        $uploadResult = (object)Cloudder::getResult();

        return $uploadResult->secure_url;
    }

    private function savePictureLocally($file, $username)
    {
        $appUrl = env('APP_URL') . ':' . config('custom.appPort');
        $fileName = strtolower($username . '.' . strtolower($file->extension()));
        $path = $file->storeAs('', $fileName, 'profile');

        return $appUrl. '/storage/profile_pictures/' . $path;
    }
}
