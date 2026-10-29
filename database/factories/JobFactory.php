<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JobFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->jobTitle();

        return [
            'company_id' => Company::factory(),
            'posted_by_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'description' => fake()->paragraphs(3, true),
            'employment_type' => 'full_time',
            'workplace_type' => 'onsite',
            'experience_level' => 'mid',
            'salary_min' => 150000,
            'salary_max' => 250000,
            'status' => 'published',
            'published_at' => now(),
        ];
    }
}
