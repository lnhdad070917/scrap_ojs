<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('author');
            $table->string('pdf_link');
            $table->string('web_link');
            $table->string('doi');
            $table->string('pages');
            $table->unsignedBigInteger('link_id')->nullable();
            $table->foreign('link_id')->references('id')->on('link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};