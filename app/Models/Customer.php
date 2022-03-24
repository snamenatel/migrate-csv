<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public    $timestamps = false;
    protected $fillable   = ['name', 'email', 'age', 'location'];
    const UNKNOWN_LOCATION = 'Unknown';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->surname) {
                [$model->name, $model->surname] = explode(' ', $model->name);
            }
            if (!$model->location) {
                $model->location = self::UNKNOWN_LOCATION;
            } else {
                $code                = Country::where('name', 'like', $model->location)->value('code');
                $model->country_code = $code;
                if (!$code) {
                    $model->location = self::UNKNOWN_LOCATION;
                }
            }
        });
    }
}
