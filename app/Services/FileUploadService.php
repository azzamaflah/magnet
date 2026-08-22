<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileUploadService
{
    /**
     * Upload sebuah file ke disk dan direktori tujuan.
     */
    public function upload(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }

    /**
     * Ganti file lama dengan file baru (hapus file lama jika ada, lalu upload file baru).
     */
    public function replace(?string $oldPath, ?UploadedFile $newFile, string $directory, string $disk = 'public'): ?string
    {
        if (!$newFile) {
            return $oldPath;
        }

        $this->delete($oldPath, $disk);
        return $this->upload($newFile, $directory, $disk);
    }

    /**
     * Hapus satu file fisik dari storage disk.
     */
    public function delete(?string $path, string $disk = 'public'): bool
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Hapus beberapa file fisik sekaligus.
     */
    public function deleteMultiple(array $paths, string $disk = 'public'): void
    {
        foreach ($paths as $path) {
            $this->delete($path, $disk);
        }
    }

    /**
     * Salin file dari sumber ke destinasi baru.
     */
    public function copy(string $sourcePath, string $destinationPath, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($sourcePath)) {
            return Storage::disk($disk)->copy($sourcePath, $destinationPath);
        }

        return false;
    }

    /**
     * Cek apakah file ada di storage disk.
     */
    public function exists(?string $path, string $disk = 'public'): bool
    {
        return $path ? Storage::disk($disk)->exists($path) : false;
    }

    /**
     * Download file dengan nama yang rapi.
     */
    public function download(string $path, ?string $downloadName = null, string $disk = 'public'): StreamedResponse
    {
        if (!$this->exists($path, $disk)) {
            abort(404, 'File berkas tidak ditemukan di server.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name = $downloadName ? Str::slug($downloadName) . '.' . $extension : basename($path);

        return Storage::disk($disk)->download($path, $name);
    }
}
