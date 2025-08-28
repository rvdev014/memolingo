<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemolingoWord extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'language_code',
        'lemma',
        'base_form'
    ];

    public function userKnownLexicons(): HasMany
    {
        return $this->hasMany(UserKnownLexicon::class, 'word_id');
    }

    public function userDictionaries(): HasMany
    {
        return $this->hasMany(UserDictionary::class, 'word_id');
    }

    public function textTokens(): HasMany
    {
        return $this->hasMany(TextToken::class, 'word_id');
    }
}
