<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Delivery Note</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			font-size: 13px;
			margin: 0;
			padding: 0;
			background: #fff;
		}

		.container-fluid {
			width: 100%;
			padding: 20px;
			border: 1px solid #ccc;
		}

		.header {
			width: 100%;
			overflow: hidden;
			margin-bottom: 20px;
		}

		.header .left,
		.header .right {
			width: 48%;
			display: inline-block;
			vertical-align: top;
		}

		.contact-info {
			font-size: 13px;
			line-height: 1.5;
		}

		.section-title {
			color: #003087;
			font-size: 20px;
			font-weight: bold;
			margin-bottom: 10px;
		}

		.no-border td {
			border: none;
			padding: 4px 8px;
			font-size: 13px;
		}

		.summary-box {
			margin-top: 15px;
		}

		.summary-table {
			width: 100%;
			border-spacing: 6px;
		}

		.summary-box td {
			background: #003087;
			color: #fff;
			text-align: center;
			padding: 6px 8px;
			font-weight: normal;
		}

		.summary-box td div:first-child {
			font-size: 9px;
		}

		.summary-box td div:last-child {
			font-size: 10px;
		}

		.info-table {
			width: 100%;
			margin-bottom: 20px;
			background-color: #e9f1ff;
			text-align: center;
			border-collapse: collapse;
		}

		.info-table td {
			padding: 10px;
			border-right: 1px solid #ccc;
			font-size: 12px;
		}

		.info-table td:last-child {
			border-right: none;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		th {
			background-color: #003087;
			color: white;
			padding: 10px;
			text-align: left;
		}

		td {
			padding: 8px;
			border: 1px solid #ddd;
		}

		.text-right {
			text-align: right;
		}

		.currency {
			font-family: monospace;
		}

		.footer {
			margin-top: 80px;
			padding: 4px;
			font-size: medium;
			width: 100%;
			background-color: #e9f1ff;
			/* border-top: 1px solid #ccc; */
		}

		.footer table {
			width: 100%;
			/* border-collapse: collapse; */
		}

		.footer td {
			padding: 5px;
		}

		.footer .company-info {
			width: 65%;
		}

		.footer .address-info {
			width: 25%;
		}
	</style>
</head>

<body>
	<div class="container-fluid">
		<!-- Header -->
		<table style="width: 100%; margin-bottom: 10px; border: none; border-collapse: collapse;">
			<tr>
				<!-- Left Column: Logo + Contact Info -->
				<td style="width: 50%; vertical-align: top; border: none;">
					<img src="https://mds.tech9et.com/public/uploads/all/4YNSioX3IvRAMGUZmCBdam6GZmpxQCmvQj5DO7me.png"
						alt="Logo" style="max-height: 50px;"><br><br>
					<div style="margin-top:25px; font-size: 12px; line-height: 1.6;">
						<img height="20" src="https://mds.tech9et.com/public/assets/img/mobile-phone.png" alt="Logo"
							style="max-height: 10px;"> 03301 133 786<br><br>
						<img height="20" src="https://mds.tech9et.com/public/assets/img/email-address.png" alt="Logo"
							style="max-height: 10px;"> ccs@mydoctorshop.com<br><br>
						<img height="20" src="https://mds.tech9et.com/public/assets/img/www.png" alt="Logo"
							style="max-height: 10px;"> www.mydoctorshop.com
					</div>
				</td>



				<!-- Right Column: Sales Receipt Info -->
				<td style="width: 50%; vertical-align: top; text-align: left; border: none;">
					<div style="font-weight: bold; font-size: 30px; margin-bottom: 5px; color:#0059A8;">Delivery Note
					</div>

					<table style="width: 100%; font-size: 12px; border: none; border-collapse: collapse;"
						cellspacing="0" cellpadding="0">
						<tr>
							<td style="text-align: left; border: none;">Invoice No:</td>
							<td style="text-align: right; border: none;">{{ $order->invoice_number ?? '' }}</td>
						</tr>
						<tr>
							<td style="text-align: left; border: none;">Order Date:</td>
							<td style="text-align: right; border: none;">{{ date('d-m-Y', $order->date) }}</td>
						</tr>
						<tr>
							<td style="text-align: left; border: none;">PO No:</td>
							<td style="text-align: right; border: none;">{{ $order->code }}</td>
						</tr>
						<tr>
							<td style="text-align: left; border: none;">Tracking No:</td>
							<td style="text-align: right; border: none;">{{ $order->tracking_code }}</td>
						</tr>
						<tr>
							<td style="text-align: left; border: none;">Shipping Term:</td>
							<td style="text-align: right; border: none;">3 to 5 Working Days</td>
						</tr>
						<tr>
							<td style="text-align: left; border: none;">Courier:</td>
							<td style="text-align: right; border: none;">{{ $order->carrier_name }}</td>
						</tr>
					</table>

					<!-- Summary Box -->
					<table style="width: 100%; margin-top: 10px; font-size: 12px; ">
						<tr>
							@if (($order->user->user_type ?? null) == "customer_credit")
							<td style="text-align: center; padding: 8px; background-color:#0059A8;color:white;">
								<div>Total Amount Due</div>
								<div>{{ single_price($order->grand_total) }}</div>
							</td>
							@else
							<td style="text-align: center; padding: 8px; background-color:#0059A8;color:white;">
								<div>Total Amount Paid</div>
								<div>{{ single_price($order->grand_total) }}</div>
							</td>
							@endif
							@if (($order->user->user_type ?? null) == "customer_credit")
							<td style="text-align: center; padding: 8px; background-color:#0059A8;color:white;">
								<div>Inv Due Date</div>
								<div>{{ date('d-m-Y', strtotime('+30 days', $order->date)) }}</div>
							</td>
							@endif
							<td style="text-align: center; padding: 8px;  background-color:#0059A8;color:white;">
								<div>Invoice Date</div>
								<div>{{ date('d-m-Y', $order->date) }}</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>




		<!-- Invoice Info -->
		<table class="info-table">
			<tr>
				<td><strong>Invoice To:</strong><br>{{ $order->user->name ?? ''}} {{ $order->user->last_name ?? ''}}
				</td>
				<td><strong>Customer Account No:</strong><br>{{ $order->user->id ?? ''}}</td>
				<td><strong>Delivery Date:</strong><br>{{ date('d-m-Y', strtotime('+1 days',
					$order->updated_at->timestamp)) }}</td>
				<td><strong>Delivery Address:</strong><br>
					@php
					$addressParts = [];
					$shipping_address = json_decode($order->shipping_address);

					foreach ($order->customer->creditDelivery as $index => $address) {
					// Line 1
					$line1 = implode(' ', array_filter([
					$address->address1,
					$address->address2,
					$address->state->name ?? '',
					]));

					// Line 2
					$line2 = implode(' ', array_filter([
					$address->town ?? '',
					$address->state1->name ?? '',

					$address->post_code ?? '',
					]));

					// Line 3
					$line3 = $address->countries->name ?? '';

					// Combine with <br>
					$fullAddress = implode('<br>', array_filter([$line1, $line2, $line3]));

					$addressParts['Address'] = $fullAddress ?: 'N/A';
					}
					@endphp

					<strong>
						@foreach($addressParts as $key => $value)
						{!! $value !!}
						@endforeach
					</strong>


				</td>
			</tr>
		</table>

		<!-- Product Table -->
		{{-- <table style="margin-bottom: 30px;">
			<thead>
				<tr>
					<th>Product Code</th>
					<th>Description</th>
					<th>Qty</th>
					<th>Pack Price</th>
					<th>Net Amount</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($order->orderDetails as $key => $orderDetail)
				@if ($orderDetail->product != null)
				<tr class="">
					<td>
						{{ $orderDetail->sku ?? '' }}


					</td>
					<td>
						{{ $orderDetail->product->description ?? '' }}


					</td>
					<td class="">
						@if(!empty($stock->variant))
						<small>size : {{ $stock->variant }}</small><br>
						@endif

						@if(!empty($stock->color))
						<small>color : {{ $stock->color }}</small><br>
						@endif

						@if(!empty($stock->flavour))
						<small>flavour : {{ $stock->flavour }}</small><br>
						@endif



					</td>
					<td class="">{{ $orderDetail->quantity }}</td>
					<td class="text-right currency">{{ single_price($orderDetail->price) }}</td>
					<td class="currency">{{ single_price($orderDetail->price/$orderDetail->quantity) }}</td>
				</tr>
				@endif
				@endforeach
			</tbody>
		</table> --}}
		<div style="padding: 1rem;">
			<table class="padding text-left small border-bottom">
				<thead>
					<tr class="gry-color" style="background: #eceff4;">
						<th width="15%" class="text-left">{{ translate('Product Code') }}</th>
						<th width="30%" class="text-left">{{ translate('Description') }}</th>
						<th width="15%" class="text-left">{{ translate('Size') }}</th>
						<th width="10%" class="text-left">{{ translate('Pack Of') }}</th>
						<th width="15%" class="text-left">{{ translate('Order QTY') }}</th>
						<th width="15%" class="text-left">{{ translate('Picked QTY') }}</th>
						<th width="20%" class="text-left">{{ translate('QTY Back-order ') }}</th>

					</tr>
				</thead>
				<tbody class="strong">
					@php
					$qtys = 0;
					$picked_qty = 0;
					$back_order = 0;
					@endphp
					@foreach ($order->orderDetails as $key => $orderDetail)
					    @php
                        $stock = App\Models\ProductStock::where('sku', $orderDetail->sku)->first();

                        @endphp
					@if ($orderDetail->product != null)
					@php
					$qtys += $orderDetail->quantity;
					$picked_qty += $orderDetail->picked_qty;
					$back_order += $orderDetail->quantity - $orderDetail->picked_qty;
					@endphp
					<tr class="">
						<td>
							{{ $orderDetail->sku }}
							{{-- @if($orderDetail->variation != null) ({{ $orderDetail->variation }}) @endif
							<br>
							<small>
								@php
								$product_stock = json_decode($orderDetail->product->stocks->first(), true);
								@endphp
								{{translate('SKU')}}: {{ $product_stock['sku'] }}
							</small> --}}
						</td>

						<td class="">{{ $orderDetail->product->name }}</td>
						<td class="">
							@if(!empty($stock->variant))
							<small>size : {{ $stock->variant }}</small><br>
							@endif

							@if(!empty($stock->color))
							<small>color : {{ $stock->color }}</small><br>
							@endif

							@if(!empty($stock->flavour))
							<small>flavour : {{ $stock->flavour }}</small><br>
							@endif



						</td>
						<td class="">{{ $orderDetail->product->stocks?->first()->qty ??"" }}</td>
						<td class="currency">{{ $orderDetail->quantity }}</td>
						<td class=" currency">{{ $orderDetail->picked_qty }}</td>
						<td class=" currency">{{ $orderDetail->quantity - $orderDetail->picked_qty }}</td>
					</tr>

					@endif
					@endforeach
				</tbody>
			</table>
		</div>
		<div style="padding: 1rem;">
			<table class="padding text-left small border-bottom">
				<thead>
					<tr class="gry-color" style="background: #eceff4;">
						<th width="33%" class="text-left">{{ translate('TOTAL ORDERED QTY') }}</th>
						<th width="33%" class="text-left">{{ translate('TOTAL DISPATCHED QTY') }}</th>
						<th width="33%" class="text-left">{{ translate('TOTAL BACK ORDER') }}</th>

					</tr>
				</thead>
				<tbody class="strong">

					<tr class="">



						<td class=" text-center currency">{{$qtys}}</td>
						<td class=" text-center currency">{{$picked_qty}}</td>
						<td class=" text-center currency">{{$back_order}}</td>

					</tr>

				</tbody>
			</table>
		</div>

		<!-- Summary and Bank Details -->


		<!-- Footer -->
		<div class="footer">
			<table>
				<tr>
					<td class="company-info" style="border: none;">
						<strong>Company Reg Number: 7326383</strong><br>
						<strong>Vat Number:GB 998 121 386 My Doctor Shop Ltd.</strong>
						<p> Terms and Conditions of Sale Apply. Title of goods to which this document refers remains
							with My Doctor Shop Ltd until full and final payment has been received.
							No claims will be accepted for damages/shortages that are unsigned/signed unchecked unless
							they are notified in writing within 24 hours. Please refer to My Doctor Shop Ltd terms &
							Conditions.<br>
							For claims, please contact Customer Services<a
								href="mailto:ccs@mydoctorshop.com">ccs@mydoctorshop.com</a>.</p>
					</td>
					<td class="address-info" style="border: none;">
						<p><strong>Address:</strong></p>
						<p>Canal Mills,Hillhouse Lane,<br>Huddersfield, HD1 1ED United Kingdom</p>
					</td>
				</tr>
			</table>
		</div>


	</div>
</body>

</html>