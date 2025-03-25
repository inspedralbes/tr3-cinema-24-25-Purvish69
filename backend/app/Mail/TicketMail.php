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

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $tickets, $sessionInfo)
    {
        $this->user = $user;
        $this->tickets = $tickets;
        $this->sessionInfo = $sessionInfo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FilmGalaxy - Tus entradas de cine están listas',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
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
