<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
class DispatchedOrder extends Mailable
{
    use Queueable, SerializesModels;
    public $array;
    public $filePath;
    public $excelFilePath;
    public $invoiceNumber;
    public function __construct($array, $filePath = '', $excelFilePath = '', $invoiceNumber = '')
    {
        $this->array = $array;
        $this->filePath = $filePath;
        $this->excelFilePath = $excelFilePath;
        $this->invoiceNumber = $invoiceNumber;
    }

 
    public function build()
    {
        $email = $this->view($this->array['view'])
            ->from($this->array['from'], env('MAIL_FROM_NAME'))
            ->subject($this->array['subject'])
            ->with([
                'order' => $this->array['order'],
                'invoices' => isset($this->array['invoices']) ? $this->array['invoices'] : []
            ])
            ->withSwiftMessage(function ($message) {
                $headers = $message->getHeaders();
                $headers->addTextHeader('X-Priority', '1');
                $headers->addTextHeader('Importance', 'High');
                $headers->addTextHeader('X-MSMail-Priority', 'High');
            });
        if (!empty($this->filePath)) {
            $email->attach($this->filePath, [
                'as' => 'order-' . $this->invoiceNumber . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }
        if (!empty($this->excelFilePath)) {
            $email->attach($this->excelFilePath, [
                'as' => 'order-statement.xlsx',
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }
        return $email;
    }
}






