<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PendaftaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $setting = Setting::where('key', 'min_durasi_magang')->first();
        $minVal = (int) ($setting->value ?? 3);
        $tipe = $setting->type ?? 'bulan';

        $rules = [
            // Posisi Lowongan (Opsional)
            'lowongan_id'    => ['nullable', 'exists:lowongans,id'],

            // 1. Regex hanya huruf dan spasi (No angka/simbol)
            'nama_pendaftar' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'asal_kampus'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'prodi'          => ['required', 'string', 'max:255'],

            // 2. Tanggal Mulai: hari ini s.d. 7 bulan ke depan
            'tanggal_mulai' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . Carbon::today()->addMonths(7)->toDateString(),
            ],

            // 3. Tanggal Selesai: setelah mulai & minimal durasi sesuai setting
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
                function ($attribute, $value, $fail) use ($minVal, $tipe) {
                    $mulaiInput = $this->input('tanggal_mulai');
                    if (!$mulaiInput) {
                        return;
                    }

                    $mulai = Carbon::parse($mulaiInput);
                    $selesai = Carbon::parse($value);

                    $diff = ($tipe === 'bulan') ? $mulai->diffInMonths($selesai) : $mulai->diffInDays($selesai);

                    if ($diff < $minVal) {
                        $fail("Maaf, durasi magang minimal adalah $minVal $tipe.");
                    }
                },
            ],
        ];

        // Validasi Berkas (Kondisional Create vs Update)
        if ($isUpdate) {
            $rules['surat_permohonan'] = ['nullable', 'file', 'mimes:pdf', 'max:2048'];
            $rules['surat_kampus']     = ['nullable', 'file', 'mimes:pdf', 'max:2048'];
            $rules['pas_foto']         = ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:1024'];
        } else {
            $rules['surat_permohonan'] = ['required', 'file', 'mimes:pdf', 'max:2048'];
            $rules['surat_kampus']     = ['required', 'file', 'mimes:pdf', 'max:2048'];
            $rules['pas_foto']         = ['required', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:1024'];
        }

        return $rules;
    }

    /**
     * Custom error messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'date'     => 'Format :attribute tidak valid.',
            'string'   => ':attribute harus berupa teks.',
            'max'      => ':attribute maksimal :max.',
            'exists'   => ':attribute yang dipilih tidak valid.',

            // Custom Message untuk Tanggal
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh tanggal lampau (harus hari ini atau masa depan).',
            'tanggal_mulai.before_or_equal'  => 'Tanggal mulai maksimal 7 bulan dari hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',

            // Custom Message untuk Regex
            'nama_pendaftar.regex' => 'Nama hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',
            'asal_kampus.regex'    => 'Nama kampus hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',

            // Validasi File
            'surat_permohonan.max'   => 'Ukuran surat permohonan maksimal 2MB.',
            'surat_permohonan.mimes' => 'Surat permohonan harus format PDF.',
            'surat_kampus.max'       => 'Ukuran surat kampus maksimal 2MB.',
            'surat_kampus.mimes'     => 'Surat kampus harus format PDF.',
            'pas_foto.max'           => 'Ukuran pas foto maksimal 1MB.',
            'pas_foto.mimes'         => 'Pas foto harus format JPEG, PNG, atau JPG.',
            'pas_foto.image'         => 'File harus berupa gambar.',
        ];
    }

    /**
     * Custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'lowongan_id'      => 'Posisi / Lowongan',
            'nama_pendaftar'   => 'Nama pendaftar',
            'asal_kampus'      => 'Asal kampus',
            'prodi'            => 'Program studi',
            'tanggal_mulai'    => 'Tanggal mulai',
            'tanggal_selesai'  => 'Tanggal selesai',
            'surat_permohonan' => 'Surat permohonan',
            'surat_kampus'     => 'Surat kampus',
            'pas_foto'         => 'Pas foto',
        ];
    }
}
