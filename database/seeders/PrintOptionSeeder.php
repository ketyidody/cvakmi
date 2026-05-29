<?php

namespace Database\Seeders;

use App\Models\PrintOption;
use Illuminate\Database\Seeder;

class PrintOptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['name' => '10×15 cm', 'price' => 0.50, 'display_order' => 1],
            ['name' => '13×18 cm', 'price' => 0.90, 'display_order' => 2],
            ['name' => '15×21 cm (A5)', 'price' => 1.50, 'display_order' => 3],
            ['name' => '21×30 cm (A4)', 'price' => 3.00, 'display_order' => 4],
            ['name' => 'Digitálna fotografia', 'description' => 'Súbor vo vysokom rozlíšení', 'price' => 1.00, 'display_order' => 5],
        ];

        foreach ($options as $option) {
            PrintOption::firstOrCreate(['name' => $option['name']], $option);
        }
    }
}
