<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $table = "licenses";
    protected $fillable = [
        'user_id',
        'license_key',
        'expire_date',
    ];
}
