<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->foreignId('base_prompt_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->index()->nullable()->constrained()->onDelete('set null');
            $table->foreignId('author_id')->index()->nullable()->constrained()->onDelete('set null');

            $table->string('driver')->nullable();
            $table->string('model')->nullable();

            $table->string('status', 20)->default(\App\Models\Post::STATUS_CREATED);
            $table->timestamp('status_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
