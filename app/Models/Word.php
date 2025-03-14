<?php

namespace App\Models;

use App\Enums\LearnStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $word
 * @property string $language
 * @property string $meaning
 * @property string $synonyms
 * @property string $antonyms
 * @property LearnStatus $learn_status
 * @property int $learned_times
 * @property boolean $is_favorite
 * @property int $user_category_id
 *
 * @property UserCategory $userCategory
 */
class Word extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'learn_status' => LearnStatus::class,
        'is_favorite' => 'boolean',
    ];

    public function userCategory(): BelongsTo
    {
        return $this->belongsTo(UserCategory::class);
    }
}
