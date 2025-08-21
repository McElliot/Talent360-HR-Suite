<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychometricResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'question_id',
        'user_id',
        'option_id',
        'text_response',
        'score',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'score' => 'float'
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychometricTest::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PsychometricTestQuestion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(PsychometricQuestionOption::class);
    }
}
