<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    public function run(): void
    {
        $instruments = [
            // Houtblazers (woodwinds)
            'Piccolo',
            'Dwarsfluit',
            'Hobo',
            'Klarinet',
            'Basklarinet',
            'Fagot',
            'Altsaxofoon',
            'Tenorsaxofoon',
            'Baritonsaxofoon',
            // Koperblazers (brass)
            'Trompet',
            'Hoorn',
            'Trombone',
            'Bastrombone',
            'Euphonium',
            'Tuba',
            'Contrabas',
            // Slagwerk (percussion)
            'Slagwerk',
        ];

        foreach ($instruments as $index => $name) {
            Instrument::create([
                'name' => $name,
                'display_order' => $index + 1,
            ]);
        }
    }
}
