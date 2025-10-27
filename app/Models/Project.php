<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'pic',
        'status',     // todo | in_progress | review | done
        'start_date',
        'end_date',
        'progress',   // 0..100
        'outcome',    // Dokumen/hasil akhir
        'activity',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'progress'   => 'integer',
    ];

    /**
     * Scope: hanya project milik user tertentu (default: user yang sedang login).
     */
    public function scopeMine(Builder $q, ?int $userId = null): Builder
    {
        $uid = $userId ?? auth()->id();
        return $uid ? $q->where('user_id', $uid) : $q;
    }

    /**
     * Relasi: satu project punya banyak Requirement.
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class)->latest();
    }

    /**
     * Relasi: satu project punya banyak Task.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->latest();
    }

    /**
     * Relasi: satu project punya banyak DesignSpec (fase Design).
     */
    public function designSpecs(): HasMany
    {
        return $this->hasMany(DesignSpec::class)->latest();
    }
}
