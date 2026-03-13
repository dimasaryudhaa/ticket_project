<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'category',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'open',
        'priority' => 'medium',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-warning',
            'in_progress' => 'bg-info',
            'resolved' => 'bg-success',
            'closed' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'bg-danger',
            'medium' => 'bg-warning',
            'low' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function isEditable(): bool
    {
        return $this->status !== 'closed';
    }

    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }
}