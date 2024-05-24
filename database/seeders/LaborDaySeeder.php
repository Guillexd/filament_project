<?php

namespace Database\Seeders;

use App\Models\LaborDay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LaborDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LaborDay::create([
            'mounth' => 'Enero',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Febrero',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Marzo',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Abril',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Mayo',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Junio',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Julio',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Agosto',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Setiembre',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Octubre',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Noviembre',
            'quantity' => 20
        ]);
        laborday::create([
            'mounth' => 'Diciembre',
            'quantity' => 20
        ]);
    }
}
