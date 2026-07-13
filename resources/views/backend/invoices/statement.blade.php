<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statement</title>
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
                    <td style="font-size: 1.5rem;" class="text-right strong">
                      {{--   @if ($order->customer_details != null)
                            {{ $order->customer_details->contactInformation->first_name }} {{ $order->customer_details->contactInformation->last_name }}
                        @else
                           GenxMedicare
                        @endif --}}
                        {{ $order->customer_details->company_name }}
                    </td>
                </tr>
            </table>

        </div>
    <div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Invoice Number</th>
                    <th>PO Number</th>
                    <th>Due Date</th>
                    <th>Payment Date</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Paid Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                 $debit = 0;
                 $credit = 0;
                @endphp
                @foreach ($invoices as $key => $entry)
                @php
                 $debit += number_format($entry->debit, 2);
                 $credit += number_format($entry->credit, 2);
                @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{$entry->customerDetail->company_name}}</td>
                        <td>{{ $entry->order->invoice_number }}</td>
                        <td>{{ $entry->order->purchase_order_number }}</td>
                        <td>{{ date('d-m-Y',strtotime($entry->due_date)) }}</td>
                        <td>{{date('d-m-Y',strtotime($entry->created_at)) ?? 'N/A'}}</td>
                        <td class="text-right currency">{{ number_format($entry->debit, 2) }}</td>
                        <td class="text-right currency">{{ number_format($entry->credit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">Total:</td>
                    <td style="text-align: right;">{{number_format($debit,2)}}</td>
                    <td style="text-align: right;">{{number_format($credit,2)}}</td>
                </tr>
            </tfoot>
        </table>
    </div>

   
</body>
</html>
