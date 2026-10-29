<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('candidate')->after('password');
            $table->string('phone', 30)->nullable()->after('role');
            $table->string('avatar_path')->nullable()->after('phone');
            $table->boolean('is_suspended')->default(false)->after('avatar_path');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->string('suspension_reason')->nullable()->after('suspended_at');
            $table->timestamp('last_login_at')->nullable()->after('suspension_reason');
            $table->timestamp('onboarding_completed_at')->nullable()->after('last_login_at');
            $table->string('timezone', 60)->default('Asia/Karachi')->after('onboarding_completed_at');
            $table->json('notification_preferences')->nullable()->after('timezone');
            $table->index(['role', 'is_suspended']);
        });

        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 60)->default('building');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 60)->default('briefcase');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country', 80)->default('Pakistan');
            $table->string('region')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('PKR');
            $table->string('billing_period', 20)->default('monthly');
            $table->integer('job_posts_limit')->default(1);
            $table->integer('featured_jobs_limit')->default(0);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group', 40)->default('general');
            $table->timestamps();
        });
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('audience', 20)->default('all');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('category', 60)->default('Career Advice');
            $table->string('cover_image')->nullable();
            $table->integer('reading_time_minutes')->default(5);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['is_published', 'published_at']);
        });
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role_title')->nullable();
            $table->string('company')->nullable();
            $table->text('quote');
            $table->integer('rating')->default(5);
            $table->string('avatar_initials', 5)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->boolean('consent_given')->default(true);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('industries');
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_suspended']);
            $table->dropColumn(['role', 'phone', 'avatar_path', 'is_suspended', 'suspended_at', 'suspension_reason', 'last_login_at', 'onboarding_completed_at', 'timezone', 'notification_preferences']);
        });
    }
};
