<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait RefKey {

    protected static function bootRefKey()
    {
        static::creating(function ($model) {
            $model->ref_key = Str::random(25);
        });
    }
}