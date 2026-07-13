@extends('backend.layouts.app')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
    Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK'
        });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'Try Again'
        });
</script>
@endif


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
        <h5>Invoice Content</h5>
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

                </tr>
            </thead>
            <tbody>

                @foreach($orders as $order)

                <tr>
                    <td>{{$order->id ?? 'N/A'}}</td>
                    <td>{{ $order->user->name }}
                    </td>
                    <td>{{$order->order->invoice_number ?? 'N/A'}}</td>
                    <td>{{$order->order->code ?? 'N/A'}}</td>
                    <td>{{$order->due_date ?? 'N/A'}}</td>
                    <td>{{debit_price($order->order_id)}}</td>
                    <td>{{credit_price($order->order_id)}}</td>
                    <td>{{due_price($order->order_id)}}</td>
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
                    <th data-breakpoints="lg">Total Amount Due</th>

                </tr>
            </thead>
            <tbody>
                @foreach($remittance as $remittancec)

                <tr>
                    <td>{{$remittancec->id ?? 'N/A'}}</td>
                    <td>
                        {{
                        $remittancec->customer_details?->company_name ?? 'N/A'
                        }}
                    </td>
                    <td>{{$remittancec->payment_date ?? 'N/A'}}</td>
                    <td>{{$remittancec->payment_ref ?? 'N/A'}}</td>
                    <td>{{$remittancec->add_remittance_value ?? 'N/A'}}</td>
                    <td>{{$remittancec->paid_amount ?? 'N/A'}}</td>
                    <td>{{$remittancec->remaining_invoice ?? 'N/A'}}</td>




                </tr>
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






@endsection