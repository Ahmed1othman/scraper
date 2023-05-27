<?php

namespace App\Http\Requests\Web;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
                    'name' => 'required',
                    'phone' => 'required|numeric|digits:11|unique:users,phone',
                    'email' => 'nullable|email|sometimes',
                    'status' => 'required|boolean',
                ];
            }
            case 'PATCH':
            case 'PUT':
            {
                return [
                    'name' => 'required',
                    'phone' => 'required|numeric|digits:11|unique:users,phone,'.$this->id,
                    'email' => 'nullable|email|sometimes',
                    'status' => 'required|boolean'
                ];
            }
            default:
                break;
        }
    }
}
