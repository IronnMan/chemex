<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumable_records', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('specification');
            $table->integer('category_id');
            $table->integer('vendor_id');
            $table->double('price')->nullable();
            $table->string('extended_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('consumable_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->string('extended_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('consumable_tracks', function (Blueprint $table) {
            $table->id();
            $table->integer('consumable_id');
            $table->string('operator')->default(0);
            $table->integer('staff_id')->nullable();
            $table->double('number');
            $table->double('change');
            $table->date('purchased')->nullable();
            $table->date('expired')->nullable();
            $table->string('extended_fields')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('consumable_records');
        Schema::dropIfExists('consumable_categories');
        Schema::dropIfExists('consumable_tracks');
    }
}
