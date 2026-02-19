<?php

namespace App\Http\Requests;

use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Program|null $program */
        $program = $this->route('program');

        $nameRule = Rule::unique('programs', 'name');

        if ($program) {
            $nameRule->ignore($program->id);
        }

        return [
            'name' => ['required', 'string', $nameRule],
            'department_id' => ['required', 'exists:departments,id'],
            'bor_approval_no' => ['nullable', 'string'],
            'bor_approval_date' => ['nullable', 'date'],
        ];
    }
}
