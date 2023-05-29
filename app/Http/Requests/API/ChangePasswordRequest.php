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
                    'name' => 'required',
                    'phone' => 'required|numeric|max:11|unique:users,phone',
                    'email' => 'nullable|email|sometimes',
                    'status' => 'required|boolean'
                ];
            }
            default:
                break;
        }
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
