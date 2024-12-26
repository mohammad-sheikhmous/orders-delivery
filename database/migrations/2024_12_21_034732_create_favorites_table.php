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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

//            $table->timestamps();

//              onDelete Options ::
//              restrict: Prevents deletion of the parent record if there are child records referencing it.
//              set null: Sets the foreign key in the child table to NULL when the parent record is deleted.
//              no action: No action is taken when the parent record is deleted. This is the default
//              behavior if you don’t specify onDelete.
//              cascade:When the parent record (e.g., a user or product) is deleted, all associated
//              child records (e.g., entries in the favorites table) are automatically deleted as well
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
