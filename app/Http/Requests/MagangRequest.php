<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class MagangRequest extends FormRequest
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

        $rules = [
            // Nama: hanya huruf & spasi
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],

            // Kampus: hanya huruf & spasi
            'asal_kampus' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],

            // Prodi: bebas string
            'prodi' => ['required', 'string', 'max:255'],

            // Tanggal Mulai: boleh mundur sampai 1 tahun ke belakang, maksimal 7 bulan ke depan
            'tanggal_mulai' => [
                'required',
                'date',
                'after_or_equal:' . Carbon::today()->subYear()->toDateString(),
                'before_or_equal:' . Carbon::today()->addMonths(7)->toDateString(),
            ],

            // Tanggal Selesai: setelah tanggal mulai, durasi min 1 bulan max 7 bulan
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
                function ($attribute, $value, $fail) {
                    $mulaiInput = $this->input('tanggal_mulai');
                    if (!$mulaiInput) {
                        return;
                    }

                    $mulai = Carbon::parse($mulaiInput);
                    $selesai = Carbon::parse($value);
                    $durasiBulan = $mulai->diffInMonths($selesai);

                    if ($durasiBulan < 1) {
                        $fail('Durasi magang minimal 1 bulan.');
                    } elseif ($durasiBulan > 7) {
                        $fail('Durasi magang maksimal 7 bulan.');
                    }
                },
            ],

            // Link pekerjaan opsional, URL valid
            'link_pekerjaan' => ['nullable', 'url', 'max:500'],

            // Kontak & sosial media (opsional)
            // Whatsapp: Wajib diawali +62, diikuti 8-15 digit angka.
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+62[0-9]{8,15}$/'],

            // IG/Tiktok: username diawali @, 1-30 char
            'instagram' => ['nullable', 'string', 'max:100', 'regex:/^@[a-zA-Z0-9._]{1,30}$/'],
            'tiktok'    => ['nullable', 'string', 'max:100', 'regex:/^@[a-zA-Z0-9._]{1,30}$/'],

            // Text area
            'kesan' => ['nullable', 'string', 'max:2000'],
            'pesan' => ['nullable', 'string', 'max:2000'],

            // Visibilitas data (Admin only)
            'is_hidden' => ['nullable', 'boolean'],
        ];

        // File foto: wajib saat create, nullable saat update
        if ($isUpdate) {
            $rules['foto'] = ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'];
        } else {
            $rules['foto'] = ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'];
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
            'string'   => ':attribute harus berupa teks.',
            'max'      => ':attribute maksimal :max karakter.',
            'date'     => 'Format :attribute tidak valid.',

            // Regex khusus
            'nama.regex'        => 'Nama hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',
            'asal_kampus.regex' => 'Nama kampus hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',

            // Tanggal khusus
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai maksimal boleh 1 tahun ke belakang.',
            'tanggal_mulai.before_or_equal'  => 'Tanggal mulai maksimal 7 bulan ke depan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',

            // Foto
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPEG, PNG, atau JPG.',
            'foto.max'   => 'Ukuran foto maksimal 2MB.',

            // URL & kontak
            'link_pekerjaan.url' => 'Link pekerjaan harus berupa URL yang valid.',
            'whatsapp.regex'     => 'Nomor WhatsApp harus diawali kode negara +62 (bukan 0). Contoh: +6281234567890.',
            'instagram.regex'    => 'Username Instagram harus diawali @, tanpa spasi (contoh: @bpsbantul).',
            'tiktok.regex'       => 'Username TikTok harus diawali @, tanpa spasi (contoh: @bpsbantul).',
        ];
    }

    /**
     * Custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama'            => 'Nama',
            'foto'            => 'Foto',
            'asal_kampus'     => 'Asal kampus',
            'prodi'           => 'Prodi',
            'tanggal_mulai'   => 'Tanggal mulai',
            'tanggal_selesai' => 'Tanggal selesai',
            'link_pekerjaan'  => 'Link pekerjaan',
            'whatsapp'        => 'WhatsApp',
            'instagram'       => 'Instagram',
            'tiktok'          => 'TikTok',
            'kesan'           => 'Kesan',
            'pesan'           => 'Pesan',
        ];
    }
}
