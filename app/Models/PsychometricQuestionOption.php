<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychometricQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'option_value',
        'score_weight',
        'is_correct',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'metadata' => 'array',
        'score_weight' => 'float'
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PsychometricTestQuestion::class);
    }

    // Helper for matrix questions
    public function getMatrixCoordinates(): ?array
    {
        return $this->metadata['matrix_coords'] ?? null; // ['row' => 1, 'col' => 2]
    }

    // Helper for ranking questions
    public function getIdealRank(): ?int
    {
        return $this->metadata['ideal_rank'] ?? null;
    }
}
