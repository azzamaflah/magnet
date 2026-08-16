<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LowonganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'judul_posisi' => ['required', 'string', 'max:255'],
            'divisi'       => ['required', 'string', 'max:255'],
            'deskripsi'    => ['required', 'string', 'max:5000'],
            'kualifikasi'  => ['required', 'string', 'max:5000'],
            'kuota'        => ['required', 'integer', 'min:1', 'max:50'],
            'status'       => ['required', 'in:buka,tutup'],
        ];
    }

    /**
     * Custom Indonesian messages.
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string'   => ':attribute harus berupa teks.',
            'max'      => ':attribute maksimal :max karakter.',
            'integer'  => ':attribute harus berupa angka bulat.',
            'min'      => ':attribute minimal :min.',
            'in'       => 'Status harus berupa Buka atau Tutup.',
        ];
    }

    /**
     * Custom attributes.
     */
    public function attributes(): array
    {
        return [
            'judul_posisi' => 'Judul posisi',
            'divisi'       => 'Divisi / Seksi',
            'deskripsi'    => 'Deskripsi pekerjaan',
            'kualifikasi'  => 'Kualifikasi',
            'kuota'        => 'Kuota peserta',
            'status'       => 'Status lowongan',
        ];
    }
}
