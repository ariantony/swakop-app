<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'barcode',
        'qty_per_unit',
        'qty_per_box',
        'qty_per_carton',
    ];

    protected $with = [
        'price',
    ];

    public function price()
    {
        return $this->hasOne(Price::class)->orderBy('effective_date', 'desc');
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
