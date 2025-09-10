<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'last_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'max:255'],
            'document_number' => [
                'required',
                'string',
                'max:255', 
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone_number' => ['required', 'string', 'max:255'],
            'academic_formation'   => ['array'],
            'academic_formation.*.institution' => [ 'string', 'max:255'],
            'academic_formation.*.degree'      => [ 'string', 'max:255'],
            'academic_formation.*.startYear' => [ 'integer' ],
            'academic_formation.*.endYear'   => [ 'integer', 'nullable' ],

            'work_experience'   => [ 'array'],
            'work_experience.*.company'    => [ 'string', 'max:255'],
            'work_experience.*.position'   => [ 'string', 'max:255'],
            'work_experience.*.startYear' => [ 'integer' ],
            'work_experience.*.endYear'   => [ 'integer', 'nullable' ],
        ];
    }
}
