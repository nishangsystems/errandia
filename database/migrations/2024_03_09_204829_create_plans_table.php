<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('plan')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description');
                $table->integer('unit_price');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('plan')) {
            Schema::dropIfExists('plans');
        }
    }
}