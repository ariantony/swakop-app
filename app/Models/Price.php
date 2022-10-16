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

    /**
     * @var string[]
     */
    protected $with = [
        'variableCosts',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variableCosts()
    {
        return $this->hasMany(VariableCost::class)->orderBy('qty', 'desc');
    }

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (Price $model) {
            $latest = Price::where('product_id', $model->product_id)
                            ->where('created_at', '<', $model->created_at)
                            ->orderBy('created_at', 'desc')
                            ->first();

            if ($latest) {
                $latest->update([
                    'expire_date' => now()->format('Y-m-d'),
                ]);
            }
        });
    }
}
