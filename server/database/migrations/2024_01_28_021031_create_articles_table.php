<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'articles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('author_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->string('title');
                $table->string('description');
                $table->text('body');
                $table->timestamps();
                $table->string('date_slug')->unique();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
