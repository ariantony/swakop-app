<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cost_selling_per_unit',
        'cost_selling_per_box',
        'cost_selling_per_carton',
        'price_per_unit',
        'price_per_box',
        'price_per_carton',
        'effective_date',
        'expire_date',
    ];
}
