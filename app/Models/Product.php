<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'name',
        'barcode',
        'group_id',
    ];

    protected $with = [
        'price',
        'group',
        'buy',
        'sell',
        'returnBuy',
        'retur',
        'variableCosts',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'stock_unit',
        'stock_box',
        'stock_carton',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variableCosts()
    {
        return $this->hasMany(VariableCost::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id')->orderBy('created_at', 'desc');
    }

    public function price()
    {
        return $this->hasOne(Price::class)->orderBy('created_at', 'desc');
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(Detail::class, 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buy()
    {
        return $this->details()->where('type', 'buy');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sell()
    {
        return $this->details()->where('type', 'sell');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnBuy()
    {
        return $this->details()->where('type', 'return buy');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function retur()
    {
        return $this->details()->where('type', 'return sell');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function from()
    {
        return $this->hasMany(Conversion::class, 'from_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function to()
    {
        return $this->hasMany(Conversion::class, 'to_id', 'id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function stockUnit() : Attribute
    {
        return Attribute::make(
            get: function () {
                $buy = $this->buy->sum('qty_unit');
                $sell = $this->sell->sum('qty_unit');
                $returnBuy = $this->returnBuy->sum('qty_unit');
                $subtractor = $this->from->sum('large');
                $addition = $this->to->sum('addition');

                return $buy - $sell - $returnBuy - $subtractor + $addition;
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
                $buy = $this->buy->sum('qty_box');
                $sell = $this->sell->sum('qty_box');
                $returnBuy = $this->returnBuy->sum('qty_box');

                return $buy - $sell - $returnBuy;
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
                $buy = $this->buy->sum('qty_carton');
                $sell = $this->sell->sum('qty_carton');
                $returnBuy = $this->returnBuy->sum('qty_carton');

                return $buy - $sell - $returnBuy;
            },
        );
    }
}
