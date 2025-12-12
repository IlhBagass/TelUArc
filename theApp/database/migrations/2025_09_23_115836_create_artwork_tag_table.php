<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtworkTagTable extends Migration
{
    public function up()
    {
        Schema::create('artwork_tag', function (Blueprint $table) {
            $table->foreignId('artwork_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            
            $table->primary(['artwork_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('artwork_tag');
    }
}