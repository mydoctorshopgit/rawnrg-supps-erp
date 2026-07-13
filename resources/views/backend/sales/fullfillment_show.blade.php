@extends('backend.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h1 class="h2 fs-16 mb-0">{{ translate('Fullfillment Details') }}</h1>
    </div>
    <div class="card-body">
        <div class="row gutters-5">
            <div class="col text-md-left text-center">
            </div>
            @php
            $delivery_status = $order->delivery_status;
            $payment_status = $order->payment_status;
            $admin_user_id = App\Models\User::where('user_type', 'admin')->first()->id;
            @endphp

            <!--Assign Delivery Boy-->
            @if ($order->seller_id == $admin_user_id || get_setting('product_manage_by_admin') == 1)

            @if (addon_is_activated('delivery_boy'))
            <div class="col-md-3 ml-auto">
                <label for="assign_deliver_boy">{{ translate('Assign Deliver Boy') }}</label>
                @if (($delivery_status == 'pending' || $delivery_status == 'confirmed' || $delivery_status ==
                'picked_up') && auth()->user()->can('assign_delivery_boy_for_orders'))
                <select class="form-control aiz-selectpicker" data-live-search="true"
                    data-minimum-results-for-search="Infinity" id="assign_deliver_boy">
                    <option value="">{{ translate('Select Delivery Boy') }}</option>
                    @foreach ($delivery_boys as $delivery_boy)
                    <option value="{{ $delivery_boy->id }}" @if ($order->assign_delivery_boy == $delivery_boy->id)
                        selected @endif>
                        {{ $delivery_boy->name }}
                    </option>
                    @endforeach
                </select>
                @else
                <input type="text" class="form-control" value="{{ optional($order->delivery_boy)->name }}" disabled>
                @endif
            </div>
            @endif
            <div class="col-md-3 ml-auto">
                <label for="update_delivery_status">{{ translate('Delivery Status') }}</label>
                @if (auth()->user()->can('update_order_delivery_status') && $delivery_status != 'delivered' &&
                $delivery_status != 'cancelled')
                <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                    id="update_delivery_status">
                    <option value="pending" @if ($delivery_status=='pending' ) selected @endif>
                        {{ translate('Pending') }}
                    </option>
                    <option value="confirmed" @if ($delivery_status=='confirmed' ) selected @endif>
                        {{ translate('Confirmed') }}
                    </option>
                    <option value="picked_up" @if ($delivery_status=='picked_up' ) selected @endif>
                        {{ translate('Picked Up') }}
                    </option>
                    <option value="on_the_way" @if ($delivery_status=='on_the_way' ) selected @endif>
                        {{ translate('On The Way') }}
                    </option>
                    <option value="delivered" @if ($delivery_status=='delivered' ) selected @endif>
                        {{ translate('Delivered') }}
                    </option>
                    <option value="cancelled" @if ($delivery_status=='cancelled' ) selected @endif>
                        {{ translate('Cancel') }}
                    </option>
                </select>
                @else
                <input type="text" class="form-control" value="{{ $delivery_status }}" disabled>
                @endif
            </div>
            <div class="col-md-3ml-auto ">
                <label for="update_payment_status">Order Id</label>
                <input type="text" id="order_id" class="form-control" value="{{ $order->code ?? '' }}" readonly>
            </div>
            <div class="col-md-3 ml-auto">
                <label for="update_payment_status">Purchase Order Number</label>
                <input type="text" class="form-control" value="{{ $order->purchase_order_number ?? '' }}" disabled>
            </div>

            <div class="col-md-3 ml-auto">
                <label for="update_payment_status">Add Courier</label>
                <input type="text" id="carrier_name" class="form-control" value="{{ $order->carrier_name ?? '' }}">
                <span id="carrier_name_error" style="color: red; display: none;">Please enter the courier name.</span>
            </div>

            <div class="col-md-3 ml-auto mt-4">
                <label for="update_tracking_code">
                    {{ translate('Tracking Details') }}
                </label>
                <input type="text" class="form-control" id="update_tracking_code"
                    value="{{ $order->tracking_code ?? '' }}">
                <span id="tracking_code_error" style="color: red; display: none;">Please enter the Tracking Code.</span>
            </div>

            {{-- <div class="col-md-3 ml-auto">
                <label for="">Notes (optional):</label>

                <textarea class="form-control" id="notes">{{ $order->notes ?? '' }}</textarea>
            </div> --}}
            <div class="col-md-3  mt-5 d-none " id="deliveryPrint">
                {{-- <label for="update_tracking_code">
                    {{ translate(' Print delivery Note ') }}
                </label> --}}
                <div class=" bg-primary">
                    <a href="{{ route('delivery.download', $order->id) }}" type="button" class="btn  text-light"><i
                            class="las la-print"></i> Print delivery Note</a>
                </div>
                {{-- <input type="text" class="form-control" id="update_tracking_code"
                    value="{{ $order->tracking_code }}"> --}}
            </div>
            <div class="col-md-3  mt-5 d-none" id="invoicePrint">
                {{-- <label for="update_tracking_code">
                    {{ translate(' Print delivery Note ') }}
                </label> --}}
                <div class="  bg-primary">
                    <a href="{{ route('invoice.download', $order->id) }}" type="button" class="btn  text-light"><i
                            class="las la-print"></i> Print Invoice</a>
                </div>
                {{-- <input type="text" class="form-control" id="update_tracking_code"
                    value="{{ $order->tracking_code }}"> --}}
            </div>

        </div>
        @endif


        <div class="mb-3">
            @php
            $removedXML = '
            <?xml version="1.0" encoding="UTF-8"?>';
            @endphp
            {!! str_replace($removedXML, '', QrCode::size(100)->generate($order->code)) !!}
        </div>
        <div class="row gutters-5">
            <div class="col text-md-left text-center">
                @include('backend.sales.partials.address_block')
                @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                {{-- <br>
                <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }},
                {{ translate('Amount') }}:
                {{ single_price(json_decode($order->manual_payment_data)->amount) }},
                {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                --}}
                <br>
                <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank">
                    <img src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt=""
                        height="100">
                </a>
                @endif
            </div>
            <div class="col-md-5 ml-auto">
                <table>
                    <tbody>
                        <tr>
                            <td class="text-main text-bold">Invoice #</td>
                            <td class="text-info text-bold text-right"> {{ $order->invoice_number }}</td>
                            <td class="text-main text-bold">&nbsp;&nbsp;</td>
                            <td class="text-main text-bold">{{ translate('Order Date') }} </td>
                            <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">PO No</td>
                            <td class="text-info text-bold text-right"> {{ $order->code }}</td>
                            <td class="text-main text-bold">&nbsp;&nbsp;</td>
                            <td class="text-main text-bold">Tracking No: </td>
                            <td class="text-right"> {{$order->tracking_code}}</td>
                        </tr>

                        <tr>
                            <td class="text-main text-bold">{{ translate('Additional Info') }}</td>
                            <td class="text-right">{{ $order->notes }}</td>
                            <td class="text-main text-bold"></td>
                            <td class="text-main text-bold"></td>
                            <td class="text-main text-bold"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="new-section-sm bord-no">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table class="table-bordered aiz-table invoice-summary table">
                    <thead>
                        <tr class="bg-trans-dark">
                            <th data-breakpoints="lg" class="min-col">#</th>
                            <th width="10%">{{ translate('Photo') }}</th>
                            <th class="text-uppercase">{{ translate('Description') }}</th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                {{ translate('Qty') }}
                            </th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-center">{{ translate('Picked
                                Qty') }}</th>

                            <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                {{ translate('Price') }}</th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-right">
                                {{ translate('Total') }}</th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-right">
                                {{ translate('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $key => $orderDetail)
                       
                        @php
                            $stock = App\Models\ProductStock::where(function($q) use ($orderDetail) {
                             
                                if (!empty($orderDetail->sku)) {
                                    $q->whereNotNull('sku')
                                    ->where('sku', $orderDetail->sku);
                                }else {
                                    $q->whereNotNull('variant')
                                    ->where('variant', $orderDetail->variation);
                                   
                                }
                            
                               
                            })->first();

                            $is_product_stock = 'Not';
                            if (empty($stock)) {
                                $is_product_stock = 'Yes';

                                $stock = $orderDetail->product?->stocks?->first();
                            }
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">
                                    <img height="50" src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}">
                                </a>
                                @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                <a href="{{ route('auction-product', $orderDetail->product->slug) }}" target="_blank">
                                    <img height="50" src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}">
                                </a>
                                @else
                                <strong>{{ translate('N/A') }}</strong>
                                @endif
                            </td>
                            <td>
                                @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                <strong>
                                    <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank"
                                        class="text-muted">
                                        {{ $orderDetail->product->getTranslation('name') }}
                                    </a>
                                </strong>

                                <br>
                                @if(!empty($stock->variant))
                                <small>SIZE : {{ $stock->variant }}</small><br>
                                @endif

                                @if(!empty($stock->color))
                                <small>COLOR : {{ $stock->color }}</small><br>
                                @endif

                                @if(!empty($stock->flavour))
                                <small>FLAVOUR : {{ $stock->flavour }}</small><br>
                                @endif

                                
                                @if (!empty($orderDetail->sku) || !empty($stock->sku))
                                  <small>{{ translate('SKU') }}: {{ $orderDetail->sku ?? $stock->sku }}</small><br>
                                 
                                @endif
                                @if (!empty($stock->pack_qty))
                                  <small>Pack qty: {{ $stock->pack_qty }}</small>
                                    
                                @endif

                                @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                <strong>
                                    <a href="{{ route('auction-product', $orderDetail->product->slug) }}"
                                        target="_blank" class="text-muted">
                                        {{ $orderDetail->product->getTranslation('name') }}
                                    </a>
                                </strong>
                                @else
                                <strong>{{ translate('Product Unavailable') }}</strong>
                                @endif
                            </td>

                            <td class="text-center">
                                {{ $orderDetail->quantity }}
                            </td>
                            <td class="text-center qty-cell">
                                {!! $orderDetail->picked_qty == 0 ? 0 : $orderDetail->picked_qty !!}
                            </td>

                            <td class="text-center">
                                {{ single_price($orderDetail->price / $orderDetail->quantity) }}
                            </td>
                            <td class="text-center">
                                {{ single_price($orderDetail->price) }}
                            </td>
                            <td class="text-center">
                                <button class="edit-button btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    onclick="openEditModal({{ $orderDetail->id }}, {{ $orderDetail->quantity }}, {{ $orderDetail->price / $orderDetail->quantity }})">
                                    <i class="las la-edit"></i>
                                </button>
                                {{-- <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{ route('orders.product.destroy', ['id' => $order->id, 'prod_id' => $orderDetail->product->id]) }}"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                                --}}
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Edit Modal -->
        <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('Edit Picked QTY') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="editRowId">
                            <div class="form-group">
                                <input type="number" id="editQty" class="form-control d-none" min="1" required>
                            </div>
                            <div class="form-group">
                                <input type="number" id="editPrice" class="form-control d-none" min="0" step="0.01"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="editPrice">{{ translate('Picked Qty') }}</label>
                                <input type="number" id="picked_qty" class="form-control" min="0" step="0.01" required>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="updateProduct()">Save
                                Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix float-right">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Net Amount') }} :</strong>
                        </td>
                        <td class="total-cell">
                            {{ single_price($order->orderDetails->sum('price')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Discount') }} :</strong>
                        </td>
                        <td class="discount-cell">
                            {{ single_price(Session::get('pos.discount', 0)) }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td>
                            <strong class="text-muted">{{ translate(' VAT Tax') }} :</strong>
                        </td>
                        <td class="tax-cell">
                            {{ single_price($order->orderDetails->sum('tax')) }}
                        </td>
                    </tr> --}}
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Delivery Charge') }} :</strong>
                        </td>
                                                @if($order->shipping_cost)
                        <td class="shipping-cell">
                            {{ single_price($order->shipping_cost) }}
                        </td>
                        @else
                        <td class="shipping-cell testing">
                            {{ single_price($order->orderDetails->sum('shipping_cost')) }}
                        </td>
                        @endif
                    </tr>

                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate(' VAT') }} :</strong>
                        </td>
                        <td class="tax-cell">
                            {{ single_price($order->total_tax) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">Total Amount :</strong>
                        </td>
                        <td class="text-muted h5 grand-total">
                            {{ single_price($order->grand_total) }}
                        </td>
                    </tr>
                </tbody>
                {{-- <tbody>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Sub Total') }} :</strong>
                        </td>
                        <td class="total-cell">
                            {{ single_price($order->orderDetails->sum('price')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Tax') }} :</strong>
                        </td>
                        <td class="tax-cell">
                            {{ single_price($order->orderDetails->sum('tax')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('Shipping') }} :</strong>
                        </td>
                        <td class="shipping-cell">
                            {{ single_price($order->orderDetails->sum('shipping_cost')) }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong class="text-muted">{{ translate('TOTAL') }} :</strong>
                        </td>
                        <td class="text-muted h5 grand-total">
                            {{ single_price($order->grand_total) }}
                        </td>
                    </tr>
                </tbody> --}}
            </table>
            <div class="no-print text-left">
                @if(isset($_GET["q"]))

                @else
                <button type="button" class="btn  btn-primary" id="send_to_dispatch"><i class="las la-arrow"></i> Send
                    to Shipment </button>
                <button type="button" class="btn  btn-danger" id="send_to_pending"><i class="las la-arrow"></i> Send to
                    Pending </button>
                @endif
            </div>
            {{-- <div class="no-print text-right">
                <a href="{{ route('invoice.download', $order->id) }}" type="button" class="btn btn-icon btn-light"><i
                        class="las la-print"></i></a>
            </div> --}}

        </div>

    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    function openEditModal(id, qty, price) {
        $('#editRowId').val(id);
        $('#editQty').val(qty);
        $('#editPrice').val(price);
        $('#editModal').modal('show');
    }

    function updateProduct() {
        const id = $('#editRowId').val();
        const picked_qty = $('#picked_qty').val();
        const qty = parseFloat($('#editQty').val());
        const price = parseFloat($('#editPrice').val());
        const total = qty * price;
        var order_id = {{ $order-> id
    }};

    $.ajax({
        url: '{{route("updateQty")}}',
        method: 'POST',
        data: {
            id,
            order_id: order_id,
            quantity: qty,
            price: price,
            picked_qty: picked_qty,
            _token: '{{ csrf_token() }}',
        },
        success: (response) => {
            if (response.success) {
                const row = $(`tr[data-id="${id}"]`);
                row.find('.qty-cell').text(qty);
                row.find('.price-cell').text(price.toFixed(2));
                row.find('.total-cell').text(total.toFixed(2));

                $('#editModal').modal('hide');
                AIZ.plugins.notify('success', '{{ translate('Data has been updated') }}');
                window.location.reload();
            } else {
                alert(response.message);
            }
        },
        error: (xhr) => {
            alert('An error occurred. Please try again.');
        },
    });
        }


    $('#update_delivery_status').on('change', function () {
        var order_id = {{ $order-> id
    }};
    var status = $('#update_delivery_status').val();
    $.post('{{ route('orders.update_delivery_status') }}', {
        _token: '{{ @csrf_token() }}',
        order_id: order_id,
        status: status
    }, function (data) {
        AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
    });
        });
    $('#update_payment_status').on('change', function () {
        var order_id = {{ $order-> id
    }};
    var status = $('#update_payment_status').val();
    $.post('{{ route('orders.update_payment_status') }}', {
        _token: '{{ @csrf_token() }}',
        order_id: order_id,
        status: status
    }, function (data) {
        AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
    });
        });
    $('#update_tracking_code').on('change', function () {
        var order_id = {{ $order-> id
    }};
    var tracking_code = $('#update_tracking_code').val();
    $.post('{{ route('orders.update_tracking_code') }}', {
        _token: '{{ @csrf_token() }}',
        order_id: order_id,
        tracking_code: tracking_code
    }, function (data) {
        AIZ.plugins.notify('success', '{{ translate('Order tracking code has been updated') }}');
    });
        });

    $('#carrier_name').on('change', function () {
        var order_id = {{ $order-> id
    }};
    var carrier_name = $('#carrier_name').val();
    $.post('{{ route('orders.purchase_order_number') }}', {
        _token: '{{ @csrf_token() }}',
        order_id: order_id,
        carrier_name: carrier_name
    }, function (data) {
        AIZ.plugins.notify('success', 'Carrier has been updated');
    });
        });
    $('#notes').on('change', function () {
        var order_id = {{ $order-> id
    }};
    var notes = $('#notes').val();
    $.post('{{ route('orders.notes') }}', {
        _token: '{{ @csrf_token() }}',
        order_id: order_id,
        notes: notes
    }, function (data) {
        AIZ.plugins.notify('success', '{{ translate('Notes has been updated') }}');
    });
        });
    $('#send_to_dispatch').on('click', function () {
        var order_id = @json($order -> id); // Ensure proper JSON parsing
        var status = 3;

        // Get input values
        var carrierName = $('#carrier_name').val().trim();
        var trackingCode = $('#update_tracking_code').val().trim();

        // Get error span elements
        var carrierError = $('#carrier_name_error');
        var trackingError = $('#tracking_code_error');

        // Validation flag
        var isValid = true;

        // Validate Carrier Name
        if (carrierName === '') {
            carrierError.text('Please enter the courier name.').show();
            isValid = false;
        } else {
            carrierError.hide();
        }

        // Validate Tracking Code
        if (trackingCode === '') {
            trackingError.text('Please enter the tracking code.').show();
            isValid = false;
        } else {
            trackingError.hide();
        }

        // If validation fails, stop execution
        if (!isValid) {
            return;
        }

        // Proceed with AJAX request if validation passes
        $.post('{{ route('orders.status') }}', {
            _token: '{{ csrf_token() }}',
            order_id: order_id,
            status: status
        })
            .done(function (data) {
                if (typeof AIZ !== 'undefined' && typeof AIZ.plugins !== 'undefined' && typeof AIZ.plugins.notify === 'function') {
                    AIZ.plugins.notify('success', '{{ translate('Status has been updated') }}');
                } else {
                    alert("Status has been updated successfully!");
                }

                // Redirect after update
                window.location.href = "{{ route('shipment_order.index') }}";
            })
            .fail(function (xhr) {
                alert('Error updating status: ' + xhr.responseText);
            });
    });
    $('#send_to_pending').on('click', function () {
        var order_id = @json($order -> id); // Ensure proper JSON parsing
        var status = 0;

        // Get input values
        // var carrierName = $('#carrier_name').val().trim();
        // var trackingCode = $('#update_tracking_code').val().trim();

        // // Get error span elements
        // var carrierError = $('#carrier_name_error');
        // var trackingError = $('#tracking_code_error');

        // // Validation flag
        // var isValid = true;

        // // Validate Carrier Name
        // if (carrierName === '') {
        //     carrierError.text('Please enter the courier name.').show();
        //     isValid = false;
        // } else {
        //     carrierError.hide();
        // }

        // // Validate Tracking Code
        // if (trackingCode === '') {
        //     trackingError.text('Please enter the tracking code.').show();
        //     isValid = false;
        // } else {
        //     trackingError.hide();
        // }

        // // If validation fails, stop execution
        // if (!isValid) {
        //     return;
        // }

        // Proceed with AJAX request if validation passes
        $.post('{{ route('orders.status') }}', {
            _token: '{{ csrf_token() }}',
            order_id: order_id,
            status: status
        })
            .done(function (data) {
                if (typeof AIZ !== 'undefined' && typeof AIZ.plugins !== 'undefined' && typeof AIZ.plugins.notify === 'function') {
                    AIZ.plugins.notify('success', '{{ translate('Status has been updated') }}');
                } else {
                    alert("Status has been updated successfully!");
                }

                // Redirect after update
                window.location.href = "{{ route('pending_orders.index') }}";
            })
            .fail(function (xhr) {
                alert('Error updating status: ' + xhr.responseText);
            });
    });
    //  $('#send_to_dispatch').on('click', function() {
    //     var order_id = {{ $order->id }};
    //     var status = 3;
    //     $.post('{{ route('orders.status') }}', {
    //         _token: '{{ @csrf_token() }}',
    //         order_id: order_id,
    //         status: status
    //     }, function(data) {
    //         AIZ.plugins.notify('success', '{{ translate('Status has been updated') }}');

    //         window.location.href = "{{ route('shipment_order.index')}}"
    //     });
    // });

    document.addEventListener('DOMContentLoaded', function () {
        const carrierNameInput = document.getElementById('carrier_name');
        const trackingCodeInput = document.getElementById('update_tracking_code');
        const deliveryPrint = document.getElementById('deliveryPrint');
        const invoicePrint = document.getElementById('invoicePrint');

        function togglePrintButtons() {
            // Check if both fields are filled
            if (carrierNameInput.value.trim() !== '' && trackingCodeInput.value.trim() !== '') {
                deliveryPrint.classList.remove('d-none'); // Show "deliveryPrint" button
                invoicePrint.classList.remove('d-none'); // Show "invoicePrint" button
            } else {
                deliveryPrint.classList.add('d-none'); // Hide "deliveryPrint" button
                invoicePrint.classList.add('d-none'); // Hide "invoicePrint" button
            }
        }

        // Attach the input event listener to both fields
        carrierNameInput.addEventListener('input', togglePrintButtons);
        trackingCodeInput.addEventListener('input', togglePrintButtons);

        // Initial check in case the fields are pre-filled
        togglePrintButtons();
    });


    //     document.addEventListener('DOMContentLoaded', function () {
    //     const carrierNameInput = document.getElementById('carrier_name');
    //     const trackingCodeInput = document.getElementById('update_tracking_code');
    //     const trackingSection = document.getElementById('tracking-section');

    //     // Event listener to show/hide the section
    //     carrierNameInput.addEventListener('input', function () {
    //         trackingCodeInput.addEventListener('input', function () {
    //         if (carrierNameInput.value.trim() !== '' && trackingCodeInput.value.trim() !== '') {
    //             deliveryPrint.classList.remove('d-none'); // Show section
    //             invoicePrint.classList.remove('d-none'); // Show section
    //         } else {
    //             deliveryPrint.classList.add('d-none'); // Show section
    //             invoicePrint.classList.add('d-none');
    //         }
    //     });
    //     });
    // });
</script>
@endsection