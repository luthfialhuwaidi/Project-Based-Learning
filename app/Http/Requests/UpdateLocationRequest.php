<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPetugas();
    }

    public function rules(): array
    {
        return [
            'delivery_id' => ['required', 'exists:deliveries,id'],
            'latitude'    => ['required', 'numeric', 'between:-90,90'],
            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'accuracy'    => ['nullable', 'numeric', 'min:0'],
            'speed'       => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_id.required' => 'ID pengiriman wajib diisi.',
            'delivery_id.exists'   => 'Pengiriman tidak ditemukan.',
            'latitude.required'    => 'Latitude wajib diisi.',
            'latitude.between'     => 'Latitude harus antara -90 dan 90.',
            'longitude.required'   => 'Longitude wajib diisi.',
            'longitude.between'    => 'Longitude harus antara -180 dan 180.',
        ];
    }
}
