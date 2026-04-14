<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('platforms');                         // ["instagram","facebook"]
            $table->json('connected_account_ids');             // [1, 2]
            $table->text('caption');
            $table->string('image_url')->nullable();
            $table->string('image_path')->nullable();          // storage path
            $table->string('status')->default('scheduled');    // draft|scheduled|publishing|published|failed
            $table->timestamp('scheduled_at');
            $table->timestamp('published_at')->nullable();
            $table->json('platform_post_ids')->nullable();     // {"instagram":"123","facebook":"456"}
            $table->integer('retry_count')->default(0);
            $table->text('failure_reason')->nullable();
            $table->boolean('ai_generated')->default(false);
            $table->string('festival')->nullable();
            $table->string('language')->default('hinglish');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'scheduled_at']);         // for queue worker
            $table->index(['user_id', 'scheduled_at']);        // for calendar
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
