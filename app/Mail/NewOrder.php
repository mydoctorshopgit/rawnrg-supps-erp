<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrder extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;
    public $filePath;
    public $excelFilePath;

    public function __construct($array,$filePath='',$excelFilePath='')
    {
        $this->array = $array;
        $this->filePath = $filePath;
        $this->excelFilePath = $excelFilePath;

    }
    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
        {
            $email = $this->view($this->array['view'])
                          ->from($this->array['from'], env('MAIL_FROM_NAME'))
                          ->subject($this->array['subject'])
                          ->with([
                              'order' => $this->array['order'],
                            //   'invoices' => isset($this->array['invoices']) ? $this->array['invoices'] : []
                          ]);
        
            return $email;
        }
}
