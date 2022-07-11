<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['buy', 'sell']);
            $table->unsignedInteger('qty_unit')->nullable()->default(null);
            $table->unsignedInteger('qty_box')->nullable()->default(null);
            $table->unsignedInteger('qty_carton')->nullable()->default(null);
            $table->unsignedDouble('cost_unit', 20)->nullable()->default(null);
            $table->unsignedDouble('cost_box', 20)->nullable()->default(null);
            $table->unsignedDouble('cost_carton', 20)->nullable()->default(null);
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
        Schema::dropIfExists('details');
    }
};
