<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class PsychometricTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'psychometric_test_type_id',
        'title',
        'instructions',
        'description',
        'duration_minutes',
        'question_count',
        'version',
        'max_attempts',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'question_count' => 'integer',
        'version' => 'integer',
        'max_attempts' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function psychometricTestType(): BelongsTo
    {
        return $this->belongsTo(PsychometricTestType::class);
    }

    /**
     * The competences that belong to the test.
     */
    public function competences(): BelongsToMany
    {
        return $this->belongsToMany(
            PsychometricCompetence::class,
            'psychometric_competences_test',
            'test_id',       // This should match your migration
            'competency_id'  // This should match your migration
        )->withPivot('weight', 'created_at', 'updated_at');
    }

    /**
     * Get the test type that owns the test.
     */
    public function testType(): BelongsTo
    {
        return $this->belongsTo(PsychometricTestType::class, 'psychometric_test_type_id');
    }

    public function competencyScore($competencyId, $userId)
    {
        return $this->questions()
            ->whereHas('competencies', fn($q) => $q->where('id', $competencyId))
            ->with(['responses' => fn($q) => $q->where('user_id', $userId)])
            ->get()
            ->sum(fn($q) => (
                $q->responses->sum('score') *
                $q->competencies->firstWhere('id', $competencyId)->pivot->weight
            ));
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->version = 1; // Set initial version
            $model->question_count = 0; // Initialize with 0 questions
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
