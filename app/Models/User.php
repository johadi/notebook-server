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
        'username'=>'required|min:2',
        'password'=>'required|min:6',
        'password_confirmation'=>'required'
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
     * Change email field value to lowercase before saving to Database
     * @param $value - value to change to lower case
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Changes username field value to lowercase before saving to Database
     * @param $value - value to change to lower case
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower($value);
    }

    /**
     * Capitalizes username when getting it from Database
     * @param $value - $value to capitalized
     * @return string - the capitalized username
     */
    public function getUsernameAttribute($value)
    {
        return ucfirst($value);
    }

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
