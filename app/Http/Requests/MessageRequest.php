<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'body' => [
                'nullable',
                'required_if:attachment,=,null',
                'max:1000'
            ],
            'attachment' => [
                'nullable',
                'required_if:body,=,null',
                'mimes:jpeg,jpg,png,pdf,xlsx,xls,doc,docx,txt',
            ]
        ];
    }
}
