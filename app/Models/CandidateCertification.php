<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateCertification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'candidate_certifications';

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_current' => 'boolean', 'issued_date' => 'date', 'expiry_date' => 'date'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_profile_id');
    }
}
