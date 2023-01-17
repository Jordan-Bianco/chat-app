<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:30',
                'unique:groups,name,' . $this->id,

                function ($attribute, $value, $fail) {
                    if (!preg_match('/\pL/u', $value) && !preg_match('/\pN/u', $value)) {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                },
            ],
            'description' => ['required', 'string', 'max:500']
        ];
    }
}
