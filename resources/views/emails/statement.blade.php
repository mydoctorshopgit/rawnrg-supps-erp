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
                        @if ($order->customer_details != null)
                            {{ $order->customer_details->contactInformation->first_name }} {{ $order->customer_details->contactInformation->last_name }}
                        @else
                           GenxMedicare
                        @endif
                    </td>
                </tr>
            </table>

        </div>
    <div>
        <div>
            <h3>Hi {{ $order->customer_details->contactInformation->first_name }} {{ $order->customer_details->contactInformation->last_name }}</h3>,
            <br>
            <p>
                 Please find the attached invoice/statement for your reference. We kindly request that you settle
                this outstanding amount as soon as possible.  
            </p>
            <p> 
                If you have already made the payment, please disregard this email. If you need any assistance or would like to discuss your account, feel free to contact us at cs@genxmedicare.com.
                We value your business and appreciate your prompt attention to this matter.
            </p>
        </div>
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
                        <td>{{$entry->customerDetail->contactInformation->first_name}} {{$entry->customerDetail->contactInformation->last_name}}</td>
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

    <hr>
        <div style="margin-left:20px; padding:4px;">
            <table>
                <tr style="width: 100%;">
                    <td style="width: 65%;  font-size: medium;">
                        <div>
                            <strong>Company Reg Number: 9614874</strong><br>
                            <strong>Vat Number: 243929584 Genx Medicare Ltd.</strong>
                            <p>Terms and Conditions of Sale Apply. Title of goods to which this document refers remains with Genx Medicare Ltd until full and final payment has been received. No claims will be accepted for damages/shortages that are signed/unsigned unchecked unless they are notified in writing within 24 hours. Please refer to Genx Medicare Ltd terms & conditions. For claims, please contact Customer Services: <a href="mailto:cs@genxmedicare.com">cs@genxmedicare.com</a>.</p>
                        </div>
                    </td>
                    <td style="width: 25%; font-size: medium;">
                        <div>
                            <p><strong>Address:</strong></p>
                            <p>Unit 5 Ray Street Enterprise Centre, Ray Street<br>Huddersfield, West Yorkshire HD1 6BL</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
</body>
</html>
