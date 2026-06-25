<?php

namespace App\Mail;

use App\Models\TourBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TourBooking $booking)
    {
        // Prevent N+1 queries in email rendering
        $this->booking->load([
            'tourPackage.checkpoints',
            'user'
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('bookings@paaila.me', 'Paaila Bookings'),
            subject: "Booking #{$this->booking->booking_number} confirmed.",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking'     => $this->booking,
                'package'     => $this->booking->tourPackage,
                'user'        => $this->booking->user,
                'checkpoints' => $this->booking->tourPackage?->checkpoints ?? [],
            ],
        );
    }
}