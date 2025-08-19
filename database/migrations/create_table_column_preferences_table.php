<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class {
    public function up(): void
    {
        Schema::create('table_column_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('table_name')->index();
            $table->json('visible_columns')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'table_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_column_preferences');
    }
};