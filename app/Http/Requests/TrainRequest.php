<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainRequest extends FormRequest
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
            'trains'           => ['array'],
            'trains.*.station' => ['string'],
            'trains.*.date'    => ['date'],
            'trains.*.time'    => ['string'],
            'trains.*.trainID' => ['string'],
        ];
    }
}
