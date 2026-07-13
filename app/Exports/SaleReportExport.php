<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SaleReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    protected $date;
    protected $customer_id;

    public function __construct($date, $customer_id = '')
    {
        $this->date = $date;
        $this->customer_id = $customer_id;
    }

    public function collection()
    {
        $orders = Order::where('status', 4)
        ->with([
            'orderDetails' => function ($query) {
                $query->where('status', '!=', 10);
            }
        ]);

        if ($this->customer_id) {
            $orders->where('customer_detail_id', $this->customer_id);
        }

        if ($this->date !== 'default') {
            $dates = explode(" to ", $this->date);
            if (count($dates) === 2) {
                $orders->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime(trim($dates[0]))),
                    date('Y-m-d 23:59:59', strtotime(trim($dates[1]))),
                ]);
            }
        }

        //   $orders->where('status', '!=', 10);

    $orders = $orders->get();

        $totalQuantity = $orders->sum(fn($order) => $order->orderDetails->sum('quantity'));
        $totalPrice = $orders->sum(fn($order) => $order->orderDetails->sum('price'));
        $totalTax = $totalPrice*20/100;
        $totalDiscount = $orders->sum(fn($order) => $order->orderDetails->sum('coupon_discount') ?? 0);
        $totalShipping = $orders->sum(fn($order) => $order->orderDetails->sum('shipping_cost') ?? 0);
 // $totalAmount = $orders->sum(fn($order) => $order->account->sum('debit'));

 $totalAmount = ($totalTax+$totalPrice+$totalShipping)-$totalDiscount;

        $orders->push((object)[
            'customer_name'   => 'Total',
            'invoice_number'  => '',
            'po_no'           => '',
            'order_date'      => '',
            'due_date'        => '',
           'quantity'        => (int) $totalQuantity,
    'net_amount'      => number_format($totalPrice, 2, '.', ''),
    'vat'             => number_format($totalTax, 2, '.', ''),
    'discount'        => number_format($totalDiscount, 2, '.', ''),
    'carriage_amount' => number_format($totalShipping, 2, '.', ''),
    'total_amount'    => number_format($totalAmount, 2, '.', ''),
        ]);

        return $orders;
    }

    public function headings(): array
    {
        return [
            "Customer Name",
            "Invoice Number",
            "PO No",
            "Order Date",
            "Payment Due Date",
            "QTY",
            "Net Amount",
            "VAT",
            "Discount",
            "Carriage Amount",
            "Total Amount"
        ];
    }

    public function map($order): array
    {
        $formatDecimal = fn($value) => number_format((float) $value, 2, '.', '');

        if ($order->customer_name === 'Total') {
            return [
                'Total',
                '',
                '',
                '',
                '',
                (int) $order->quantity,
                $formatDecimal($order->net_amount),
                $formatDecimal($order->vat),
                $formatDecimal($order->discount),
                $formatDecimal($order->carriage_amount),
                $formatDecimal($order->total_amount),
            ];
        }

        $price = $order->orderDetails->sum('price');
  
        $tax =  $taxFromPrice = $price * 20/100;
        $shipping = $order->orderDetails->sum('shipping_cost');
        $discount = $order->orderDetails->sum('coupon_discount');
      $totalAmount = ($price + $taxFromPrice + $shipping) - $discount;

        return [
            $order->customer_details->company_name ?? 'Guest (' . $order->guest_id . ')',
            $order->invoice_number,
            $order->purchase_order_number,
            optional($order->created_at)->format('d-m-Y'),
            optional($order->account->first())->due_date,
            (int) $order->orderDetails->sum('quantity'),
            $formatDecimal($price),
            $formatDecimal($tax),
            $formatDecimal($discount),
            $formatDecimal($shipping),
            $formatDecimal($totalAmount),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_00, // Net Amount
            'H' => NumberFormat::FORMAT_NUMBER_00, // VAT
            'I' => NumberFormat::FORMAT_NUMBER_00, // Discount
            'J' => NumberFormat::FORMAT_NUMBER_00, // Carriage Amount
            'K' => NumberFormat::FORMAT_NUMBER_00, // Total Amount
        ];
    }
}
