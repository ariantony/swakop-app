<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->double('cost_selling_per_unit', 20, null, true)->default(0);
            $table->double('cost_selling_per_box', 20, null, true)->default(0);
            $table->double('cost_selling_per_carton', 20, null, true)->default(0);
            $table->double('price_per_unit', 20, null, true)->default(0);
            $table->double('price_per_box', 20, null, true)->default(0);
            $table->double('price_per_carton', 20, null, true)->default(0);
            $table->date('effective_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('expire_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
};
