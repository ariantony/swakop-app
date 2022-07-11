<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'barcode',
    ];

    protected $with = [
        'price',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'stock_unit',
        'stock_box',
        'stock_carton',
    ];

    public function price()
    {
        return $this->hasOne(Price::class)->orderBy('effective_date', 'desc');
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buy()
    {
        return $this->transactions()->where('type', 'buy');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sell()
    {
        return $this->transactions()->where('type', 'sell');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function stockUnit() : Attribute
    {
        return Attribute::make(
            get: function () {
                $buy = $this->buy()->sum('qty_unit');
                $sell = $this->sell()->sum('qty_unit');

                return $buy - $sell;
            },
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function stockBox() : Attribute
    {
        return Attribute::make(
            get: function () {
                $buy = $this->buy()->sum('qty_box');
                $sell = $this->sell()->sum('qty_box');

                return $buy - $sell;
            },
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function stockCarton() : Attribute
    {
        return Attribute::make(
            get: function () {
                $buy = $this->buy()->sum('qty_carton');
                $sell = $this->sell()->sum('qty_carton');

                return $buy - $sell;
            },
        );
    }
}
