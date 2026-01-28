<?php

namespace App\Mail;

use App\Models\Pendaftaran; // <-- TAMBAHKAN INI
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    // Buat properti publik untuk menyimpan data pendaftar
    public Pendaftaran $pendaftaran;

    /**
     * Create a new message instance.
     */
    public function __construct(Pendaftaran $pendaftaran) // Terima data pendaftar
    {
        $this->pendaftaran = $pendaftaran; // Set data ke properti
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Buat subjek email yang dinamis
        $statusText = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'conditional' => 'Disetujui Bersyarat'
        ];
        $subject = 'Status Pendaftaran Magang Anda: ' . ($statusText[$this->pendaftaran->status] ?? 'Diperbarui');

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Tentukan view mana yang akan digunakan sebagai template email
        return new Content(
            markdown: 'emails.pendaftaran-status', // Mengarah ke file view di bawah
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}