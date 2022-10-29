<?php

use App\Models\VariableCost;
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
        Schema::table('variable_costs', function (Blueprint $table) {
            $table->after('qty', function (Blueprint $table) {
                $table->unsignedBigInteger('min_qty')->default(null)->nullable();
            });
        });

        VariableCost::get()->each(fn ($item) => $item->update(['min_qty' => $item->qty]));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variable_costs', function (Blueprint $table) {
            $table->dropColumn('min_qty');
        });
    }
};
