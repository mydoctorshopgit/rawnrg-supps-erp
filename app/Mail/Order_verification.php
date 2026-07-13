<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Order_verification extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($verificationUrl, $order)
    {
        $this->verificationUrl = $verificationUrl;
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: ' Payment Order Confirmation',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.order_confirmation',
            with: [
                'url' => $this->verificationUrl,
                'order' => $this->order,
                'invoices' => [],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
