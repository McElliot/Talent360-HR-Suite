<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PsychometricTestQuestion extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'psychometric_test_id',
        'parent_question',
        'question_text',
        'answer_type',
        'sort_order',
        'metadata',
        'is_required',
        'is_active',
        'question_number'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Question type constants for easy reference
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_RADIO = 'radio';
    const TYPE_OPEN_ENDED = 'open_ended';
    const TYPE_LIKERT_SCALE = 'likert_scale';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_DROPDOWN = 'dropdown';
    const TYPE_MATRIX = 'matrix';
    const TYPE_RANKING = 'ranking';
    const TYPE_DATE = 'date';
    const TYPE_FILE_UPLOAD = 'file_upload';
    const TYPE_NUMERIC = 'numeric';
    const TYPE_TEXT_AREA = 'text_area';

    // Relationships
    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychometricTest::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PsychometricQuestionOption::class, 'question_id');
    }

    public function childQuestions(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_question', 'question_text')
            ->orderBy('sort_order');
    }

    public function competencies()
    {
        return $this->belongsToMany(
            PsychometricCompetence::class,
            'psychometric_competences_question',
            'question_id',   // This should match your migration
            'competency_id'  // This should match your migration
        )
            ->withPivot('weight')
            ->withTimestamps();
    }

    // Scoring methods
    public function maxPossibleScore()
    {
        if ($this->hasOptions()) {
            return $this->options->max('score_value') *
                $this->competencies->max('pivot.weight');
        }
        return $this->competencies->sum('pivot.weight');
    }

    public function isCoreCompetencyQuestion(): bool
    {
        return $this->competencies()->where('is_core', true)->exists();
    }

    // Scopes
    public function scopeRootQuestions($query)
    {
        return $query->whereNull('parent_question');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('answer_type', $type);
    }

    // Helpers
    public function isMultipleChoice(): bool
    {
        return $this->answer_type === self::TYPE_MULTIPLE_CHOICE;
    }

    public function isMatrixQuestion(): bool
    {
        return $this->answer_type === self::TYPE_MATRIX;
    }

    public function hasOptions(): bool
    {
        return in_array($this->answer_type, [
            self::TYPE_MULTIPLE_CHOICE,
            self::TYPE_RADIO,
            self::TYPE_LIKERT_SCALE,
            self::TYPE_TRUE_FALSE,
            self::TYPE_DROPDOWN,
            self::TYPE_MATRIX,
            self::TYPE_RANKING
        ]);
    }

    public function getConfigAttribute()
    {
        return $this->metadata['config'] ?? [];
    }

    public function getValidationRulesAttribute()
    {
        return $this->metadata['validation'] ?? [];
    }

    // For file upload questions
    public function getAllowedFileTypes()
    {
        return $this->metadata['allowed_file_types'] ?? ['pdf', 'doc', 'docx', 'jpg', 'png'];
    }

    public function getMaxFileSize()
    {
        return $this->metadata['max_file_size'] ?? 2048; // in KB
    }
}
