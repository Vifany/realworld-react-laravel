<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest
{
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
            'article.title' =>['required','string','max:256'],
            'article.description' =>['required','string','max:512'],
            'article.body' =>['required','string'],
            'article.tagList' =>['nullable','array'],
            'article.tagList.*'=>['string', 'max:32'],
        ];
    }
}
