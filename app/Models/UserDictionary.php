<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserDictionary extends Model
{
    use HasFactory;
    
    protected $table = 'user_dictionary';
    
    protected $fillable = [
        'user_id',
        'word_id',
        'progress_percent',
        'ease',
        'interval_days',
        'last_review_at',
        'due_at',
        'status'
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'ease' => 'decimal:2',
        'interval_days' => 'integer',
        'last_review_at' => 'datetime',
        'due_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(MemolingoWord::class, 'word_id');
    }

    public function isDue(): bool
    {
        return $this->due_at <= Carbon::now();
    }

    public function updateSRS(string $answer): void
    {
        $now = Carbon::now();
        $this->last_review_at = $now;
        
        switch ($answer) {
            case 'bad':
                // Decrease ease and reset progress
                $this->ease = max(1.30, $this->ease - 0.20);
                $this->interval_days = 1;
                $this->progress_percent = max(0, $this->progress_percent - 20);
                break;
            case 'ok':
                // Increase ease slightly and progress
                $this->ease = min(2.50, $this->ease + 0.05);
                $this->interval_days = (int) ceil($this->interval_days * $this->ease);
                $this->progress_percent = min(90, $this->progress_percent + 15);
                break;
            case 'mastered':
                $this->status = 'mastered';
                $this->progress_percent = 100;
                $this->ease = 2.50;
                $this->interval_days = 30; // Long interval for mastered words
                break;
        }
        
        $this->due_at = $now->addDays($this->interval_days);
        $this->save();
    }
}
