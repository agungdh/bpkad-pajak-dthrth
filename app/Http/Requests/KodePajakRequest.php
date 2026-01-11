<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KodePajakRequest extends FormRequest
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
        $kodePajakId = $this->route('kode_pajak')?->id;

        return [
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kode_pajaks', 'kode')
                    ->whereNull('deleted_at')
                    ->ignore($kodePajakId),
            ],
            'nama' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode pajak wajib diisi.',
            'kode.unique' => 'Kode pajak sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
        ];
    }
}
