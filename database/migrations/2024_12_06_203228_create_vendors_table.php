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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('mobile',10)->unique();
            $table->json('name',40);
            $table->string('password');
            $table->string('photo',150);
            $table->string('email',100)->nullable();
            $table->text('address')->nullable();
            $table->text('longitude')->nullable();
            $table->text('latitude')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->timestamps();

            $table->unsignedBigInteger('main_category_id');

            $table->foreign('main_category_id')->references('id')->on('main_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
