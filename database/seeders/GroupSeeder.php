<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $group = [
            'DPR' => 'diapers',
            'HCR' => 'detergent / home care',
            'BDC' => 'body care',
            'MDC' => 'obat obatan',
            'BBC' => 'baby care',
            'TSE' => 'tissue',
            'MIN' => 'mie instan',
            'CDM' => 'condiment',
            'CNC' => 'coklat + permen',
            'SNB' => 'snack + biscuits',
            'DRK' => 'minuman / drink',
            'MBF' => 'milk baby food',
            'CNT' => 'kopi + teh',
            'ICM' => 'ice cream',
            'RKK' => 'rokok',
            'ATK' => 'alat tulis kantor',
            'BKR' => 'roti / bakery',
        ];

        foreach ($group as $key => $value) {
            Group::create([
                'name' => $value,
                'code' => $key,
            ]);
        }
    }
}
