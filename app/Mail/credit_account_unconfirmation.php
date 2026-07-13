<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class credit_account_unconfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    // public $email;
    public $companyName;
    public $accountNumber;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $password, $accountNumber, $companyName)
    {
        $this->user = $user;
        $this->password = $password;
        // $this->email = $email;
        $this->companyName = $companyName;
        $this->accountNumber = $accountNumber;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Account Unconfirmation Email')
                    ->view('emails.credit_account_unconfirmation')
                    ->with([
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                        'companyName' => $this->companyName,
                        'accountNumber' => $this->accountNumber,
                        'password' => $this->password,
                        'loginUrl' => url('/login'),
                    ]);
    }
}
