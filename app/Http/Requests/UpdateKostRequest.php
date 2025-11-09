<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'price_per_month' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'max:2048'],
            'remove_photos' => ['nullable', 'array'],
            'remove_photos.*' => [
                'integer',
                Rule::exists('kost_photos', 'id')->where(function ($query) {
                    $kost = $this->route('kost');

                    return $query->where('kost_id', $kost?->id ?? 0);
                }),
            ],
            'map_embed' => ['nullable', 'string'],
        ];
    }
}