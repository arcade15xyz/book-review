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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('book_id');

            $table->text('review');
            $table->unsignedTinyInteger('rating');

            $table->timestamps();

            //Creating the foreign key like column "reference" to column "on" nthe table and "on Delete" what action will happen what cascade means is that on deletion of the id the reviews related to it also be removed i.e. reviews are asssociated with a book always(must)
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            //a short hand syntax which will rep,ace line 17 and line 25
            //$table->foreignId('book_id')->constrained()->cascadeOnDelete

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
