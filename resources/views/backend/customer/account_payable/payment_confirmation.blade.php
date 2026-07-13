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
</style>
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
    <h5 class="mb-0">Confirmation Invoice</h5>

    <!-- RIGHT: Search -->
    <form method="GET"
          action="{{ route('payment_confirmation') }}"
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
                    <th data-breakpoints="lg">Paid Amount</th>
                    <th data-breakpoints="lg">Remaining</th>
                    <th data-breakpoints="lg">Action</th>
                </tr>
            </thead>
            <tbody  id="invoiceTable">
               
                @foreach($orders as $order)
               
             
                <tr>
                    <td>{{$order->id ?? 'N/A'}}</td>
                    <td>{{$order->user->name ?? "" . ' ' . $order->user->last_name ?? ""}}
                    </td>
                    <td>{{$order->order->invoice_number ?? 'N/A'}}</td>
                    <td>{{$order->order->code ?? 'N/A'}}</td>
                    <td>{{$order->due_date ?? 'N/A'}}</td>
                    <td>{{debit_price($order->order_id)}}</td>
                    <td>{{credit_price($order->order_id)}}</td>
                    <td>{{due_price($order->order_id)}}</td>
                    <td>
                        <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm" prim-id="{{$order->id}}"
                            cust-id="{{$order->order_id}}" po_no="{{$order->order->purchase_order_number}}"
                            cname="{{$order->user->name  . ' ' . $order->user->last_name}}"
                            invoice_number="{{$order->order->invoice_number ?? 'N/A'}}"
                            due_date="{{$order->due_date ?? 'N/A'}}" total="{{debit_price($order->order_id) ?? 'N/A'}}"
                            paid="{{credit_price($order->order_id) ?? 'N/A'}}"
                            due_price="{{ single_price(optional($order->order)->grand_total) ?? ''  }}"
                            remmittan_payment_number="{{$order->remmittan_payment_number ?? 'N/A'}}"
                            onclick="openModal(this)" title="Add Remittance">
                            <i class="las la-edit"></i>
                        </a>
                          <a class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                            href="{{route('orderReturn', $order->order_id)}}" title="{{ translate('Return') }}">
                            <i class="las la-angle-double-left"></i>


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
                    <th data-breakpoints="lg">Total Amount Due </th>
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
                        {{$remittancec->user->name ?? "" . ' ' . $remittancec->user->last_name ?? ""}}
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

                        <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                            prim-id="{{$remittancec->id}}"
                            cname="{{$remittancec->user->name  . ' ' . $remittancec->user->last_name}}"
                            due_date="{{$remittancec->payment_date ?? 'N/A'}}"
                            due_price="{{$remittancec->add_remittance_value}}"
                            remmittan_payment_number="{{$remittancec->payment_ref ?? 'N/A'}}"
                            paid="{{$remittancec->paid_amount ?? 'N/A'}}"
                            remaining_invoice="{{$remittancec->remaining_invoice ?? 'N/A'}}" onclick="openModal2(this)"
                            title="Confirm Remmittance">
                            <i class="las la-edit"></i>
                        </a>

                    </td>


                </tr>
                {{-- @endif --}}
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $remittance->links() }}
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
<!-- Modal -->
<div id="remittanceModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Payment Details Form</h5>
            <button type="button" class="btn-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('payment.confirm') }}" method="post" enctype="multipart/form-data">
                @csrf <!-- Laravel's CSRF Token -->
                <div class="form-group">

                    <input type="hidden" name="prim_id" id="prim_id" value="">
                    <input type="hidden" name="request" value="invoice">
                    <label for="customerName" class="form-label">Customer Name</label>
                    <input type="text" class="form-control" id="customerName" name="customer_name" readonly>
                    <input type="hidden" id="account_id" name="account_id">
                </div>
                <div class="form-group">
                    <label for="invoice_numbers" class="form-label">Invoice Number</label>
                    <input type="text" id="invoice_numbers" name="invoice_numbers" class="form-control" value=""
                        readonly required>
                </div>
                <div class="form-group">
                    <label for="paymentDate" class="form-label">Payment Date</label>
                    <input type="date" id="paymentDate" name="payment_date" class="form-control" value="" readonly
                        required>
                </div>
                <div class="form-group">
                    <label for="po_no" class="form-label">PO Number</label>
                    <input type="text" id="po_nos" name="po_no" class="form-control" readonly required>
                </div>
                <div class="form-group">
                    <label for="paymentRef" class="form-label">Payment Reference</label>
                    <input type="text" id="paymentRef" name="payment_ref" class="form-control" readonly required>
                </div>
                <div class="form-group">
                    <label for="paidAmount" class="form-label">Pay Amount</label>
                    <input type="text" id="paidAmount" name="paid_amount" class="form-control" readonly>
                    <span id="message" style="display:none; font-weight: bold;"></span>
                </div>

                <div class="form-group">
                    <label for="paymentAmount" class="form-label">Due Amount</label>
                    <input type="text" id="paymentAmount" name="payment_amount" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label for="remainingAmount" class="form-label">Remaining Amount</label>
                    <input type="text" id="remainingAmount" name="remaining_amount" class="form-control" readonly>
                </div>


                <button type="submit" class="btn btn-primary">Confirm Payment</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="remittanceModal2" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Payment Details Form</h5>
            <button type="button" class="btn-close" onclick="closeModal2()">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('payment.confirm') }}" method="post" enctype="multipart/form-data">
                @csrf <!-- Laravel's CSRF Token -->
                <div class="form-group">

                    <input type="hidden" name="rem_id" id="rem_id" value="">
                    <input type="hidden" name="request" value="remmittance">
                    <label for="customerName" class="form-label">Customer Name</label>
                    <input type="text" class="form-control" id="customerName2" name="customer_name" readonly>
                    <input type="hidden" id="account_id" name="account_id">
                </div>

                <div class="form-group">
                    <label for="paymentDate" class="form-label">Payment Date</label>
                    <input type="date" id="paymentDate2" name="payment_date" class="form-control" value="" readonly
                        required>
                </div>
                <div class="form-group">
                    <label for="paymentRef" class="form-label">Payment Ref</label>
                    <input type="text" id="paymentRef2" name="payment_ref" class="form-control" readonly required>
                </div>
                <div class="form-group">
                    <label for="paymentAmount" class="form-label">Payment Amount</label>
                    <input type="text" id="paymentAmount2" name="payment_amount" class="form-control" readonly required>
                </div>
                <div class="form-group">
                    <label for="paymentAmount" class="form-label">Paid Amount</label>
                    <input type="text" id="paidAmount2" name="paid" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="remainingAmount" class="form-label">Remaining Amount</label>
                    <input type="text" id="remainingAmount2" name="remaining" class="form-control" readonly>
                </div>

                <div class="form-group" id="remainingAmountSection">
                    <span id="remainingAmountMessage"
                        style="display:block; text-align:center; font-weight:bold; color: #d9534f;"></span>
                </div>

                <button type="submit" class="btn btn-primary" id="confirmButton">Confirm Payment</button>
            </form>
        </div>
    </div>
</div>

<script>
    function statusLoad() {
        var dueAmountInput = document.getElementById('paymentAmount');
        var dueAmount = parseFloat(dueAmountInput.value.replace(/[^0-9.]/g, '')) || 0;
        var paidAmount = parseFloat(this.value.replace(/[^0-9.]/g, '')) || 0;
        var remainingAmountInput = document.getElementById('remainingAmount');
        var messageSpan = document.getElementById('message');

        if (isNaN(dueAmount) || dueAmount <= 0) {
            messageSpan.textContent = "Please enter a valid due amount.";
            messageSpan.style.color = "red";
            messageSpan.style.display = "inline";
            remainingAmountInput.value = "";
            return;
        }

        if (isNaN(paidAmount) || paidAmount < 0) {
            remainingAmountInput.value = dueAmount;
            messageSpan.style.display = "none";
            return;
        }

        var remainingAmount = dueAmount - paidAmount;

        remainingAmountInput.value = remainingAmount.toFixed(2); // Keep two decimal places

        if (remainingAmount < 0) {
            messageSpan.textContent = "Payment amount exceeded";
            messageSpan.style.color = "red";
        } else if (remainingAmount === 0) {
            messageSpan.textContent = "Payment Confirmed";
            messageSpan.style.color = "green";
        } else {
            messageSpan.textContent = "Your order is in exception payment";
            messageSpan.style.color = "orange";
        }

        messageSpan.style.display = "inline";
    }

    statusLoad();

</script>
<script>
    // Function to check if the remaining amount is zero or empty and update the UI accordingly
    function updatePaymentStatus() {
        const remainingAmountField = document.getElementById("remainingAmount2");
        const remainingAmountMessage = document.getElementById("remainingAmountMessage");
        const confirmButton = document.getElementById("confirmButton");

        // Get the value of the remaining amount input (as a string)
        const remainingAmountValue = remainingAmountField.value.trim();

        // Check if the remaining amount is empty or zero
        if (remainingAmountValue === "" || parseFloat(remainingAmountValue) === 0) {
            remainingAmountMessage.textContent = "Your payment is confirmed.";
            remainingAmountMessage.style.color = "green";
        } else {
            remainingAmountMessage.textContent = "Payment still pending.";
            remainingAmountMessage.style.color = "red";
        }
    }

    updatePaymentStatus();

    document.getElementById("remainingAmount2").addEventListener("input", updatePaymentStatus);
</script>

<script>
    function openModal(element) {
        // Get the value from the 'val' attribute of the clicked element
        const prim_id = element.getAttribute('prim-id');
        const nameValue = element.getAttribute('cname');
        const custId = element.getAttribute('cust-id');
        const invoice_number = element.getAttribute('invoice_number');
        const due_date = element.getAttribute('due_date');
        const remmittan_payment_number = element.getAttribute('remmittan_payment_number');
        const due_price = element.getAttribute('due_price');
        const total = element.getAttribute('total');
        const paid = element.getAttribute('paid');
        const po_no = element.getAttribute('po_no');



        console.log(remmittan_payment_number);

        // Set the value to the input field in the modal
        document.getElementById('prim_id').value = prim_id;
        document.getElementById('customerName').value = nameValue;
        document.getElementById('account_id').value = custId;
        document.getElementById('invoice_numbers').value = invoice_number;
        document.getElementById('paymentDate').value = due_date;
        document.getElementById('paidAmount').value = total;
        document.getElementById('paymentRef').value = remmittan_payment_number;
        document.getElementById('po_nos').value = po_no;
        document.getElementById('paymentAmount').value = paid;
        document.getElementById('remainingAmount').value = due_price;

        // Display the modal
        document.getElementById('remittanceModal').style.display = 'block';
    }

    function openModal2(element) {
        // Get the value from the 'val' attribute of the clicked element
        const rem_id = element.getAttribute('prim-id');
        const nameValue = element.getAttribute('cname');
        const due_date = element.getAttribute('due_date');
        const remmittan_payment_number = element.getAttribute('remmittan_payment_number');
        const due_price = element.getAttribute('due_price');
        const paid = element.getAttribute('paid');
        const remaining_invoice = element.getAttribute('remaining_invoice');


        // Set the value to the input field in the modal
        document.getElementById('rem_id').value = rem_id;
        document.getElementById('customerName2').value = nameValue;
        document.getElementById('paymentDate2').value = due_date;
        document.getElementById('paymentRef2').value = remmittan_payment_number;
        document.getElementById('paymentAmount2').value = due_price;
        document.getElementById('paidAmount2').value = paid;
        document.getElementById('remainingAmount2').value = remaining_invoice;

        // Display the modal
        document.getElementById('remittanceModal2').style.display = 'block';
    }

    function closeModal() {
        // Hide the modal
        document.getElementById('remittanceModal').style.display = 'none';
    }

    function closeModal2() {
        document.getElementById('remittanceModal2').style.display = 'none';
    }
</script>
<script>
function filterInvoice() {
    let input = document.getElementById("searchInvoice").value.toLowerCase();
    let rows = document.querySelectorAll("#invoiceTable tr");

    rows.forEach(row => {
        // Invoice number column (0 se count hota hai)
        // Tumhare table mein invoice number 3rd column hai
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