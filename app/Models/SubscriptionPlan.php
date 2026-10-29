<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['features' => 'array', 'is_active' => 'boolean', 'is_popular' => 'boolean', 'price' => 'decimal:2'];
}
