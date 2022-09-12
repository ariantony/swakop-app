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
        Schema::create('variable_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                    ->constrained()
                    ->cascadeOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedDouble('price', 20, 3);
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
        Schema::dropIfExists('variable_costs');
    }
};
