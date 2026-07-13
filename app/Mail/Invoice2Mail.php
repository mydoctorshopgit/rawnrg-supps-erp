<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Invoice2Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoiceData;

    public function __construct($invoiceData)
    {
        $this->invoiceData = $invoiceData;
    }

    public function build()
    {
        return $this->view('emails.invoice2')
                    ->subject('Your Invoice')
                    ->with(['invoiceData' => $this->invoiceData]);
    }
}
