<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'transaction_id',
        'product_id',
        'type',
        'qty_unit',
        'qty_box',
        'qty_carton',
        'cost_unit',
        'cost_box',
        'cost_carton',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
