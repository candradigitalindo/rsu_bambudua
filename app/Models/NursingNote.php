<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NursingNote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'admission_id',
        'nurse_id', 
        'note',
        'note_type',
        'priority',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admission this nursing note belongs to
     */
    public function admission(): BelongsTo
    {
        return $this->belongsTo(InpatientAdmission::class, 'admission_id');
    }

    /**
     * Get the nurse who wrote this note
     */
    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for filtering by note type
     */
    public function scopeByNoteType($query, $noteType)
    {
        return $query->where('note_type', $noteType);
    }

    /**
     * Scope for recent notes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('recorded_at', '>=', now()->subDays($days));
    }
}
