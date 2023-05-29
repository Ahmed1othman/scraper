<?php

namespace App\Http\Requests\API;

use App\Rules\CheckOldPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'status' => $this->has('status'),
        ]);
    }


    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'old_password' => ['required', new CheckOldPassword],
                    'new_password' => 'required',
                ];
            }
            case 'PATCH':
            case 'PUT':
            {
                return [
                    'old_password' => ['required', new CheckOldPassword],
                    'new_password' => 'required'
                ];
            }
            default:
                break;
        }
    }

    public function messages()
    {
        return [
            'old_password.required' => trans('messages.كلمة السر القديمة مطلوبة'),
            'new_password.required' => trans('messages.كلمة السر القديمة مطلوبة'),
            'name.min' => trans('messages.visitor_name_at_least_3'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
