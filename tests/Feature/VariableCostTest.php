<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VariableCostTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_variable_cost_algoritm()
    {
        $vars = collect([
            [
                'qty' => 12,
                'cost' => 10_000,
            ],

            [
                'qty' => 9,
                'cost' => 11_000,
            ],

            [
                'qty' => 6,
                'cost' => 12_000,
            ],

            [
                'qty' => 3,
                'cost' => 13_000,
            ],
        ]);

        $unitPrice = 15_000;
        $totalPrice = 0;
        $buy = 183;
        
        while ($buy > 0) {
            /**
             * @var \Illuminate\Support\Collection<string, int> $var
             */
            $var = $vars->sortBy('qty', descending: true)->where('qty', '<=', $buy)->first();

            if ($var) {
                $buy -= $var['qty'];
                $totalPrice += $var['cost'];
            } else {
                $buy -= 1;
                $totalPrice += $unitPrice;
            }
        }
        
        $this->assertEquals(163_000, $totalPrice);
    }
}
