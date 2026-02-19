<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Department|null $department */
        $department = $this->route('department');

        $nameRule = Rule::unique('departments', 'name');

        if ($department) {
            $nameRule->ignore($department->id);
        }

        return [
            'name' => ['required', 'string', $nameRule],
            'college_id' => ['required', 'exists:colleges,id'],
        ];
    }
}
