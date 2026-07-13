@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('Self Report') }}</h5>
                </div>

             

                <div class="col-lg-2 ml-auto">
                    <select name="customer_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{$customer->company_name}}
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
            </div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('Invoice Number') }}</th>
                            <th>{{ translate('PO No') }}</th>
                            <th>{{ translate('Order Date') }}</th>
                            <th>{{ translate('payment due date') }}</th>
                            <th>{{ translate('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                                                          {{ $order->customer_details->company_name }}
                               
                                </td>
                                <td>{{ $order->invoice_number }}</td>
                                <td>{{ $order->post_code }}</td> <!-- Assuming PO No is stored as post_code -->
                                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                <td>{{ $order->account->first()->dua_date ?? 'N/A' }}</td>
                                <td>  <a href="#" class="send_invoice btn btn-soft-primary btn-icon btn-circle btn-sm" order-id="{{$order->id}}"  email="{{$order->customer_details->accountPayable->first()->confirmation_email}}" onclick="sendStatement(this)"   title="Download Excel-sheet">
                                    <i class="las la-mail-bulk"></i>
                                </a>
                            </td>
                            </tr>
                            
                        @endforeach
                    </tbody>
                </table>

                <div class="clearfix float-right mt-4">
                    <table class="table">
                        <tbody>
                             <tr>
                                <td>
                                    <strong class="text-muted">{{ translate(' Total Amount') }} :</strong>
                                </td>
                                <td>
                                    
                                    {{ $total_amount}}
                                </td>
                            </tr>  
                          
                           
                           
                        <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Paid Amount') }} :</strong>
                                </td>
                                <td class="text-muted h5">
                                    {{ $paid_amount }}
                                </td>
                            </tr>  
                        </tbody>
                    </table>
                   
              
    
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

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        //        function change_status() {
        //            var data = new FormData($('#order_form')[0]);
        //            $.ajax({
        //                headers: {
        //                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                },
        //                url: "{{ route('bulk-order-status') }}",
        //                type: 'POST',
        //                data: data,
        //                cache: false,
        //                contentType: false,
        //                processData: false,
        //                success: function (response) {
        //                    if(response == 1) {
        //                        location.reload();
        //                    }
        //                }
        //            });
        //        }

        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-order-delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }
        function sendStatement(element) {
        var order_id = element.getAttribute('order-id');
        var order_id = element.getAttribute('order-id');
        var email = element.getAttribute('email');
        // console.log(email);jh
        // return false
        fetch('{{ route('download-excel') }}', {
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
@endsection
