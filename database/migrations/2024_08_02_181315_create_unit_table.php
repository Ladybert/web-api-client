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
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->json('image')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('unit_type_id');
            $table->text('description');
            $table->string('size');
            $table->string('city');
            $table->string('province');
            $table->string('address');
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('unit_type_id')->references('id')->on('unit_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};
