<div class="row">
	<div class="col-xl-6">
		@php
		$subtotal = 0;
		$tax = 0;
		@endphp
		@if (Session::has('pos.cart'))
		<ul class="list-group list-group-flush">
			@forelse (Session::get('pos.cart') as $key => $cartItem)
			@php
			$subtotal += $cartItem['price']*$cartItem['quantity'];
			$tax += $cartItem['tax']*$cartItem['quantity'];
			$stock = \App\Models\ProductStock::find($cartItem['stock_id']);
			@endphp
			<li class="list-group-item px-0">
				<div class="row gutters-10 align-items-center">
					<div class="col">
						<div class="d-flex">
							@if($stock->image == null)
							<img src="{{ uploaded_asset($stock->product->thumbnail_img) }}" class="img-fit size-60px">
							@else
							<img src="{{ uploaded_asset($stock->image) }}" class="img-fit size-60px">
							@endif
							<span class="flex-grow-1 ml-3 mr-0">
								<div class="text-truncate-2">{{ $stock->product->name }}</div>
								<span class="span badge badge-inline fs-12 badge-soft-secondary">{{ $cartItem['variant']
									}}</span>
							</span>
						</div>
					</div>
					<div class="col-xl-3">
						<div class="fs-14 fw-600 text-right">{{ single_price($cartItem['price']) }}</div>
						<div class="fs-14 text-right">{{ translate('QTY') }}: {{ $cartItem['quantity'] }}</div>
					</div>
				</div>
			</li>
			@empty
			<li class="list-group-item">
				<div class="text-center">
					<i class="las la-frown la-3x opacity-50"></i>
					<p>{{ translate('No Product Added') }}</p>
				</div>
			</li>
			@endforelse
		</ul>
		@else
		<div class="text-center">
			<i class="las la-frown la-3x opacity-50"></i>
			<p>{{ translate('No Product Added') }}</p>
		</div>
		@endif
	</div>
	<div class="col-xl-6">
		<div class="pl-xl-4">
			<div class="card mb-4">
				<div class="card-header"><span class="fs-16">{{ translate('Customer Info') }}</span></div>
				<div class="card-body">
					@if(Session::has('pos.shipping_info') && Session::get('pos.shipping_info')['name'] != null)
					<div class="d-flex justify-content-between  mb-2">
						<span class="">{{translate('Name')}}:</span>
						<span class="fw-600">{{ Session::get('pos.shipping_info')['name'] }}</span>
					</div>

					{{-- <div class="d-flex justify-content-between  mb-2">
						<span class="">{{translate('Email')}}:</span>
						<span class="fw-600">{{ Session::get('pos.shipping_info')['email'] }}</span>
					</div>
					<div class="d-flex justify-content-between  mb-2">
						<span class="">{{translate('Phone')}}:</span>
						<span class="fw-600">{{ Session::get('pos.shipping_info')['phone'] }}</span>
					</div> --}}
					<div class="d-flex justify-content-between mb-2">
						<span class="">{{ translate('Address') }}:</span>
						<span class="fw-600">{!! nl2br(Session::get('pos.shipping_info')['address']) !!}</span>
					</div>

					<div class="d-flex justify-content-between  mb-2">
						<span class="">{{translate('Country')}}:</span>
						<span class="fw-600">{{ Session::get('pos.shipping_info')['country'] }}</span>
					</div>
					<div class="d-flex justify-content-between  mb-2">
						<span class="">{{translate('City')}}:</span>
						<span class="fw-600">{{ Session::get('pos.shipping_info')['city'] }}</span>
					</div>
					<div class="d-flex justify-content-between  mb-2">
						<span class="">{{translate('Postal Code')}}:</span>
						<span class="fw-600">{{ Session::get('pos.shipping_info')['postal_code'] }}</span>
					</div>
					@else
					<div class="text-center p-4">
						{{ translate('No customer information selected.') }}
					</div>
					@endif
				</div>
			</div>

			<div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
				<span>{{translate('Net Amount')}}</span>
				<span>{{ single_price($subtotal) }}</span>
			</div>
			<div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
				<span>{{translate('Discount')}}</span>
				<span>{{ single_price(Session::get('pos.discount', 0)) }}</span>
			</div>
			<div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
				<span>{{translate('Delivery Charge')}}</span>
				<span>{{ single_price(Session::get('pos.shipping', 0)) }}</span>
			</div>

			@php
			$total = $subtotal + Session::get('pos.shipping', 0) - Session::get('pos.discount', 0);
			$tax = $total * 20/100;
			@endphp
			<div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
				<span>{{translate(' VAT')}}</span>
				<span>{{ single_price(Session::get('pos.tax', 0)) }}</span>
			</div>
			<div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
				<span>{{translate('Total Amount')}}</span>
				<span>{{ single_price($subtotal+Session::get('pos.tax', 0)+Session::get('pos.shipping', 0) -
					Session::get('pos.discount', 0)) }}</span>
			</div>
			<div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
				<div>
					<h6>Purchase Number</h6>
					<input type="text" class="form-control" placeholder="Purchase Order Number" name="purchase_number"
						required>
				</div>
				<div>
					<h6>Order Id</h6>
					<input type="text" class="form-control" placeholder="Order ID" name="order_id">
				</div>


			</div>
			<div>

				<label style="margin-top:20px;">
					<input type="checkbox" id="myCheckbox" value="" onchange="toggleValue()"
						name="pharmaceutical_checkbox"> Pharmaceutical
				</label>
				<script>
					function toggleValue() {
            const checkbox = document.getElementById("myCheckbox");
            // console.log(checkbox);
            if (checkbox.checked) {
                checkbox.value = "1";
            } else {
                checkbox.value = "0";
            }
        }
				</script>
			</div>
		</div>
	</div>
</div>