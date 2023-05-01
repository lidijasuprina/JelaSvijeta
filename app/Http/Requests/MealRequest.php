<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Language;
use App\Rules\CategoryRule;

class MealRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $lang = '';
        foreach (Language::all() as $language) {
            $lang .= $language->code . ',';
        }
        return [
            'lang' => "required|in:$lang",
            'per_page' => 'sometimes|required|integer|min:1',
            'page' => 'sometimes|required|integer|min:1',
            'category' => ['sometimes', new CategoryRule],
            'tags' => 'sometimes|required|regex:/^\d+(,\d+)*$/',
            'with' => 'sometimes|required|in:category,tags,ingredients',
            'diff_time' => 'sometimes|required|integer|min:1'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => $validator->errors()->all()
            ], 400)
        );
    }
}
