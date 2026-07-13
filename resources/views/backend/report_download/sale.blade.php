<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .currency {
            font-weight: bold;
        }
    </style>
</head>
<body>
	<body>
	<div>
		@php
			$logo = get_setting('header_logo');
		@endphp
		<div style="background: #eceff4;padding: 1rem;">
            <table>
                <tr>
                    <td>
                        @if($logo != null)
                            <img src="{{ uploaded_asset($logo) }}" height="30" style="display:inline-block;">
                        @else
                            <img src="{{ static_asset('assets/img/logo.png') }}" height="30" style="display:inline-block;">
                        @endif
                    </td>
                    <td>Stock Out Report</td>
                   <td>
                       Date: {{$date}}
                   </td>
                </tr>
            </table>

        </div>
    <div>
         <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('Invoice Number') }}</th>
                            <th>{{ translate('PO No') }}</th>
                            <th>{{ translate('Order Date') }}</th>
                            <th>{{ translate('Payment Due Date') }}</th>
                            <th>{{ translate('QTY') }}</th>
                            <th>{{ translate('Net Amount') }}</th>
                            <th>{{ translate('VAT') }}</th>
                            <th>{{ translate('Discount') }}</th>
                            <th>{{ translate('Carrige Amount') }}</th>
                            <th>{{ translate('Total Amount') }}</th>
                        </tr>
                    </thead>
                    <!--<tbody>-->
                    <!--     @php-->

                    <!--     $sum_debit = 0;-->
                    <!--     $price = 0;-->
                    <!--     $tax = 0;-->
                    <!--     $coupon_discount = 0;-->
                    <!--     $shipping = 0;-->
                    <!--    @endphp-->
                    <!--    @foreach ($orders as $order)-->
                    <!--        @php-->
                    <!--            $sum_debit += $order->account->sum('debit');-->
                    <!--             $price += $order->orderDetails->sum('price');-->
                    <!--             $tax += $order->orderDetails->sum('tax');-->
                    <!--             $coupon_discount += $order->orderDetails->sum('coupon_discount');-->
                    <!--             $shipping += $order->orderDetails->sum('shipping_cost');-->
                    <!--        @endphp-->
                    <!--        <tr>-->
                    <!--             <td>-->
                    <!--                                                      {{ $order->customer_details->company_name }}-->
                               
                                
                    <!--            </td>-->
                    <!--            <td>{{ $order->invoice_number }}</td>-->
                    <!--            <td>{{ $order->post_code }}</td> <!-- Assuming PO No is stored as post_code -->-->
                    <!--            <td>{{ $order->created_at->format('d-m-Y') }}</td>-->
                    <!--            <td>{{ $order->account->first()->due_date  ?? ''}}</td>-->
                    <!--            <td>{{ $order->orderDetails->sum('quantity') }}</td>-->
                    <!--            <td>{{ number_format($order->orderDetails->sum('price'),2) }}</td>-->
                    <!--            <td>{{number_format( $order->orderDetails->sum('tax'),2) }}</td>-->
                    <!--            <td>{{ $order->coupon_discount }}</td>-->
                    <!--            <td>{{ $order->orderDetails->sum('shipping_cost') }}</td>-->
                    <!--            <td>{{ number_format(($order->orderDetails->sum('price')+$order->orderDetails->sum('tax')+$order->orderDetails->sum('shipping_cost'))-$order->coupon_discount,2) }}</td>-->
                                
                    <!--        </tr>-->
                            
                    <!--    @endforeach-->
                    <!--</tbody>-->
                     <tbody>
                         @php

                         $sum_debit = 0;
                         $price = 0;
                         $tax = 0;
                         $coupon_discount = 0;
                         $shipping = 0;
                        @endphp
                        @foreach ($orders as $order)
                            @php
                                $sum_debit += $order->account->sum('debit');
                                 $price += $order->orderDetails->sum('price');
                                 $tax += $order->orderDetails->sum('tax');
                                 $coupon_discount += $order->orderDetails->sum('coupon_discount');
                                 $shipping += $order->orderDetails->sum('shipping_cost');
                            @endphp
                            <tr>
                                <td>
                                                                          {{ $order->customer_details->company_name }}
                               
                                
                                </td>
                                <td>{{ $order->invoice_number }}</td>
                                <td>{{ $order->purchase_order_number }}</td> <!-- Assuming PO No is stored as post_code -->
                                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                <td>{{ $order->account->first()->due_date ?? ''}}</td>
                                <td>{{ $order->orderDetails->sum('quantity') }}</td>
                              <td>{{ number_format($order->orderDetails->sum('price'), 2) }}</td>
      @php
    $price = $order->orderDetails->sum('price');
    $taxFromPrice = $price * 0.20; // 20%
@endphp

<td>{{ number_format($taxFromPrice, 2) }}</td>
                                <td>{{ $order->coupon_discount }}</td>
                                <td>{{ $order->orderDetails->sum('shipping_cost') }}</td>
                                <!--<td>{{ single_price($order->grand_total ?? 0) }}</td>-->
                               <td>{{ number_format(($order->orderDetails->sum('price')+$taxFromPrice+$order->orderDetails->sum('shipping_cost'))-$order->coupon_discount,2) }}</td>

                                
                            </tr>
                            
                        @endforeach
                    </tbody>
                 

                    <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td> <h4>£{{number_format( $totalNetAmount,2)}}</h4></td>
                                                             @php
    $price = $totalNetAmount;
    $taxFromPrice = $totalNetAmount * 0.20; // 20%
@endphp
                            <td> <h4>£{{ number_format($taxFromPrice ,2)}}</h4></td>
                            <td> <h4>£{{ number_format($shipping,2) }}</h4></td>
                            <td> <h4>£{{ number_format($coupon_discount,2) }}</h4></td>
                            <td> <h4>£{{number_format(($taxFromPrice+$price+$shipping)-$coupon_discount,2)}}</h4></td>
                        </tr>
                    </tfoot>
                </table>

                

</body>
</html>
