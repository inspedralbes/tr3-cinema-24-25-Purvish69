<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tickets;
    public $sessionInfo;

    public function __construct(User $user, $tickets, $sessionInfo)
    {
        $this->user = $user;
        $this->tickets = $tickets;
        $this->sessionInfo = $sessionInfo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FilmGalaxy - Tus entradas de cine están listas',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets',
            with: [
                'tickets' => $this->tickets,
                'sessionInfo' => $this->sessionInfo,
                'user' => $this->user
            ]
        );
    }

    public function attachments(): array
    {
        $pdf = PDF::loadView('pdfs.tickets', [
            'user' => $this->user,
            'tickets' => $this->tickets,
            'sessionInfo' => $this->sessionInfo
        ]);

        return [
            Attachment::fromData(fn() => $pdf->output(), 'entradas.pdf')
                ->withMime('application/pdf')
        ];
    }
}