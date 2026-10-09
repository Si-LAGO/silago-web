<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'description',
        'condition',
        'specification',
        'completeness',
        'sell_reason',
        'price',
        'stock',
        'is_negotiable',
        'verified_by',
        'verified_at',
        'rejection_note',
        'scan_note',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_negotiable' => 'boolean',
            'verified_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
