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
<div class="d-flex col-12 ">
    <div class="col-3">
        <span style="color: #333; font-size: 16px; font-weight: bold; padding: 5px 10px;  border-radius: 5px;">
            Customer Name:
        </span>
        {{ 
            $remittance->user->name .''.$remittance->user->last_name
        }} 
         </div>
    <div class="col-3">
        <span style="color: #333; font-size: 16px; font-weight: bold; padding: 5px 10px;  border-radius: 5px;">
            Payment Reference:
        </span>
        
        {{ $remittance->payment_ref }}</div>
    <div class="col-2">
        <span style="color: #333; font-size: 16px; font-weight: bold; padding: 5px 10px;  border-radius: 5px;">
            Payment Date:
        </span>

        {{ $remittance->payment_date }}
    </div>
    <div class="col-2">
        <span style="color: #333; font-size: 16px; font-weight: bold; padding: 5px 10px;  border-radius: 5px;">
             Paid Amount:
        </span>

        {{ $remittance->paid_amount }}
    </div>
    <div class="col-2">
        <span style="color: #333; font-size: 16px; font-weight: bold; padding: 5px 10px;  border-radius: 5px;">
            Total Amount Due:
        </span>
  {{ $remittance->remaining_invoice }}
      
    </div>
</div>
<div class="container table-container">
    {{--  <div class="d-flex justify-content-between align-items-center  mb-4 header">
        <h2>Invoices Payables</h2>
        <button class="add-remittance-btn" onclick="openModal()">+ Add Remittance</button>
    </div> --}}
   
    <table class="table aiz-table mb-0 mt-5">
        <thead>
        <tr>
            <th data-breakpoints="lg">Customer Name</th>
            <th data-breakpoints="lg">Invoice Number</th>
            <th data-breakpoints="lg">Po Number</th>
            <th data-breakpoints="lg"> Due Date</th>
            <th data-breakpoints="lg">Total Amount Due</th>
            <th data-breakpoints="lg">Action</th>
          
        </tr>
        </thead>
        <tbody>
              @php
        $totalNetAmount = 0;
        $totalTax = 0;
        $totalShipping = 0;
        $totalDiscount = 0;
        $totalGrand = 0;
    @endphp
            @foreach($accountData as $order)
                @php
                dd($order);
            $orderDetails = $order->order->orderDetails ?? collect();

            $netAmount = $orderDetails->sum('price');
            $tax = $orderDetails->sum('tax');
            $shipping = $orderDetails->sum('shipping_cost');
            $discount = $orderDetails->sum('coupon_discount');
            $grandTotal = ($netAmount + $tax + $shipping) - $discount;

            // Accumulate totals
            $totalNetAmount += $netAmount;
            $totalTax += $tax;
            $totalShipping += $shipping;
            $totalDiscount += $discount;
            $totalGrand += $grandTotal;
        @endphp
            
            <tr>
                <td>{{$order->user->name ?? "" . ' ' . $order->user->last_name ?? ""}}</td>
                <td>{{$order->order->invoice_number ?? 'N/A'}}</td>
                <td>{{$order->order->code ?? 'N/A'}}</td>
                <td>{{$order->comments ?? 'N/A'}}</td>
                <td style="background-color: {{ (float) preg_replace('/[^0-9.-]+/', '', single_price(optional($order->order)->grand_total) ?? '') !== 0.00 ? '#FF623E' : '#6dea64' }};">
                 {{ single_price($grandTotal) ?? '' }}
                </td>
                <td>  <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{url('admin/orders-view/'.$order->order_id.'/show?q=orders-view')}}"  title="{{ translate('View') }}">
                    <i class="las la-eye"></i>
                </a></td>
              
            </tr>
            @endforeach
            
        </tbody>
    </table>
    {{-- <p>{{$orders->links()}}</p> --}}
</div>

    <!-- Modal -->


@endsection
