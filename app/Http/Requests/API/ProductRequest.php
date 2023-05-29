<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
            'status' => $this->boolean('status'),
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
//                    'product_name' => 'required',
                    'price' => 'required|numeric',
//                    'platform' => 'required',
                    'status' => 'required|boolean',
                ];
            }
            case 'PATCH':
            case 'PUT':
            {
                return [
                    'product_id' => 'required|exists:products,id',
                    'price' => 'required|numeric',
                    'status' => 'required|boolean',
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
