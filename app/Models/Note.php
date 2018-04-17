<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'title', 'body'
    ];

    public static $rules = [
        'title' => 'required',
        'body' => 'required'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'email');
    }
}
