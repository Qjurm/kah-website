<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Instrument;
use App\Models\InstrumentAlias;

class InstrumentAliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            'Trompet'          => ['trumpet', 'tpt', 'tromp', 'trp'],
            'Klarinet'         => ['clarinet', 'cl'],
            'Dwarsfluit'       => ['flute', 'fl'],
            'Saxofoon Alt'     => ['alto sax', 'altsax', 'alto saxophone', 'alt sax'],
            'Saxofoon Tenor'   => ['tenor sax', 'tenorsax', 'tenor saxophone'],
            'Saxofoon Bariton' => ['bari sax', 'barisax', 'baritone saxophone', 'bariton sax'],
            'Trombone'         => ['tbn', 'trb'],
            'Hoorn'            => ['horn', 'french horn'],
            'Bariton'          => ['baritone', 'euphonium', 'euph'],
            'Bas'              => ['bass', 'tuba', 'eb bas', 'bb bas'],
            'Percussie'        => ['percussion', 'perc', 'drums', 'drum', 'slagwerk'],
            'Pauken'           => ['timpani', 'timp'],
            'Hobo'             => ['oboe'],
            'Fagot'            => ['bassoon'],
            'Dirigent'         => ['partituur', 'score', 'conductor', 'directie']
        ];

        foreach ($mappings as $instrumentName => $aliases) {
            $instrument = Instrument::where('name', 'LIKE', '%' . $instrumentName . '%')->first();

            if ($instrument) {
                foreach ($aliases as $alias) {
                    InstrumentAlias::firstOrCreate([
                        'alias' => strtolower($alias)
                    ], [
                        'instrument_id' => $instrument->id
                    ]);
                }
            }
        }
    }
}
