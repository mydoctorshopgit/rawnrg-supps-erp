<?php

namespace App\Exports;

use App\Models\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CancelledOrderReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        if ($this->date === 'default') {
            $products = Product::whereHas('orderDetails.order', function ($query) {
                $query->where('status', 10); // Filter for cancelled orders
            })
            ->orderBy('created_at', 'desc')
            ->get();
        } else {
            $clean_date = preg_replace('/[^0-9\-to ]/', '', $this->date);

            if (!str_contains($clean_date, 'to')) {
                return collect(); // Return empty collection on invalid format
            }

            $dateRange = explode("to", $clean_date);

            if (count($dateRange) < 2 || empty(trim($dateRange[0])) || empty(trim($dateRange[1]))) {
                return collect();
            }

            try {
                $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
                $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();
            } catch (\Exception $e) {
                return collect();
            }

            $products = Product::whereHas('orderDetails', function ($query) use ($from_date, $to_date) {
                $query->whereHas('order', function ($orderQuery) {
                    $orderQuery->where('status', 10); // Filter for cancelled orders
                })->whereBetween('created_at', [$from_date, $to_date]);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        }

        // Calculate total stock
        $totalStock = $products->reduce(function ($carry, $product) {
            return $carry + $product->orderDetails->sum('quantity');
        }, 0);

        // Add total stock row
        $products->push((object)[
            'id' => 'Total Stock',
            'product_code' => '',
            'name' => '',
            'pack_qty' => '',
            'quantity' => $totalStock,
        ]);

        return $products;
    }

    public function headings(): array
    {
        return ["#", "Product Code", "Product Name", "Pack Qty", "Qty Cancelled"];
    }

    public function map($product): array
    {
        if (!is_object($product) || !isset($product->orderDetails)) {
            return [$product->id, '-', '-', '-', '-'];
        }

        if ($product->name === 'Total Stock') {
            return ['', '', 'Total Stock', '', $product->quantity];
        }

         $qty = $product->orderDetails->where('status', 10)->sum('quantity');

        return [
            $product->id,
            $product->getTranslation('product_code'),
            $product->getTranslation('name'),
            $product->pack_qty,
            '-'.$qty,
        ];
    }
}
