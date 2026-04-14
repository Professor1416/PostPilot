<?php
// ============================================================
// FILE: database/migrations/2024_01_01_000001_create_users_table.php
// ============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('photo_url')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->string('plan')->default('free');          // free|starter|growth|agency
            $table->integer('post_quota')->default(5);
            $table->integer('posts_used_this_month')->default(0);
            $table->date('quota_reset_date')->nullable();
            $table->integer('account_limit')->default(1);
            $table->integer('total_posts_scheduled')->default(0);
            $table->integer('total_posts_published')->default(0);
            $table->boolean('onboarding_complete')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
