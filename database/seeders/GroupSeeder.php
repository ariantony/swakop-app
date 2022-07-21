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
            'HCR' => 'detergent / home care',
            'BDC' => 'body care',
            'BBC' => 'baby care',
            'DPR' => 'diapers',
            'TSE' => 'tissue',
            'CDM' => 'condiment',
            'CNT' => 'coffe & teh',
            'SNB' => 'snack & biscuits',
            'MIN' => 'min instan',
            'MBF' => 'milk body food',
            'CNC' => 'coklat + permen',
            'DRK' => 'minuman / drink',
            'ATK' => 'alat tulis kantor',
            'OBT' => 'obat obatan',
        ];

        foreach ($group as $key => $value) {
            Group::create([
                'name' => $value,
                'code' => $key,
            ]);
        }
    }
}
