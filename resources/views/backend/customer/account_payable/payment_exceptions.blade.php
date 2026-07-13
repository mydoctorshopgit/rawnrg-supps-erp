@extends('backend.layouts.app')

@section('content')

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f9f9f9;
    }

    .tabs {
        display: flex;
        width: 30%;
        border-bottom: 1px solid #ccc;
    }

    .tabs button {
        flex: 1;
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background-color: #f1f1f1;
        color: #333;
        font-weight: bold;
        text-align: center;
        outline: none;
        transition: background-color 0.3s;
    }

    .tabs button:hover {
        background-color: #ddd;
    }

    .tabs button.active {
        background-color: #007bff;
        color: #fff;
        border-bottom: 2px solid white;
    }

    .tab-content {
        padding: 20px;
        background-color: white;
        border-top: none;
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Optional: Add responsive styling */
    @media (max-width: 600px) {
        .tabs button {
            padding: 10px;
            font-size: 14px;
        }

        .tab-content {
            padding: 10px;
        }
    }
</style>


<div>
    <div class="tabs">
        <button class="tab-btn active" data-tab="tab1">Invoice</button>
        <button class="tab-btn" data-tab="tab2">Remittance</button>
    </div>

    <div class="tab-content active" id="tab1">
       
   <div class="d-flex justify-content-between align-items-center mb-4">

    <!-- LEFT: Heading -->
    <h5 class="mb-0">Exception Invoice</h5>

    <!-- RIGHT: Search -->
    <form method="GET"
          action="{{ route('payment_exceptions') }}"
          id="searchForm"
          class="d-flex align-items-center"
          style="max-width: 420px; width: 100%;">

        <div class="input-group flex-nowrap">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Search by customer, invoice"
                   value="{{ request('search') }}" style="width:100%;">

            <button class="btn btn-primary" type="submit" style="width:20%;">
                <i class="las la-search"></i>
            </button>
        </div>
    </form>

</div>



        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <!--<th data-breakpoints="lg">#</th>-->
                    <th>
                        <div class="form-group">
                            <div class="aiz-checkbox-inline">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-all">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                    </th>

                    <th data-breakpoints="lg">Cusotmer Name</th>
                    <th data-breakpoints="lg">Invoice Number</th>
                    <th data-breakpoints="lg">Tracking Code</th>
                    <th data-breakpoints="lg">Due Date</th>
                    <th data-breakpoints="lg">Total Amount </th>
                    <th data-breakpoints="lg">Total Paid Amount</th>
                    <th data-breakpoints="lg">Total Amount Due</th>
                    <th data-breakpoints="lg">Action</th>

                </tr>
            </thead>
            <tbody id="invoiceTable">
                <!--                  @php-->
                <!--    $totalNetAmount = 0;-->
                <!--    $totalTax = 0;-->
                <!--    $totalShipping = 0;-->
                <!--    $totalDiscount = 0;-->
                <!--    $totalGrand = 0;-->
                <!--@endphp-->
                @foreach($orders as $order)
                <!--                @php-->
                <!--    $orderDetails = $order->order->orderDetails ?? collect();-->

                <!--    $netAmount = $orderDetails->sum('price');-->
                <!--    $tax = $orderDetails->sum('tax');-->
                <!--    $shipping = $orderDetails->sum('shipping_cost');-->
                <!--    $discount = $orderDetails->sum('coupon_discount');-->
                <!--    $grandTotal = ($netAmount + $tax + $shipping) - $discount;-->

                <!--    // Accumulate totals-->
                <!--    $totalNetAmount += $netAmount;-->
                <!--    $totalTax += $tax;-->
                <!--    $totalShipping += $shipping;-->
                <!--    $totalDiscount += $discount;-->
                <!--    $totalGrand += $grandTotal;-->
                <!--@endphp-->
                <tr>
                    <td>{{$order->id ?? 'N/A'}}</td>
                    <td>{{$order->user->name . ' ' . $order->user->last_name}}
                    </td>
                    <td>{{$order->order->invoice_number ?? 'N/A'}}</td>
                    <td>{{$order->order->code ?? 'N/A'}}</td>
                    <td>{{$order->due_date ?? 'N/A'}}</td>
                    <td>{{debit_price($order->order_id)}}</td>
                    <td>{{credit_price($order->order_id)}}</td>
                    <td>{{due_price($order->order_id)}}</td>
                    <td>

                        <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                            href="{{route('excep_amount_view', $order->order_id)}}" title="{{ translate('View') }}">
                            <i class="las la-eye"></i>


                        </a>
                        <a class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                            href="{{route('orderReturn', $order->order_id)}}" title="{{ translate('Return') }}">
                            <i class="las la-angle-double-left"></i>


                        </a>
                        <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                            cust-id="{{ $order->order_id }}"
                            val="{{ isset($order->user->registerCredit->company_name) ? $order->user->registerCredit->company_name : 'N/A'  }}"
                            uval="{{ isset($order->user->name) && isset($order->user->last_name) ? $order->user->name . ' ' .
                        $order->user->last_name : 'N/A' }}" invo="{{ $order->order->invoice_number ?? '' }}"
                            po="{{ $order->order->code ?? 'N/A' }}" track="{{ $order->order->tracking_code ?? 'N/A' }}"
                            da="{{due_price($order->order_id)?? ''}}" onclick="openModal(this)" title="Add Remittance">
                            <i class="las la-edit"></i>
                        </a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $orders->links() }}
        </div>
    </div>

<style>
/* Modal background overlay */
#remittanceModal {
    position: fixed;
    inset: 0; /* top right bottom left = 0 */
    background: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 9999;

    /* Perfect center */
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Modal box */
#remittanceModal .modal-content {
    background: #fff;
    width: 50%;
    max-width: 1000px;
    border-radius: 10px;
    padding: 25px;

    /* force center */
    margin: 0 auto;

    animation: fadeIn 0.25s ease-in-out;
}

/* Mobile responsive */
@media (max-width: 768px) {
    #remittanceModal .modal-content {
        width: 95%;
        padding: 15px;
    }
}

/* Animation */
@keyframes fadeIn {
    from {
        transform: translateY(-10px) scale(0.95);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}
</style>

    <!-- Model -->
    <div id="remittanceModal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Payment Details Form</h5>
                <button type="button" class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('payment.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <!-- Laravel's CSRF Token -->
                    <div class="form-group">
                        <div class="row">
                            <input type="hidden" id="account_id" name="account_id">
                            <div class="col-6">
                                <label for="customerName" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="companyName" name="company_name" readonly>
                            </div>
                            <div class="col-6">
                                <label for="customerName" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customerName" name="customer_name" readonly>
                            </div>
                            <div class="col-6">
                                <label for="customerName" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoiceNumber" name="invoice_number"
                                    readonly>

                            </div>
                            <div class="col-6">
                                <label for="customerName" class="form-label">Po Number</label>
                                <input type="text" class="form-control" id="po_number" name="po_number" readonly>
                            </div>
                            <div class="col-6">
                                <label for="customerName" class="form-label">Tracking Number</label>
                                <input type="text" class="form-control" id="tracking_number" name="tracking_number"
                                    readonly>
                            </div>

                        </div>

                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label for="paymentDate" class="form-label">Payment Date</label>
                                <input type="date" id="paymentDate" name="payment_date" class="form-control">
                                <span id="paymentDate1_error" class="text-danger error-message"
                                    style="display: none;">Please enter a payment date.</span>
                            </div>
                            <div class="col-6">
                                <label for="paymentRef" class="form-label">Payment Ref</label>
                                <input type="text" id="paymentRef" name="payment_ref" class="form-control">
                                <span id="remmittanNumber_error" class="text-danger error-message"
                                    style="display: none;">Please enter a valid payment reference.</span>

                            </div>

                        </div>
                    </div>



                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label for="dueAmount" class="form-label">Due Amount</label>
                                <input type="text" id="dueAmount" name="due_amount" class="form-control" value="1000"
                                    readonly>
                            </div>
                            <div class="col-6">
                                <label for="paymentAmount" class="form-label">Payment Amounts</label>
                                <!--<input type="number" id="paymentAmount" name="payment_amount" class="form-control">-->
                                <input type="number" id="paymentAmount" name="payment_amount" class="form-control"
                                    step="any" min="0"> <span id="paymentAmount_error" class="text-danger error-message"
                                    style="display: none;">Please enter a Amount.</span>

                                <span id="message" style="display:none; font-weight: bold;"></span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label for="remainingAmount" class="form-label">Remaining Amount</label>
                                <input type="number" id="remainingAmount" name="remaining_amount" class="form-control"
                                    readonly>

                            </div>
                            <div class="col-6">
                                <label for="uploadPdf" class="form-label">Upload a PDF</label>
                                <input type="file" id="uploadPdf" name="payment_pdf" class="form-control" accept=".pdf">
                            </div>
                        </div>

                    </div>
                    <script>
                        document.getElementById('paymentAmount').addEventListener('input', function () {

                            // Get due and payment amounts
                            var dueAmount = parseFloat(document.getElementById('dueAmount').value.replace(/[^0-9.]/g,
                                '')); // Parse due amount
                            var paymentAmount = parseFloat(this.value); // Parse payment input
                            var remainingAmountInput = document.getElementById('remainingAmount'); // Remaining amount field
                            var messageSpan = document.getElementById('message'); // Message field

                            // Handle invalid or empty input
                            if (isNaN(paymentAmount) || paymentAmount < 0) {
                                remainingAmountInput.value = dueAmount; // Show full due amount if no valid payment
                                messageSpan.style.display = "none";
                                return;
                            }

                            // Calculate remaining amount
                            var remainingAmount = dueAmount - paymentAmount;

                            // Ensure remaining amount is never negative
                            if (remainingAmount < 0) {
                                remainingAmount = remainingAmount;
                            }

                            // Update the remaining amount field
                            remainingAmountInput.value = remainingAmount.toFixed(2);;

                            // Update the message based on comparison
                            if (paymentAmount === dueAmount) {
                                messageSpan.textContent = "Order Confirm";
                                messageSpan.style.color = "green";
                            } else if (paymentAmount < dueAmount) {
                                messageSpan.textContent = "Your order is in exception payment";
                                messageSpan.style.color = "orange";
                            } else {
                                messageSpan.textContent = "Payment amount exceeded";
                                messageSpan.style.color = "red";
                            }

                            // Show the message
                            messageSpan.style.display = "inline";
                        });
                    </script>

                    <div class="d-flex justify-content-around">
                        <div class="col-4">
                            <button type="submit" id="Payment_detail_id" class="btn btn-primary">Add Payment
                                Details</button>

                        </div>
                        <div class="col-4">
                            <div class=" bg-primary">
                                <a href="" id="delivery_note_btn" type="button" class="btn  text-light"><i
                                        class="las la-print"></i> View delivery Note</a>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="  bg-primary">
                                <a href="" id="delivery_invoice" type="button" class="btn  text-light"><i
                                        class="las la-print"></i> View Invoice</a>


                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- model script -->
    <script>
        function openModal(element) {
            const nameValue = element.getAttribute('val');
            const user_name = element.getAttribute('uval');
            const invoValue = element.getAttribute('invo');
            const poValue = element.getAttribute('po');
            const trackValue = element.getAttribute('track');
            const daValue = element.getAttribute('da');
            const custId = element.getAttribute('cust-id');
            var url = "{{ url('delivery') }}" + "/" + custId;

            var deliveryNoteBtn = document.getElementById('delivery_note_btn');

            deliveryNoteBtn.setAttribute('href', url);
            var url = "{{ url('invoice') }}" + "/" + custId;

            var deliveryNoteBtn = document.getElementById('delivery_invoice');

            deliveryNoteBtn.setAttribute('href', url);
            document.getElementById('customerName').value = user_name;
            document.getElementById('companyName').value = nameValue;
            document.getElementById('invoiceNumber').value = invoValue;
            document.getElementById('po_number').value = poValue;
            document.getElementById('tracking_number').value = trackValue;
            document.getElementById('dueAmount').value = daValue;
            document.getElementById('account_id').value = custId;
            document.getElementById('remittanceModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('remittanceModal').style.display = 'none';
        }
    </script>
    <div class="tab-content" id="tab2">
        <h5>Remittance Content</h5>
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <!--<th data-breakpoints="lg">#</th>-->
                    <th>
                        <div class="form-group">
                            <div class="aiz-checkbox-inline">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-all">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                    </th>

                    <th data-breakpoints="lg">Cusotmer Name</th>
                    <th data-breakpoints="lg">Payment Date</th>
                    <th data-breakpoints="lg">Remmittan Payment-Refrence</th>
                    <th data-breakpoints="lg">Remittance value</th>
                    <th data-breakpoints="lg">Paid Amount</th>
                    <th data-breakpoints="lg">Total Amount Due</th>
                    <th data-breakpoints="lg">Status</th>
                    <th data-breakpoints="lg">Action</th>

                </tr>
            </thead>
            <tbody>
                @foreach($remittance as $remittancec)
                {{-- @if(due_price_without_currency($order->order_id) !== 0) --}}

                <tr>
                    <td>{{$remittancec->id ?? 'N/A'}}</td>
                    <td>
                        {{$remittancec->user->name . ' ' . $remittancec->user->last_name}}
                    </td>
                    <td>{{$remittancec->payment_date ?? 'N/A'}}</td>
                    <td>{{$remittancec->payment_ref ?? 'N/A'}}</td>
                    <td>{{$remittancec->add_remittance_value ?? 'N/A'}}</td>
                    <td>{{$remittancec->paid_amount ?? 'N/A'}}</td>
                    <td>{{$remittancec->remaining_invoice ?? 'N/A'}}</td>
                    <td>
                        @if($remittancec->remaining_invoice == 0)
                        <span style="color:green">Payment Confirm</span>
                        @else
                        <span style="color:red">Exception Payment</span>
                        @endif
                    </td>
                    <td>

                        <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                            href="{{route('payment_ref', $remittancec->payment_ref)}}" title="{{ translate('View') }}">
                            <i class="las la-eye"></i>
                        </a>

                    </td>


                </tr>
                {{-- @endif --}}
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{-- {{ $order->links() }} --}}
        </div>
    </div>
</div>

<script>
    // JavaScript to handle tab switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to the clicked button and corresponding content
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
</script>

<script>
    function filterInvoice() {
        let input = document.getElementById("searchInvoice").value.toLowerCase();
        let rows = document.querySelectorAll("#invoiceTable tr");

        rows.forEach(row => {

            let invoiceCell = row.cells[2];

            if (invoiceCell) {
                let invoiceText = invoiceCell.textContent.toLowerCase();

                if (invoiceText.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    }
</script>





@endsection