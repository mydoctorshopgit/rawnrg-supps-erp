<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Cancelled Report</title>
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
                    <td>Cancelled Report</td>
                   <td>
                       Date: {{$date}}
                   </td>
                </tr>
            </table>

        </div>
    <div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Pack Qty</th>
                    <th>Qty Cancelled</th>
                </tr>
            </thead>
            <tbody>
                        @php
                        $count = 0;
                        $counter= 1;
                        @endphp
                       @foreach ($products as $key => $product)
                                    @php
                                        $qty = 0;
                                        foreach ($product->orderDetails->where('status',10) as $key => $stock) {
                                            $qty += $stock->quantity;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{$counter}}</td>
                                        <!--<td>{{ $product->id }}</td>-->
                                        <td>{{ $product->product_code }}</td>
                                        <!--<td>{{ $product->getTranslation('invoice_number') }}</td>-->
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->pack_qty,2 }}</td>
                                        <td>{{ '-'.$qty }}</td>
                                    </tr>
                                    @php
                                    $count += $qty;
                                    $counter++;
                                    @endphp
                                @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total Stock</td>
                            <td>{{'-'.$count}}</td>
                        </tr>
                    </tfoot>
                    </table>
    </div>

</body>
</html>
