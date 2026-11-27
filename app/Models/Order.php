<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'order_number',
    'gift_name',
    'total',
    'status',
    'estimated_delivery',
    'tracking_note',
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'estimated_delivery' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
