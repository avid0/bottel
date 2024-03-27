<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBottelSymbolicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bottel_symbolic', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('file');
            $table->string('mime_type');
            $table->string('filename');
            $table->integer('file_modified_at');
            $table->boolean('keep_alive_modifies');
            $table->boolean('one_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bottel_symbolic');
    }
}
