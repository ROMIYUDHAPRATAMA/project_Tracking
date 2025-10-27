<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'requirement_id',
        'artifact_type',
        'artifact_name',
        'reference_url',
        'rationale',
        'status',
    ];

    /**
     * Relasi ke Project (banyak DesignSpec dalam satu Project)
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke Requirement (DesignSpec terkait Requirement tertentu)
     */
    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }
}
