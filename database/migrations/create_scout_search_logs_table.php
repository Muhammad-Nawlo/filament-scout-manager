<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scout_search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->string('model_type')->nullable();
            $table->integer('result_count')->default(0);
            $table->float('execution_time')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('successful')->default(true);
            $table->timestamps();

            $table->index('query');
            $table->index('created_at');
            $table->index(['model_type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('scout_search_logs');
    }
};
