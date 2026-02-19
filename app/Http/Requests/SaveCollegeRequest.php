<?php

namespace App\Http\Requests;

use App\Models\College;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCollegeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var College|null $college */
        $college = $this->route('college');

        $nameRule = Rule::unique('colleges', 'name');

        if ($college) {
            $nameRule->ignore($college->id);
        }

        return [
            'name' => ['required', 'string', $nameRule],
        ];
    }
}
