<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->foreignId('category_id')->index()->nullable()->constrained()->onDelete('set null');
            $table->foreignId('author_id')->index()->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_published')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
