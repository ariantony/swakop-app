<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'value'
    ];

    public function value() : Attribute
    {
        return Attribute::make(
            get:function($value) {
                return json_decode($value, true);
            },
            set:function($value) {
                $value = is_string($value) ? $value : json_encode($value);
                return $value;
            }
        );
    }
}
