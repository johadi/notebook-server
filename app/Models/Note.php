<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'title', 'body','category'
    ];

    public static $rules = [
        'title' => 'required',
        'body' => 'required',
        'category' => 'required'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }

    /**
     * Ensure category is returned in lower case when in query
     * @param $value - category value
     * @return string - category in lowercase
     */
    public function getCategoryAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * Ensure title is returned in capitalized form when in query
     * @param $value - title value
     * @return string - title capitalized
     */
    public function getTitleAttribute($value)
    {
        return ucfirst($value);
    }
}
