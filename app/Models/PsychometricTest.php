<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsychometricTest extends Model
{
    /** @use HasFactory<\Database\Factories\PsychometricTestFactory> */
    use HasFactory;

    protected $fillable = [
        'psychometric_test_type_id',
        'title',
        'instructions',
        'duration_minutes',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function psychometricTestType()
    {
        return $this->belongsTo(PsychometricTestType::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeWithType($query, $typeId)
    {
        return $query->where('psychometric_test_type_id', $typeId);
    }
}
