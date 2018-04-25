<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'avatar_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Overrides the laravel default primary key which is from id to email.
     *
     * @var string
     */
    protected $primaryKey = "email";

    /**
     * Tells laravel the primary key is not an integer but a string.
     *
     * @var string
     */
    protected $keyType = "string";

    /**
     * Tells laravel the primary key is not an incrementing value.
     *
     * @var string
     */
    public $incrementing = false;

    /**
     * User model signup rules
     * @var array
     */
    public static $signupRules = [
        'email'=>'required|email',
        'password'=>'required',
        'username'=>'required'
    ];

    /**
     * User model signin rules
     * @var array
     */
    public static $signinRules = [
        'email' => 'required|email',
        'password'=>'required|min:6'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Defines relationship between User and Note models
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_email', 'email');
    }
}
