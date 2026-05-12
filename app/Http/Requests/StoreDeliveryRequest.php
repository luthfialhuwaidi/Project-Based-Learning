<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPetugas();
    }

    public function rules(): array
    {
        return [
            'school_id'      => ['required', 'exists:schools,id'],
            'total_portions' => ['required', 'integer', 'min:1', 'max:500'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required'      => 'Sekolah tujuan wajib dipilih.',
            'school_id.exists'        => 'Sekolah yang dipilih tidak valid.',
            'total_portions.required' => 'Jumlah porsi wajib diisi.',
            'total_portions.min'      => 'Jumlah porsi minimal 1.',
            'total_portions.max'      => 'Jumlah porsi maksimal 500.',
        ];
    }
}
