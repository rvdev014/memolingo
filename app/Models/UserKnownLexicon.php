<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserKnownLexicon extends Model
{
    use HasFactory;
    
    protected $table = 'user_known_lexicon';
    
    protected $fillable = [
        'user_id',
        'word_id',
        'added_at'
    ];

    protected $casts = [
        'added_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(MemolingoWord::class, 'word_id');
    }
}
