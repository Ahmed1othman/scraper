<?php

namespace App\Http\Requests\Web;

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
//                    'product_name' => 'required',
                    'price' => 'required|numeric',
//                    'platform' => 'required',
                    'url' => 'required|url',
                    'status' => 'required|boolean',
                ];
            }
            case 'PATCH':
            case 'PUT':
            {
                return [
                    'price' => 'required|numeric',
                    'platform' => 'required',
                    'url' => 'required|url',
                    'status' => 'required|boolean',
                ];
            }
            default:
                break;
        }
    }

}
