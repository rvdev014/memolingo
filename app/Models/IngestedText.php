<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IngestedText extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'text_excerpt',
        'full_text',
        'keep_full'
    ];

    protected $casts = [
        'keep_full' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function textTokens(): HasMany
    {
        return $this->hasMany(TextToken::class);
    }
}
