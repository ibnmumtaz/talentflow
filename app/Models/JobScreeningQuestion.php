<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobScreeningQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['options' => 'array', 'is_required' => 'boolean'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ApplicationAnswer::class, 'question_id');
    }
}
