<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@paaila.me', 'Paaila Info'),
            subject: "Welcome to Paaila ! You account is now active. ",
        );
    }

    public function content(): Content

    {

        return new Content(

            view: 'emails.welcome',

            with: ['user' => $this->user],

        );

    }
}