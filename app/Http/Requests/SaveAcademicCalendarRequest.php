<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveAcademicCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $academicYear = $this->route('academicYear');

        $academicYearRule = Rule::unique('academic_calendars', 'academic_year');

        if ($academicYear) {
            $academicYearRule->ignore($academicYear, 'academic_year');
        }

        return [
            'academic_year' => ['required', 'string', $academicYearRule],
            'start_date_1' => ['required', 'date'],
            'end_date_1' => ['required', 'date', 'after_or_equal:start_date_1'],
            'start_date_2' => ['required', 'date'],
            'end_date_2' => ['required', 'date', 'after_or_equal:start_date_2'],
        ];
    }
}
