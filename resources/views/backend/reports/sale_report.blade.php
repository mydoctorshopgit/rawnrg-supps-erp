@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('Sale Report') }}</h5>
                </div>

             

                <div class="col-lg-2 ml-auto">
                    <select name="customer_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->company_name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    
                  
                </div>
                
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="aiz-date-range form-control" value="{{ $date }}"
                            name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                            data-separator=" to " data-advanced-range="true" autocomplete="off">
                    </div>
                </div>
            
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>
                <div class="col-auto">
                    @php
                    $date = request()->input('date') ?? 'default';
        $customerId = request()->input('customer_id') ?? '';
              

                @endphp
                <div class="form-group mb-0">
                   
                     <a class="btn btn-primary" href="{{ url('admin/sale/pdf/' . $date . '/' . $customerId) }}">
                        {{ translate('PDF Export') }}
                    </a> 
                      <a class="btn btn-primary" href="{{ url('admin/sale/excel/' . $date . '/' . $customerId) }}">
                        {{ translate('Excel Export') }}
                    </a> 
                </div>
                </div>
            </div>
<div class="row justify-content-center text-center mt-3">
    <div class="col-md-2 row ">
        <span>Net Amount</span>:
        <strong> £{{number_format($totalNetAmount,2)}}</strong>
    </div>
    <div class="col-md-2">
        <span>Vat Tax</span>:
                                     @php
    $price = $totalNetAmount;
    $taxFromPrice = $price * 0.20; // 20%
@endphp
        <strong>£{{number_format($taxFromPrice,2)}}</strong>
    </div>
    <div class="col-md-2">
        <span>Discount</span>:
        <strong>£{{number_format($coupon_discount,2)}}</strong>
    </div>
    <div class="col-md-2">
        <span>Carriage Amount</span>:
        <strong>£{{number_format($shipping_cost,2)}}</strong>
    </div>
    <div class="col-md-2">
        <span>Total Amount</span>:
        <strong>£{{number_format(($taxFromPrice+$totalNetAmount+$shipping_cost)-$coupon_discount,2)}}</strong>
    </div>
</div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('Invoice Number') }}</th>
                            <th>{{ translate('PO No') }}</th>
                            <th>{{ translate('Order Date') }}</th>
                            <th>{{ translate('Payment Due Date') }}</th>
                            <th>{{ translate('QTY') }}</th>
                            <th>{{ translate('Net Amount') }}</th>
                            <th>{{ translate('VAT') }}</th>
                            <th>{{ translate('Discount') }}</th>
                            <th>{{ translate('Carrige Amount') }}</th>
                            <th>{{ translate('Total Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                         @php

                         $sum_debit = 0;
                         $price = 0;
                         $tax = 0;
                         $coupon_discount = 0;
                         $shipping = 0;
                        @endphp
                        @foreach ($orders as $order)
                            @php
                                $sum_debit += $order->account->sum('debit');
                                 $price += $order->orderDetails->sum('price');
                                 $tax += $order->orderDetails->sum('tax');
                                 $coupon_discount += $order->orderDetails->sum('coupon_discount');
                                 $shipping += $order->orderDetails->sum('shipping_cost');
                            @endphp
                            <tr>
                                <td>
                                                                          {{ $order->customer_details->company_name ?? ''}}
                               
                                
                                </td>
                                <td>{{ $order->invoice_number }}</td>
                                <td>{{ $order->purchase_order_number }}</td> <!-- Assuming PO No is stored as post_code -->
                                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                <td>{{ $order->account->first()->due_date ?? ''}}</td>
                                <td>{{ $order->orderDetails->sum('quantity') }}</td>
                              <td>{{ number_format($order->orderDetails->sum('price'), 2) }}</td>
                              @php
    $price = $order->orderDetails->sum('price');
    $taxFromPrice = $price * 0.20; // 20%
@endphp
                              
<td>{{ number_format($taxFromPrice, 2) }}</td>
                                <td>{{ $order->coupon_discount }}</td>
                                <td>{{ $order->orderDetails->sum('shipping_cost') }}</td>
                               <!--<td>{{ single_price($order->grand_total ?? 0) }}</td>-->
                               <td>{{ number_format(($order->orderDetails->sum('price')+$taxFromPrice+$order->orderDetails->sum('shipping_cost'))-$order->coupon_discount,2) }}</td>

                                
                            </tr>
                            
                        @endforeach
                    </tbody>
                   <tfoot>
                   {{--      <tr>
                            <td colspan="6"></td>
                            <td> <h4>{{ round( $price,2)}}</h4></td>
                            <td> <h4>{{round( $tax ,2)  }}</h4></td>
                            <td> <h4>{{ $shipping }}</h4></td>
                            <td> <h4>{{ $coupon_discount }}</h4></td>
                            <td> <h4>{{ $sum_debit }}</h4></td>
                        </tr>  --}}
                    </tfoot>
                </table> 

   <div class="aiz-pagination mt-4">
                    {{ $orders->appends(request()->input())->links() }}
                </div>
            </div>
        </form>
    </div>
@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')
@endsection


