@extends('backend.layouts.app')

@section('content')
<style>
    .table-container {
        padding: 20px;
    }

    .add-remittance-btn {
        float: right;
        margin-bottom: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    .add-remittance-btn:hover {
        background-color: #0056b3;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background-color: #ECECEC;
    }

    .table th,
    .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .table tbody {
        background-color: white;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
    }

    .btn-close {
        background-color: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }

    .modal-body {
        margin-top: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .header {
        background-color: white;
    }

    .footable-filtering {
    margin-bottom: 15px;
}
.footable-filtering .form-control {
    width: 250px;
    display: inline-block;
}
</style>

<div class="container table-container">
    <div class="d-flex justify-content-between align-items-center  mb-4 header">
 <div class="col-12 col-md-8">
    <form method="GET" action="{{ route('invoice_payable') }}" id="searchForm" class="d-flex w-100">
        <div class="d-flex">
            <!-- Input -->
            <input type="text" 
                   name="search" 
                   class="form-control"  
                   placeholder="Search by customer,invoice" 
                   value="{{ request('search') }}" style="width:80%;">
            
            <!-- Button with icon inside input -->
            <button class="btn btn-primary" type="submit" style="width:20%;">
                <i class="las la-search"></i>
            </button>
        </div>
    </form>
</div>




        <a href="{{ route('remittance_form') }}" class="btn btn-primary col-2">Add-Remittance</a>
    </div>

    <table class="table aiz-table mb-0">
        <thead>
            <tr>
                <th data-breakpoints="lg">Business Name</th>
                <th data-breakpoints="lg">Customer Name</th>
                <th data-breakpoints="lg">Po Number</th>
                <th data-breakpoints="lg">Invoice Number</th>
                <th data-breakpoints="lg">Transaction ID</th>
                <th data-breakpoints="lg">Charge ID</th>
                <th data-breakpoints="lg">Tracking Number</th>
                <th data-breakpoints="lg">Amount Withheld</th>
                <th data-breakpoints="lg"> Due Date</th>
                <th data-breakpoints="lg">Total Amount Due</th>
                <th data-breakpoints="lg">Action</th>
            </tr>
        </thead>
        <tbody  id="invoiceTable">
            <!--@php-->
            <!--    $totalNetAmount = 0;-->
            <!--    $totalTax = 0;-->
            <!--    $totalShipping = 0;-->
            <!--    $totalDiscount = 0;-->
            <!--    $totalGrand = 0;-->
            <!--@endphp-->

            @foreach ($orders as $order)
            <!--@php-->
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
                <td>{{ isset($order->user->registerCredit->company_name) ? $order->user->registerCredit->company_name :
                    'N/A' }}
                </td>
                <td>
                    {{ isset($order->user->name) && isset($order->user->last_name) ? $order->user->name . ' ' .
                    $order->user->last_name : 'N/A' }}
                    <br>
                    @if ($order->user?->user_type == 'customer_credit')
                    <span class="badge-warning ">Credit / <b>AC</b></span>
                    @elseif ($order->user?->user_type == 'customer')
                    <span class="badge-primary ">Register / <b>AC</b></span>
                    @elseif ($order->user?->user_type == 'customer_guest')
                    <span class="badge-secondary ">Guest / <b>AC</b></span>
                    @elseif ($order->user?->user_type == 'customer_pharmaceuti')
                    <span class="badge-info ">Pharmaceutical / <b>AC</b></span>
                    @endif
                </td>
                <td>{{ $order->order->code ?? 'N/A' }}</td>
                <td>{{ $order->order->invoice_number ?? 'N/A' }}</td>
                <td>
                    @php
                    $payment_details_parsed = json_decode($order->order->payment_details ?? "");
                    @endphp
                    {{ isset($payment_details_parsed->transactionId) && isset($payment_details_parsed->chargeId) ? $payment_details_parsed->transactionId : 'N/A' }}
                </td>

                <td>{{ isset($payment_details_parsed->transactionId) && isset($payment_details_parsed->chargeId) ? $payment_details_parsed->chargeId : 'N/A' }}
                <td>{{ $order->order->tracking_code ?? 'N/A' }}</td>
                <td>{{ $order->order->amount_withheld ?? 'N/A' }}</td>
                <td>{{ $order->due_date ?? 'N/A' }}</td>
                <td>{{ single_price(optional($order->order)->grand_total) ?? '' }}</td>

                <td>
                    <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm" cust-id="{{ $order->order_id }}"
                        val="{{ isset($order->user->registerCredit->company_name) ? $order->user->registerCredit->company_name : 'N/A'  }}"
                        uval="{{ isset($order->user->name) && isset($order->user->last_name) ? $order->user->name . ' ' .
                        $order->user->last_name : 'N/A' }}"
                        invo="{{ $order->order->invoice_number ?? '' }}"
                        po="{{ $order->order->code ?? 'N/A' }}"
                        track="{{ $order->order->tracking_code ?? 'N/A' }}"
                        da="{{ single_price(optional($order->order)->grand_total) ?? ''}}" onclick="openModal(this)"
                        title="Add Remittance">
                        <i class="las la-edit"></i>
                    </a>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p>{{ $orders->links() }}</p>
</div>

<!-- Modal -->
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
                            <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" readonly>

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
 <input 
        type="number" 
        id="paymentAmount" 
        name="payment_amount" 
        class="form-control"
        step="any" 
        min="0"
    >                            <span id="paymentAmount_error" class="text-danger error-message"
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
                    document.getElementById('paymentAmount').addEventListener('input', function() {
                          
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




<script>

    $(document).ready(function() {
    let timeout;

    $('input[name="search"]').on('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            $('#searchForm').submit();   // or use AJAX fetch
        }, 500);  // debounce
    });
                                 
    // Or full AJAX version:
    /*
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        let url = $(this).attr('action') + '?' + $(this).serialize();

        $.get(url, function(data) {
            // Replace only table + pagination
            $('#invoiceTable').html($(data).find('#invoiceTable').html());
            $('.pagination').html($(data).find('.pagination').html());
        });
    });
    */
});
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
<script>
    document.querySelector("form").addEventListener("submit", function (event) {
    const paymentDate = document.getElementById("paymentDate");
    const remmittanNumber = document.getElementById("paymentRef");
    const paymentAmount = document.getElementById('paymentAmount').value.trim();

    const paymentDateError = document.getElementById("paymentDate1_error");
    const remmittanNumberError = document.getElementById("remmittanNumber_error");
    const paymentAmountError = document.getElementById("paymentAmount_error");

    let isValid = true;

    // Validate Payment Date
    if (paymentDate.value.trim() === "") {
        paymentDateError.style.display = "block";
        isValid = false;
    } else {
        paymentDateError.style.display = "none";
    }

    // Validate Payment Amount
    if (paymentAmount.value.trim() === "") {
        paymentAmountError.style.display = "block";
        isValid = false;
    } else {
        paymentAmountError.style.display = "none";
    }

    // Validate Payment Reference
    if (remmittanNumber.value.trim() === "") {
        remmittanNumberError.style.display = "block";
        isValid = false;
    } else {
        remmittanNumberError.style.display = "none";
    }

    // If validation fails, prevent form submission
    if (!isValid) {
        event.preventDefault(); // Prevents the form from submitting
    }
});

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