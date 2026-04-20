<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstrumentSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            ['name' => 'Hout', 'display_order' => 1],
            ['name' => 'Koper', 'display_order' => 2],
            ['name' => 'Slagwerk', 'display_order' => 3],
            ['name' => 'Overig', 'display_order' => 4],
        ];

        foreach ($sections as $section) {
            \App\Models\InstrumentSection::updateOrCreate(
                ['name' => $section['name']],
                ['display_order' => $section['display_order']]
            );
        }

        // Assign existing instruments to sections based on the old hardcoded logic
        $this->assignExistingInstruments();
    }

    private function assignExistingInstruments()
    {
        $hout = \App\Models\InstrumentSection::where('name', 'Hout')->first();
        $koper = \App\Models\InstrumentSection::where('name', 'Koper')->first();
        $slagwerk = \App\Models\InstrumentSection::where('name', 'Slagwerk')->first();
        $overig = \App\Models\InstrumentSection::where('name', 'Overig')->first();

        $instrumentFamilies = [
            ['section' => $hout, 'keywords' => ['fluit', 'piccolo', 'hobo', 'fagot', 'klarinet', 'saxofoon']],
            ['section' => $koper, 'keywords' => ['trompet', 'hoorn', 'trombone', 'euphonium', 'tuba', 'cornet', 'bugel']],
            ['section' => $slagwerk, 'keywords' => ['pauken', 'percussie', 'slagwerk', 'mallets', 'drums']],
        ];

        $instruments = \App\Models\Instrument::all();

        foreach ($instruments as $inst) {
            $name = strtolower($inst->name);
            $found = false;
            foreach ($instrumentFamilies as $family) {
                foreach ($family['keywords'] as $keyword) {
                    if (str_contains($name, $keyword)) {
                        $inst->update(['section_id' => $family['section']->id]);
                        $found = true;
                        break 2;
                    }
                }
            }
            if (!$found && $overig) {
                $inst->update(['section_id' => $overig->id]);
            }
        }
    }
}
