<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariableCost extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'price_id',
        'qty',
        'min_qty',
        'price',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
