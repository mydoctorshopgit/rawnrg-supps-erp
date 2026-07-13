<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class register extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $randomPassword;


    /**
     * Create a new message instance.
     */
    public function __construct( $user, $randomPassword)
    {
        $this->user = $user;
        $this->randomPassword = $randomPassword;

    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Account Registration Email')
                    ->view('emails.account_registration_confirmation')
                    ->with([
                        'firstName' => $this->user->name,
                        'last_name' => $this->user->last_name,
                        'email' => $this->user->email,
                        'randomPassword' => $this->randomPassword,
                        'loginUrl' => url('/login'),
                    ]);
    }
}
