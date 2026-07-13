<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceEmailManager extends Mailable
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
                              'invoices' => isset($this->array['invoices']) ? $this->array['invoices'] : []
                          ]);
            // Attach PDF if filePath is not empty
            if (!empty($this->filePath)) {
                $email->attach($this->filePath, [
                    'as' => 'order-statement.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
            // Attach Excel file if excelFilePath is not empty
            if (!empty($this->excelFilePath)) {
                $email->attach($this->excelFilePath, [
                    'as' => 'order-statement.xlsx',
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            }
            return $email;
        }
}
