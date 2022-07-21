<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'payment_method',
        'total_cost',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        // 'total_cost',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'user',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(Detail::class, 'transaction_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // public function totalCost() : Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => $this->details->reduce(fn (int $last, Detail $detail) => $last + $detail->total_cost_all, 0),
    //     );
    // }
}
