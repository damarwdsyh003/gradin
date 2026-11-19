<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 30)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('address', 500)->nullable();
            $table->unsignedTinyInteger('level');
            $table->date('joined_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['level', 'is_active']);
            $table->index('joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
