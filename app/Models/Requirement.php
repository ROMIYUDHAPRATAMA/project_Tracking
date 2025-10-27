<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'type',
        'priority',
        'status',
        'acceptance_criteria',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }public function designSpecs() {
  return $this->hasMany(DesignSpec::class);
}

}
