<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scout_synonyms', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->index();
            $table->string('word')->index();
            $table->json('synonyms');
            $table->json('engine_settings')->nullable();
            $table->timestamps();

            $table->unique(['model_type', 'word']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('scout_synonyms');
    }
};
