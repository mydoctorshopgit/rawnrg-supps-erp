@extends('backend.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
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
                <label for="update_payment_status">Purchase Order Number</label>
                <input type="text" class="form-control" id="" disabled value="{{ $order->purchase_order_number ?? ''}}">
            </div>
            <div class="col-md-3 ml-auto">
                <label for="update_payment_status">Order Id </label>
                <input type="text" class="form-control" id="" disabled value="{{ $order->code ?? '' }}">
            </div>
            <div class="col-md-3 ml-auto">
                <label for="update_delivery_status">Notes:</label>

                <textarea class="form-control" id="notes">{{ $order->notes ?? '' }}</textarea>
            </div>
            {{-- <div class="col-md-3 ml-auto">
                <label for="update_tracking_code">
                    {{ translate('Tracking Code (optional)') }}
                </label>
                <input type="text" class="form-control" id="update_tracking_code" value="{{ $order->tracking_code }}">
            </div> --}}
            @endif
        </div>
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
       
        @if($order->user_id != null)
        @if($order->user->user_type == 'customer_credit' || $order->user->user_type == 'customer_pharmaceuti')
        <button class="btn btn-soft-success btn-sm mb-3" data-toggle="modal" data-target="#productModal">
            + Add Product
        </button>
        @endif
        @endif
        <!-- Product Search Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Search and Add Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Search Bar -->
                        <input type="text" class="form-control" id="productSearch"
                            placeholder="Search by Product Name or Code...">
                        <!-- Search Results -->
                        <div class="row">
                            <div class="col-lg-12 mt-3">
                                <table style="width:100%;" class="table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Name</td>
                                            <td>Price</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                    <tbody id="searchResults">

                                    </tbody>
                                </table>

                                <table style="width:100%;" class="mt-3 table-bordered">
                                    <thead>
                                        <tr>
                                            <td>Name</td>
                                            <td>Price</td>
                                            <td>Qty</td>
                                            <td>Sub Total</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                    <tbody id="pinResult">

                                    </tbody>
                                </table>
                            </div>
                            <input class="btn btn-primary" id="add_btn" type="button" value="Submit" name="Submit" />

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const productSearchInput = document.getElementById('productSearch');
                const searchResultsContainer = document.getElementById('searchResults');
                const pinResultContainer = document.getElementById('pinResult');
                const productModal = new bootstrap.Modal(document.getElementById('productModal'));

                // Handle product search and fetching results
                productSearchInput.addEventListener('keyup', function () {
                    const query = productSearchInput.value;
                    const customer_id = {{ $order-> customer_detail_id
                }};
            if (query.length > 2) {
                fetch(`{{ route('search.products') }}?search=${encodeURIComponent(query)}&customer_id=${encodeURIComponent(customer_id)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResultsContainer.innerHTML = data.html;
                        attachAddProductEventListeners();
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                searchResultsContainer.innerHTML = '';
            }
    });

            function attachAddProductEventListeners() {
                const addProductButtons = searchResultsContainer.querySelectorAll('.add-product-btn');
                addProductButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const productId = button.getAttribute('data-id');
                        const stockId   = button.getAttribute('data-stock-id');
                        const productName = button.getAttribute('data-name');
                        const productPrice = parseFloat(button.getAttribute('data-price')).toFixed(2);
                        const sku       = button.getAttribute('data-sku') || '';
                        const variant   = button.getAttribute('data-variant') || '';
                        const initialQty = 1;
                        const initialSubTotal = (productPrice * initialQty).toFixed(2);

                        // Create a new row for the first table (product entry table)
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                    <td>${productName}${variant ? ' <small class="text-muted">('+variant+')</small>' : ''}</td>
                    <td>${productPrice}</td>
                    <td><input type="number" value="${initialQty}" class="form-control qty-input" min="1" data-price="${productPrice}"></td>
                    <td class="sub-total">${initialSubTotal}</td>
                    <td><button class="btn btn-danger btn-sm remove-product">Remove</button></td>
                    <!-- Hidden form inputs -->
                    <input type="hidden" name="products[${productId}][id]" value="${productId}">
                    <input type="hidden" name="products[${productId}][stock_id]" class="hidden-stock-id" value="${stockId}">
                    <input type="hidden" name="products[${productId}][sku]" class="hidden-sku" value="${sku}">
                    <input type="hidden" name="products[${productId}][variant]" class="hidden-variant" value="${variant}">
                    <input type="hidden" name="products[${productId}][name]" value="${productName}">
                    <input type="hidden" name="products[${productId}][price]" value="${productPrice}">
                    <input type="hidden" name="products[${productId}][quantity]" class="hidden-qty" value="${initialQty}">
                    <input type="hidden" name="products[${productId}][subtotal]" class="hidden-subtotal" value="${initialSubTotal}">
                `;

                        // Append the new row to the pinned results table
                        pinResultContainer.appendChild(newRow);

                        // Attach event listeners to the remove button and quantity input
                        attachRemoveProductEventListener(newRow.querySelector('.remove-product'));
                        attachQuantityChangeEventListener(newRow.querySelector('.qty-input'));

                        // Close the modal after adding the product
                        productModal.hide();
                    });
                });
            }

            function attachRemoveProductEventListener(button) {
                button.addEventListener('click', function () {
                    const row = button.closest('tr');
                    row.remove();
                });
            }

            function attachQuantityChangeEventListener(input) {
                input.addEventListener('input', function () {
                    const newQuantity  = parseInt(input.value, 10) || 0;
                    const pricePerUnit = parseFloat(input.getAttribute('data-price')) || 0;
                    const newSubTotal  = (pricePerUnit * newQuantity).toFixed(2);

                    // Update the visible subtotal cell
                    input.closest('tr').querySelector('.sub-total').textContent = newSubTotal;

                    // Update all hidden inputs
                    input.closest('tr').querySelector('.hidden-qty').value      = newQuantity;
                    input.closest('tr').querySelector('.hidden-subtotal').value  = newSubTotal;
                    // Keep the hidden price in sync with the line total so the server gets the right value
                    const hiddenPrice = input.closest('tr').querySelector('input[name*="[price]"]');
                    if (hiddenPrice) hiddenPrice.value = newSubTotal;
                });
            }

            const addButton = document.getElementById('add_btn');
            addButton.addEventListener('click', function () {
                const pinnedRows = document.querySelectorAll('#pinResult tr');
                let formData = [];

                // Iterate through each row and collect product details
                pinnedRows.forEach(row => {
                    const product_id = row.querySelector('input[name*="[id]"]').value;
                    const stock_id   = row.querySelector('.hidden-stock-id')?.value || '';
                    const sku        = row.querySelector('.hidden-sku')?.value || '';
                    const variant    = row.querySelector('.hidden-variant')?.value || '';
                    const name       = row.querySelector('input[name*="[name]"]').value;
                    const price      = row.querySelector('input[name*="[price]"]').value;
                    const quantity   = row.querySelector('.hidden-qty').value;
                    const subtotal   = row.querySelector('.hidden-subtotal').value;
                    const order_id   = {{ $order->id }};

                    formData.push({ order_id, product_id, stock_id, sku, variant, name, price, quantity, subtotal });
                });

            // Send the form data to the server using AJAX
            fetch('{{ route("save.products") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure you include the CSRF token
                },
                body: JSON.stringify({ products: formData })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // alert('Products saved successfully!');
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'Product saved successfully!',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        }).then(() => {
                            window.location.reload();

                        });

                        // Clear the pinned result table
                        document.getElementById('pinResult').innerHTML = '';
                    } else {
                        alert('Error saving products. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
    });
});


            // document.addEventListener('DOMContentLoaded', function () {
            //     const productSearchInput = document.getElementById('productSearch');
            //     console.log(productSearchInput);
            //     const searchResultsContainer = document.getElementById('searchResults');
            //     const orderDetailsTable = document.getElementById('orderDetailsTable');
            //     const productModal = new bootstrap.Modal(document.getElementById('productModal'));
            //     productSearchInput.addEventListener('keyup', function () {
            //         const query = productSearchInput.value;
            //         const customer_id = {{$order->customer_detail_id }};
            //         if (query.length > 2) {
            //             fetch(`{{ route('search.products') }}?search=${encodeURIComponent(query)}&customer_id=${encodeURIComponent(customer_id)}`)
            //                 .then(response => response.json())
            //                 .then(data => {
            //                     console.log(data.html);
            //                     searchResultsContainer.innerHTML = data.html;
            //                     attachAddProductEventListeners();
            //                 })
            //                 .catch(error => console.error('Error:', error));
            //         } else {
            //             searchResultsContainer.innerHTML = '';
            //         }
            //     });



            //     function attachAddProductEventListeners() {
            //         const addProductButtons = searchResultsContainer.querySelectorAll('.add-product-btn');
            //         console.log(addProductButtons[0].dataset);
            //         addProductButtons.forEach(button => {
            //             button.addEventListener('click', function () {
            //                 const productId = button.getAttribute('data-id');
            //                 const productName = button.getAttribute('data-name');
            //                 const productPrice = parseFloat(button.getAttribute('data-price')).toFixed(2);
            //                 const newRow = document.createElement('tr');
            //                 newRow.innerHTML = `
            //                     <td>New</td>
            //                     <td>-</td>
            //                     <td><strong>${productName}</strong></td>
            //                     <td class="text-center"><input type="number" value="1" class="form-control qty-input" min="1"></td>
            //                     <td class="text-center">${productPrice}</td>
            //                     <td class="text-center">${productPrice}</td>
            //                     <td class="text-center">
            //                         <button class="btn btn-soft-danger btn-sm remove-product">Remove</button>
            //                     </td>
            //                 `;
            //                 orderDetailsTable.appendChild(newRow);
            //                 attachRemoveProductEventListener(newRow.querySelector('.remove-product'));
            //                 productModal.hide();
            //             });
            //         });
            //     }
            //     function attachRemoveProductEventListener(button) {
            //         button.addEventListener('click', function () {
            //             const row = button.closest('tr');
            //             row.remove();
            //         });
            //     }
            // });
        </script>
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table class="table-bordered aiz-table invoice-summary table">
                    <thead>
                        <tr class="bg-trans-dark">
                            <th data-breakpoints="lg" class="min-col">#</th>
                            <th width="10%">{{ translate('Photo') }}</th>
                            <th class="text-uppercase">{{ translate('Description') }}</th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-center">{{ translate('Qty') }}
                            </th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-center">{{ translate('Price')
                                }}</th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-right">{{ translate('Total') }}
                            </th>
                            <th data-breakpoints="lg" class="min-col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="orderDetailsTable">
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



                        <tr data-id="{{ $orderDetail->id }}">
                      
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if ($orderDetail->product)
                                <a href="{{ $orderDetail->product->auction_product == 0 
                                            ? route('product', $orderDetail->product->slug) 
                                            : route('auction-product', $orderDetail->product->slug) }}"
                                    target="_blank">
                                    <img height="50" src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}">
                                </a>
                                @else
                                <strong>{{ translate('N/A') }}</strong>
                                @endif
                            </td>
                            <td>
                                @if ($orderDetail->product)
                                <strong>
                                    <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank"
                                        class="text-muted">
                                        {{ $orderDetail->product->getTranslation('name') }}
                                    </a>
                                </strong><br>
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
                                @else
                                <strong>{{ translate('Product Unavailable') }}</strong>
                                @endif
                            </td>
                            <td class="text-center qty-cell">{{ $orderDetail->quantity }}</td>


                            <td class="text-center price-cell">{{ single_price($orderDetail->price /
                                $orderDetail->quantity) }}</td>
                            <td class="text-center total-cell">{{ single_price($orderDetail->price) }}</td>
                            <td class="text-center">
                                <button class="edit-button btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    onclick="openEditModal({{ $orderDetail->id }}, {{ $orderDetail->quantity }}, {{ $orderDetail->price / $orderDetail->quantity }})">
                                    <i class="las la-edit"></i>
                                </button>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{ route('orders.product.destroy', ['id' => $order->id, 'prod_id' => $orderDetail->product->id]) }}"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>

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
                        <h5 class="modal-title">{{ translate('Edit Product Details') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="editRowId">
                            <div class="form-group">
                                <label for="editQty">{{ translate('Quantity') }}</label>
                                <input type="number" id="editQty" class="form-control" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="editPrice">{{ translate('Price') }}</label>
                                <input type="number" id="editPrice" class="form-control" min="0" step="0.01" required>
                            </div>
                            <div class="form-group d-none">
                                <label for="editPrice">{{ translate('Picked Qty') }}</label>
                                <input type="number" id="picked_qty" class="form-control" value="0" min="0" step="0.01"
                                    required>
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
                            {{ single_price($order->coupon_discount) }}
                        </td>
                    </tr>
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
                            {{-- {{ single_price($order->total_tax) }} --}}
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
            </table>
            <div class="no-print text-left">
                <button type="button" class="btn  btn-primary" id="send_to_dispatch"><i class="las la-arrow"></i> Send
                    to Orders Fullfillment </button>
                <button type="button" class="btn  btn-danger" id="order_cancelled"><i class="las la-arrow"></i>
                    Cancelled Order </button>
            </div>
            {{-- <div class="no-print text-right">
                <a href="{{ route('invoice.download', $order->id) }}" type="button" class="btn btn-icon btn-light"><i
                        class="las la-print"></i></a>
            </div> --}}

        </div>


    </div>
</div>

>

@endsection
@section('modal')
<!-- Delete modal -->
@include('modals.delete_modal')
<!-- Bulk Delete modal -->
@include('modals.bulk_delete_modal')
@endsection
@section('script')
<script type="text/javascript">

    $('#order_cancelled').on('click', function () {
        var order_id = @json($order -> id); // Ensure proper JSON parsing
        var status = 10;

        // // Get input values
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
                window.location.href = "{{ route('cancelled_orders.index') }}";
            })
            .fail(function (xhr) {
                alert('Error updating status: ' + xhr.responseText);
            });
    });
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

    function updateGrandTotal() {
        let grandTotal = 0;
        $('#orderDetailsTable tr').each(function () {
            const total = parseFloat($(this).find('.total-cell').text());
            console.log(total)
            if (!isNaN(total)) grandTotal += total;
        });

        $('.grand-total').text(grandTotal.toFixed(2));
    }

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

    $('#purchase_order_number').on('change', function () {
        var order_id = {{ $order-> id
    }};
    var purchase_order_number = $('#purchase_order_number').val();
    $.post('{{ route('orders.purchase_order_number') }}', {
        _token: '{{ @csrf_token() }}',
        order_id: order_id,
        purchase_order_number: purchase_order_number
    }, function (data) {
        AIZ.plugins.notify('success', 'Purchase Number has been updated');
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
        var order_id = {{ $order-> id
    }};
    var status = 2;
    $.post('{{ route('orders.status') }}', {
        _token: '{{ @csrf_token() }}',
        order_id: order_id,
        status: status
    }, function (data) {
        AIZ.plugins.notify('success', '{{ translate('Status has been updated') }}');

        window.location.href = "{{ route('fulfillment_orders.index') }}";
    });
        });
</script>
@endsection