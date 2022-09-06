<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detail extends Model
{
    use HasFactory;
    use SoftDeletes;

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
     * @var string[]
     */
    protected $appends = [
        'total_cost_unit',
        'total_cost_box',
        'total_cost_carton',
        'total_cost_all',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function totalCostUnit() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->qty_unit * $this->cost_unit,
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function totalCostBox() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->qty_box * $this->cost_box,
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function totalCostCarton() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->qty_carton * $this->cost_carton,
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function totalCostAll() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_cost_unit + $this->total_cost_box + $this->total_cost_carton,
        );
    }
}
