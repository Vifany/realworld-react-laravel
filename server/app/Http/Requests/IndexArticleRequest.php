<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexArticleRequest extends FormRequest
{
    /**
     * Rewritten all() to force default values
     *
     * @return array $requestData
     */
    public function all($keys = null)
    {
        $requestData = parent::all($keys);

        // Define default values for fields if they don't exist in the request
        $defaults = [
            'tag' => null,
            'author' => null,
            'favorited' => null,
            'limit' => 20,
            'offset' => 0,
        ];

        // Merge the default values with the request data
        $requestData = array_merge($defaults, $requestData);

        return $requestData;
    }


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tag' => ['nullable', 'string'],
            'author' => ['nullable', 'string'],
            'favorited' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'gt:0'],
            'offset' => ['nullable', 'integer', 'gte:0'],
        ];
    }
}
