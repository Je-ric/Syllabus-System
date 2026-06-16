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

    /**
     * Resequence PO codes in the given ordered ID list.
     * Nulls all codes first to avoid unique constraint collisions during swap.
     */
    public static function resequencePoCodesOrdered(int $programId, array $orderedIds): void
    {
        // Clear all codes first (nullable) to avoid unique constraint collisions
        ProgramOutcome::where('program_id', $programId)->update(['po_code' => null]);
        foreach ($orderedIds as $position => $id) {
            ProgramOutcome::where('id', $id)
                ->update(['po_code' => self::numberToLetter($position + 1)]);
        }
    }

    /**
     * Resequence PEO codes in the given ordered ID list.
     * Nulls all codes first to avoid unique constraint collisions during swap.
     */
    public static function resequencePeoCodesOrdered(int $programId, array $orderedIds): void
    {
        // Clear all codes first (nullable) to avoid unique constraint collisions
        ProgramEducationalObjective::where('program_id', $programId)->update(['peo_code' => null]);
        foreach ($orderedIds as $position => $id) {
            ProgramEducationalObjective::where('id', $id)
                ->update(['peo_code' => self::numberToLetter($position + 1)]);
        }
    }

    /** @deprecated Use resequencePoCodesOrdered instead */
    public static function resequencePoCodes(int $programId): void
    {
        $ids = ProgramOutcome::where('program_id', $programId)->orderBy('id')->pluck('id')->all();
        self::resequencePoCodesOrdered($programId, $ids);
    }

    /** @deprecated Use resequencePeoCodesOrdered instead */
    public static function resequencePeoCodes(int $programId): void
    {
        $ids = ProgramEducationalObjective::where('program_id', $programId)->orderBy('id')->pluck('id')->all();
        self::resequencePeoCodesOrdered($programId, $ids);
    }
}
