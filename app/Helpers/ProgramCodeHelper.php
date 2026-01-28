<?php

namespace App\Helpers;
use App\Models\ProgramEducationalObjective;
use App\Models\ProgramOutcome;

class ProgramCodeHelper
{
    public static function numberToLetter(int $number): string
    {
        $letter = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $letter = chr(97 + $remainder) . $letter;
            $number = floor(($number - 1) / 26);
        }
        return $letter;
    }

    public static function resequencePoCodes(int $programId)
    {
        $pos = ProgramOutcome::where('program_id', $programId)->orderBy('id')->get();
        foreach ($pos as $index => $po) {
            $po->update(['po_code' => ProgramCodeHelper::numberToLetter($index + 1)]); // 1 (a), 2 (b), 3 (c), ...
        }
    }
    
    public static function resequencePeoCodes(int $programId)
    {
        $peos = ProgramEducationalObjective::where('program_id', $programId)->orderBy('id')->get();
        foreach ($peos as $index => $peo) {
            $peo->update(['peo_code' => 'PEO' . ($index + 1)]); // PEO1, PEO2, ...
        }
    }
}
