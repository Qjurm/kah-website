<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    public function run(): void
    {
        $instruments = [
            // Houtblazers
            'Piccolo',
            'Dwarsfluit',
            'Hobo',
            'Klarinet 1',
            'Klarinet 2',
            'Klarinet 3',
            'Basklarinet',
            'Fagot',
            'Altsaxofoon 1',
            'Altsaxofoon 2',
            'Tenorsaxofoon',
            'Baritonsaxofoon',
            // Koperblazers
            'Trompet 1',
            'Trompet 2',
            'Trompet 3',
            'Hoorn 1',
            'Hoorn 2',
            'Hoorn 3',
            'Trombone 1',
            'Trombone 2',
            'Bastrombone',
            'Euphonium',
            'Tuba',
            'Contrabas',
            // Slagwerk
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
