<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Course|null $course */
        $course = $this->route('course');

        $courseCodeRule = Rule::unique('courses', 'course_code');

        if ($course) {
            $courseCodeRule->ignore($course->id);
        }

        // confirmed_submission default to 0

        return [
            'program_id' => [$course ? 'sometimes' : 'required', 'exists:programs,id'],
            'confirmed_submission' => ['accepted'],
            'code' => ['required', 'string', $courseCodeRule],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'credits' => ['required', 'integer', 'min:1'],
            'has_lec_lab' => ['nullable', 'boolean'],
            'year_level' => ['nullable', 'integer', 'between:1,5'],
            'semester' => ['nullable', 'integer', 'in:1,2'],
            'prerequisite' => ['nullable', 'string'],
            'corequisite' => ['nullable', 'string'],
            'po_mapping' => ['nullable', 'array'],
            'po_mapping.*' => ['nullable', 'in:I,E,D'],
        ];
    }
}
