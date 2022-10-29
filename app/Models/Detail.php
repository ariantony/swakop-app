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
        'price_id',
        'type',
        'qty_unit',
        'qty_box',
        'qty_carton',
        'cost_unit',
        'cost_box',
        'cost_carton',
        'subtotal',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'variable_cost',
        'total_cost_unit',
        'total_cost_all',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'price',
    ];

    public static $withoutAppends = false;

    protected function getArrayableAppends()
    {
        if(self::$withoutAppends){
            return [];
        }
        return parent::getArrayableAppends();
    }
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
    public function price()
    {
        return $this->hasOne(Price::class, 'id', 'price_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }

    /**
     * @return int
     */
    public function getVariablePrice()
    {
        if (in_array($this->type, ['buy', 'return buy'])) {
            return $this->price?->price_per_unit * $this->qty_unit;
        }

        $qty = $this->qty_unit;
        $price = $this?->price;

        if ((int) $price?->variableCosts()->count() > 0) {
            $cost = $price?->variableCosts?->firstWhere('min_qty', '<=', $qty);
            if ($cost) {
                return $qty * $cost->price;
            }
        }

        return $qty * $this->cost_unit;
    }

    /**
     * @return array
     */
    public function getVariableData()
    {
        $qty = $this->qty_unit;
        $price = $this?->price;

        if ((int) $price?->variableCosts()->count() > 0){
            $cost = $price?->variableCosts?->firstWhere('min_qty', '<=', $qty);
            if ($cost) {
                return [[
                    'id' => $price?->id,
                    'perqty' => $cost->qty,
                    'perprice' => $cost->price,
                    'min_qty' => $cost->min_qty,
                    'qty' => $qty,
                    'subtotal' => $qty * $cost->price,
                ]];
            }
        }
        return [[
            'id' => $price?->id,
            'perqty' => 1,
            'perprice' => $this->cost_unit,
            'min_qty' => 1,
            'qty' => $qty,
            'subtotal' => $qty * $this->cost_unit,
        ]];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function variableCost() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->getVariablePrice(),
        );
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
