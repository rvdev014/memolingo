<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextToken extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ingested_text_id',
        'token_raw',
        'word_id',
        'is_unknown'
    ];

    protected $casts = [
        'is_unknown' => 'boolean'
    ];

    public function ingestedText(): BelongsTo
    {
        return $this->belongsTo(IngestedText::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(MemolingoWord::class, 'word_id');
    }
}
