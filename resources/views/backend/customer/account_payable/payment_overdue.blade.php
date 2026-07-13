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
    .table th, .table td {
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
        background-color: rgba(0,0,0,0.4);
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
    .header{
        background-color: white;
    }
</style>

<div class="container table-container">
    <div class="card-body row align-items-center justify-content-between">
        <!-- Left Section: Date Input and Filter Button -->
        <div class="d-flex align-items-center col-auto">
            <!-- Date Filter -->
            <div class="form-group mb-0 me-2">
                <input type="text" class="aiz-date-range form-control" id="filter-date"
                name="date" placeholder="Filter by date" data-format="DD-MM-Y"
                data-separator=" to " data-advanced-range="true" autocomplete="off">
            </div>
            <!-- Filter Button -->
            <div class="form-group mb-0 " style="margin-left:5px;">
                <button class="btn btn-primary" id="filter-orders-btn">Filter</button>
                        </div>
        </div>
    
        <!-- Right Section: Send Statement Button -->
        <div class="col-auto">
            <button class="add-remittance-btn" onclick="openModal()">Send Statement</button>       
    </div>
    </div>
    
    <table class="table table-bordered" id="orders-table">
        <thead>
        <tr>
            <th data-breakpoints="lg">Customer Name</th>
            <th data-breakpoints="lg">Invoice Number</th>
            <th data-breakpoints="lg">Po Number</th>
            <th data-breakpoints="lg">Order Date</th>
            <th data-breakpoints="lg"> Due Date</th>
            <th data-breakpoints="lg">Status</th>
            <th data-breakpoints="lg">Total Amount</th>
            <th data-breakpoints="lg">Paid Amount</th>
            <th data-breakpoints="lg">Action</th>
        </tr>
        </thead>
        <tbody>
            
    <!--          @php-->
    <!--    $totalNetAmount = 0;-->
    <!--    $totalTax = 0;-->
    <!--    $totalShipping = 0;-->
    <!--    $totalDiscount = 0;-->
    <!--    $totalGrand = 0;-->
    <!--@endphp-->
            @foreach($orders as $order)
        <!--        @php-->
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
            @if(strtotime(date("Y-m-d")) > strtotime($order->due_date) )

            <tr>
<td>{{ ($order->user->name ?? '') . ' ' . ($order->user->last_name ?? '') }}</td>

                <td>{{$order->order->invoice_number ?? 'N/A'}}</td>
                <td>{{$order->order->code ?? 'N/A'}}</td>
                <td>{{date('d-m-Y',strtotime($order->created_at)) ?? 'N/A'}}</td>
                <td>{{$order->due_date ?? 'N/A'}}</td>
                <td>
                    Invoice: <span style="color:red">{{$order->last_sending_mail == 1 ? "Sent Mail":'Pending'}} </span>
                    <br>
                    Statement: <span style="color:red">{{$order->last_sending_statement == 1 ? "Sent Statement":'Pending'}}</span>
                </td>
                <td>{{debit_price($order->order_id)}}</td>
                <td>{{ single_price(optional($order->order)->grand_total) ?? '' }}</td>
                <td>
                    <a href="#" class="send_invoice btn btn-soft-primary btn-icon btn-circle btn-sm" order-id="{{$order->order_id}}" email="{{$order->user->email ?? "" }}" onclick="sendInvoice(this)"   title="Send Invoice Remainder">
                        <i class="las la-envelope"></i>
                    </a>
                    {{-- <a href="#" class="send_invoice btn btn-soft-primary btn-icon btn-circle btn-sm" order-id="{{$order->order_id}}"  email="{{$order->user->email ?? "" }}" onclick="sendStatement(this)"   title="Send Statement Remainder">
                        <i class="las la-mail-bulk"></i>
                    </a> --}}
                </td>
                
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    <p>{{$orders->links()}}</p>
</div>

    <!-- Modal -->
    <div id="remittanceModal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Statement Send</h5>
                <button type="button" class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('send.customers.statement') }}" method="post" enctype="multipart/form-data">
                    @csrf <!-- Laravel's CSRF Token -->
                    <div class="form-group">
                        <label for="customerName" class="form-label">Customer Name</label>
                        <select name="customer_id" class="form-control aiz-selectpicker pos-customer"
                            id="customerSelect" data-live-search="true" required>
                            <option value="">Select Customers</option>
                            @foreach ($customer as $key => $customer)
                                {{-- @if (isset($customer->contactInformation)) --}}
                                    <option value="{{ $customer->id }}">
                                   {{ ($order->user->name ?? '') . ' ' . ($order->user->last_name ?? '') }}

                                    </option>
                                {{-- @endif --}}
                            @endforeach
                        </select>
                    </div>
                   {{--  <div class="form-group">
                        <label for="paymentDate" class="form-label">Select Date</label>
                        <input type="text" class="aiz-date-range form-control"
                        name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                        data-separator=" to " data-advanced-range="true" autocomplete="off" required>                    </div> --}}
                    
                    <button type="submit" class="btn btn-primary">Send Statement</button>
                </form>
            </div>
        </div>
    </div>
<script>
// xyz
document.getElementById('filter-orders-btn').addEventListener('click', function () {
    const dateRange = document.getElementById('filter-date').value;

    fetch('{{ route('filter_date') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
        body: JSON.stringify({
            date: dateRange
        })
    })
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById('orders-table-body');
        tableBody.innerHTML = '';

        if (data.orders && data.orders.length > 0) {
            data.orders.forEach(order => {
                const row = `
                    <tr>
                        <td>${order.customer_name || 'N/A'}</td>
                        <td>${order.invoice_number || 'N/A'}</td>
                        <td>${order.code || 'N/A'}</td>
                        <td>${order.due_date || 'N/A'}</td>
                        <td>${order.due_price || 'N/A'}</td>
                        <td>
                            <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               order-id="${order.order_id}"
                               email="${order.customer_email}"
                               onclick="sendInvoice(this)"
                               title="Send Invoice">
                               <i class="las la-mail-bulk"></i>
                            </a>
                            <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               order-id="${order.order_id}"
                               email="${order.customer_email}"
                               onclick="sendStatement(this)"
                               title="Send Statement">
                               <i class="las la-mail-bulk"></i>
                            </a>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">No records found</td>
                </tr>
            `;
        }
    })
    .catch(error => console.error('Error fetching orders:', error));
});






    function openModal(element) {
    // Get the value from the 'val' attribute of the clicked element
    // const nameValue = element.getAttribute('val');
    // const custId = element.getAttribute('cust-id');

    // // Set the value to the input field in the modal
    // document.getElementById('customerName').value = nameValue;
    // document.getElementById('account_id').value = custId;

    // Display the modal
    document.getElementById('remittanceModal').style.display = 'block';
}

function closeModal() {
    // Hide the modal
    document.getElementById('remittanceModal').style.display = 'none';
}

    function closeModal() {
        document.getElementById('remittanceModal').style.display = 'none';
    }
    function sendInvoice(element) {
        var order_id = element.getAttribute('order-id');
        var email = element.getAttribute('email');
        // console.log(email);
        // return false
        fetch('{{ route('send.invoices') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ @csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: order_id,
                email: email
            })
        })
        .then(response => response.json())
        .then(data => {
            // Assuming `AIZ.plugins.notify` is your custom notification plugin
            AIZ.plugins.notify('success', 'Email sent Successfully');

            // window.location.href = "{{ route('inhouse_orders.index') }}";
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };

    function sendStatement(element) {
        var order_id = element.getAttribute('order-id');
        var order_id = element.getAttribute('order-id');
        var email = element.getAttribute('email');
        // console.log(email);jh
        // return false
        fetch('{{ route('send.statement') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ @csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: order_id,
                order_id: order_id,
                email: email
            })
        })
        .then(response => response.json())
        .then(data => {
            // Assuming `AIZ.plugins.notify` is your custom notification plugin
            AIZ.plugins.notify('success', 'Statement sent Successfully');

            // window.location.href = "{{ route('inhouse_orders.index') }}";
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };

</script>
</script>

@endsection
