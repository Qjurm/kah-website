<?php

namespace App\Actions;

use App\Models\Score;
use App\Models\ScorePart;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadScorePartAction
{
    /**
     * Handle the download of a score part.
     *
     * @param  Score  $score
     * @param  ScorePart  $part
     * @return StreamedResponse|void
     */
    public function execute(Score $score, ScorePart $part)
    {
        abort_if($part->score_id !== $score->id, 404);

        $filename = $this->generateFilename($score, $part);

        $disk = config('filesystems.scores.driver') === 'local' ? 'public' : 'scores';
        
        if (!Storage::disk($disk)->exists($part->pdf_path)) {
            abort(404, 'Bestand niet gevonden');
        }

        return Storage::disk($disk)->download($part->pdf_path, $filename . '.pdf');
    }

    /**
     * Generate the filename for the download.
     *
     * @param  Score  $score
     * @param  ScorePart  $part
     * @return string
     */
    private function generateFilename(Score $score, ScorePart $part): string
    {
        // Use original filename without extension if available
        if ($part->original_filename) {
            return pathinfo($part->original_filename, PATHINFO_FILENAME);
        }

        // Fallback to: [Score Title] - [Instrument Name] [Part Number]
        $instrumentName = $part->instrument?->name ?? $part->instrument;
        $name = $score->title . ' - ' . $instrumentName;

        if ($part->part_number && $part->part_number > 1) {
            $name .= ' ' . $part->part_number;
        }

        return $name;
    }
}
