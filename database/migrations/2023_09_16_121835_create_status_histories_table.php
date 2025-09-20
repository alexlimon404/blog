<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->nullable();

            $table->morphs('resource');
            $table->string('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }
};
