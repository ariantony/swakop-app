<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'from_id',
        'to_id',
        'large',
        'small_per_large',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'addition',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function from()
    {
        return $this->hasOne(Product::class, 'id', 'from_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function to()
    {
        return $this->hasOne(Product::class, 'id', 'to_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function addition() : Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->large * $this->small_per_large;
            },
        );
    }
}
