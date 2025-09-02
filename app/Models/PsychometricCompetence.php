<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PsychometricCompetence extends Model
{
    /** @use HasFactory<\Database\Factories\PsychometricCompetenceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'test_type_id',
        'sort_order'
    ];

    // Relationships
    /**
     * Get the test type that owns the competence.
     */
    public function testType(): BelongsTo
    {
        return $this->belongsTo(PsychometricTestType::class, 'test_type_id');
    }

    /**
     * The tests that belong to the competence.
     */
    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(
            PsychometricTest::class,
            'psychometric_competences_test',
            'competency_id', // This should match your migration
            'test_id'        // This should match your migration
        )->withPivot('weight');
    }

    public function questions()
    {
        return $this->belongsToMany(
            PsychometricTestQuestion::class,
            'psychometric_competences_question',
            'competency_id', // This should match your migration
            'question_id'    // This should match your migration
        )
            ->withPivot('weight')
            ->withTimestamps();
    }

    // Scopes
    public function scopeForTestType($query, $testTypeId)
    {
        return $query->where('test_type_id', $testTypeId);
    }

    // Helpers
    public static function personalityDimensions()
    {
        return [
            'OPEN' => ['name' => 'Openness', 'description' => '...'],
            'CONS' => ['name' => 'Conscientiousness', 'description' => '...'],
            'EXT' => ['name' => 'Extraversion', 'description' => '...'],
            'AGR' => ['name' => 'Agreeableness', 'description' => '...'],
            'NEUR' => ['name' => 'Neuroticism', 'description' => '...']
        ];
    }
}
