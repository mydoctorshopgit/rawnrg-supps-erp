<?php
namespace App\Exports;

use App\Models\Accounts;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $invoices;
    protected $totalDebit = 0;
    protected $totalCredit = 0;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        // Calculate totals before returning data
        $totalDebit = $this->invoices->sum('debit');
        $totalCredit = $this->invoices->sum('credit');

        // Add a total row at the end
        $totalRow = [
            'Total', '', '', '', '','', number_format($totalDebit, 2), number_format($totalCredit, 2)
        ];

        return $this->invoices->map(function ($invoice) {
            return [
                $invoice->id,
                $invoice->customerDetail->contactInformation->first_name . ' ' . $invoice->customerDetail->contactInformation->last_name,
                $invoice->order->invoice_number ?? 'N/A',
                $invoice->order->purchase_order_number ?? 'N/A',
                date('d-m-Y', strtotime($invoice->due_date)),
                $invoice->created_at ? date('d-m-Y', strtotime($invoice->created_at)) : 'N/A',
                number_format($invoice->debit, 2),
                number_format($invoice->credit, 2),
            ];
        })->push($totalRow); // Append total row at the end
    }

    public function headings(): array
    {
        return [
            '#',
            'Customer',
            'Invoice Number',
            'PO Number',
            'Due Date',
            'Payment Date',
            'Total Amount',
            'Paid Amount'
        ];
    }

    public function map($invoice): array
    {
        // Check if $invoice is an object (to avoid errors for the total row)
        if (is_object($invoice)) {
            return [
                $invoice->id,
                $invoice->customerDetail->contactInformation->first_name . ' ' . $invoice->customerDetail->contactInformation->last_name,
                $invoice->order->invoice_number ?? 'N/A',
                $invoice->order->purchase_order_number ?? 'N/A',
                date('d-m-Y', strtotime($invoice->due_date)),
                $invoice->created_at ? date('d-m-Y', strtotime($invoice->created_at)) : 'N/A',
                number_format($invoice->debit, 2),
                number_format($invoice->credit, 2),
            ];
        } else {
            // Return total row (already formatted correctly)
            return $invoice;
        }
    }

}
