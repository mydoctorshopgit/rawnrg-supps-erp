@extends('backend.layouts.app')

@section('content')
<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6"> Shipment Confirmation</h5>
            </div>

            @can('delete_order')
            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{ translate('Bulk Action') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item confirm-alert" href="javascript:void(0)" data-target="#bulk-delete-modal">
                        {{ translate('Delete selection') }}</a>
                </div>
            </div>
            @endcan

            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                    <option value="">{{ translate('Filter by Delivery Status') }}</option>
                    <option value="pending" @if ($delivery_status=='pending' ) selected @endif>{{ translate('Pending')
                        }}
                    </option>
                    <option value="confirmed" @if ($delivery_status=='confirmed' ) selected @endif>
                        {{ translate('Confirmed') }}</option>
                    <option value="picked_up" @if ($delivery_status=='picked_up' ) selected @endif>
                        {{ translate('Picked Up') }}</option>
                    <option value="on_the_way" @if ($delivery_status=='on_the_way' ) selected @endif>
                        {{ translate('On The Way') }}</option>
                    <option value="delivered" @if ($delivery_status=='delivered' ) selected @endif>
                        {{ translate('Delivered') }}</option>
                    <option value="cancelled" @if ($delivery_status=='cancelled' ) selected @endif>
                        {{ translate('Cancel') }}</option>
                </select>
            </div>
            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status">
                    <option value="">{{ translate('Filter by Payment Status') }}</option>
                    <option value="paid" @isset($payment_status) @if ($payment_status=='paid' ) selected @endif
                        @endisset>
                        {{ translate('Paid') }}</option>
                    <option value="unpaid" @isset($payment_status) @if ($payment_status=='unpaid' ) selected @endif
                        @endisset>
                        {{ translate('Unpaid') }}</option>
                </select>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date"
                        placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to "
                        data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search)
                        value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type Order code & hit Enter') }}">
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
                        <!--<th>#</th>-->
                        @if (auth()->user()->can('delete_order'))
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
                        @else
                        <th data-breakpoints="lg">#</th>
                        @endif

                        <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                        <th data-breakpoints="md">{{ translate('Company Name') }}</th>

                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <!--<th data-breakpoints="md">{{ translate('Delivery Name') }}</th> -->
                        <th data-breakpoints="md">{{ translate('PostCode') }}</th>

                        <!--<th data-breakpoints="md">{{ translate('PO Number') }}</th>-->
                        <th data-breakpoints="md">{{ translate('PO Id') }}</th>
                        {{-- <th data-breakpoints="md">{{ translate('Delivery Name') }}</th> --}}
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <!--<th data-breakpoints="md">{{ translate('Customer Account') }}</th> -->
                        {{-- <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment method') }}</th>

                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th> --}}
                        <th data-breakpoints="md">{{ translate('Transaction Id') }}</th>
                        <th data-breakpoints="md">{{ translate('Charge Id') }}</th>
                        {{-- @if (addon_is_activated('refund_request')) --}}
                        <th>{{ translate('Payment Status') }}</th>
                        <th>{{ translate('Order Date') }}</th>
                        {{-- @endif --}}
                        <th class="text-right" width="15%">{{ translate('options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $postcodeCounts = [];

                    foreach ($orders as $order) {
                    $shipping = json_decode($order->shipping_address, true);
                    $postcode = $shipping['postal_code'] ?? null;

                    if ($postcode) {
                    if (isset($postcodeCounts[$postcode])) {
                    $postcodeCounts[$postcode]++;
                    } else {
                    $postcodeCounts[$postcode] = 1;
                    }
                    }
                    }

                    // Initialize variables
                    $postcodeColors = [];
                    $colors = [
                    '#FFCCCC', '#CCFFCC', '#CCCCFF', '#FFFFCC', '#FFCCFF', '#FFD700', '#00FA9A', '#FF69B4', '#7B68EE',
                    '#40E0D0',
                    '#FF4500', '#8A2BE2', '#20B2AA', '#DC143C', '#98FB98', '#1E90FF', '#FF6347', '#8B008B', '#00CED1',
                    '#FF8C00',
                    '#00FF7F', '#4682B4', '#DA70D6', '#ADFF2F', '#5F9EA0', '#4B0082', '#7FFF00', '#BA55D3', '#008080',
                    '#D2691E'
                    ];
                    $colorIndex = 0;
                    @endphp


                    @foreach ($orders as $key => $order)
                    @php
                    $shipping = json_decode($order->shipping_address, true);
                    $postcode = $shipping['postal_code'] ?? null;
                    $bgColor = "#fff";

                    if ($postcode && $postcodeCounts[$postcode] > 1) {
                    if (!isset($postcodeColors[$postcode])) {
                    $postcodeColors[$postcode] = $colors[$colorIndex % count($colors)];
                    $colorIndex++;
                    }
                    $bgColor = $postcodeColors[$postcode];
                    }
                    @endphp

                    <tr>
                        @if (auth()->user()->can('delete_order'))
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{ $order->id }}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        @else
                        <td>{{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                        @endif

                        <td>
                            {{ count($order->orderDetails) }}
                        </td>

                        <td>
                            @if ($order->company_name != null || $order->company_name != '')
                            {{ $order->company_name ?? 'N/A' }}
                            @else
                            N/A
                            @endif
                        </td>
                       <td>
                            @if ($order->customer_details != null)
                            {{ $order->customer_details->company_name }}

                            @elseif ($order->User) {{-- Use elseif instead of nested else-if for clarity --}}
                            {{ $order->User->name }} {{ $order->User->last_name }} <br>

                            @if ($order->User->user_type == 'customer_credit')
                            <span class="badge-warning">Credit / <b>AC</b></span>
                            @elseif ($order->User->user_type == 'customer')
                            <span class="badge-primary">Register / <b>AC</b></span>
                            @elseif ($order->User->user_type == 'customer_guest')
                            <span class="badge-secondary">Guest / <b>AC</b></span>
                            @elseif ($order->User->user_type == 'customer_pharmaceuti')
                            <span class="badge-info">Pharmaceutical / <b>AC</b></span>
                            @endif

                            @else
                            {{ $shipping['name'] ?? $shipping['email'] ?? 'Guest' }} <br>
                            <span class="badge-secondary">Guest / <b>AC</b></span>
                            @endif

                        </td>
                        <!-- <td>-->
                        <!--    {{ $order->delivery_name }}-->
                        <!--</td>-->
                        <td><span style="background-color: {{ $bgColor }}; padding: 2px">{{ $shipping['postal_code']
                                ??'N/A' }}</span></td>


                        <!--<td>-->
                        <!--    {{ $order->purchase_order_number }}-->
                        <!--</td>-->
                        <td>
                            {{ $order->code }}
                        </td>

                        <td>
                            {{ single_price($order->grand_total) }}
                        </td>
                        @php
                        $paymentDetails = [];
                        if ($order->payment_details && is_string($order->payment_details) &&
                        str_starts_with($order->payment_details, '{')) {
                        $paymentDetails = json_decode($order->payment_details, true);
                        }
                        @endphp

                        <td>
                            @if(isset($paymentDetails['transactionId']) && isset($paymentDetails['chargeId']))
                            {{ $paymentDetails['transactionId'] ?? 'N/A' }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td>
                            @if(isset($paymentDetails['transactionId']) && isset($paymentDetails['chargeId']))
                            {{ $paymentDetails['chargeId'] ?? 'N/A' }}
                            @else
                            N/A
                            @endif
                        </td>




                        <!--              <td>-->
                        <!--    @if ($order->User)-->
                        <!--        {{ $order->User->name }} {{ $order->User->last_name }}<br>-->

                        <!--        @if ($order->User->user_type == 'customer_credit')-->
                        <!--            <span class="badge-success ">Credit</span>-->
                        <!--        @elseif ($order->User->user_type == 'customer')-->
                        <!--            <span class="badge-success ">Register</span>-->
                        <!--        @endif-->
                        <!--    @else-->
                        <!--        <span class="">User Not Found</span>-->
                        <!--    @endif-->
                        <!--</td>-->

                        {{-- <td>
                            {{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}
                        </td>
                        <td>
                            {{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}
                        </td>
                        <td>
                            @if ($order->payment_status == 'paid')
                            <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                            @else
                            <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                            @endif
                        </td> --}}
                        {{-- @if (addon_is_activated('refund_request')) --}}
                       <td>
                            @php
                            $payment_details_parsed = json_decode($order->payment_details);
                            @endphp
                            @if ($order->User)
                            @if ($order->user->user_type == 'customer_credit')
                            <span class="">{{ translate('Credit') }}</span>
                            @else
                            @if($order->payment_details == 'cash' || $order->payment_details == null ||
                            !isset($payment_details_parsed->chargeId))
                            <span class="">{{ translate('UnComplete') }}</span>

                            @elseif(isset($payment_details_parsed->chargeId) &&
                            isset($payment_details_parsed->transactionId))
                            <span class="">{{ translate('Complete') }}</span>
                            @endif

                            @endif
                            @endif


                        </td>
<td>{{ date('d-m-Y H:i A', strtotime($order->created_at)) }}</td>
                        <td class="text-right">
                            @if (addon_is_activated('pos_system') && $order->order_from == 'pos')
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                href="{{ route('admin.invoice.thermal_printer', $order->id) }}" target="_blank"
                                title="{{ translate('Thermal Printer') }}">
                                <i class="las la-print"></i>
                            </a>
                            @endif
                            @can('view_order_details')
                            @php
                            $order_detail_route = route('orders.show', encrypt($order->id));
                            if (Route::currentRouteName() == 'seller_orders.index') {
                            $order_detail_route = route('seller_orders.show', encrypt($order->id));
                            } elseif (Route::currentRouteName() == 'pick_up_point.index') {
                            $order_detail_route = route('pick_up_point.order_show', encrypt($order->id));
                            }
                            if (Route::currentRouteName() == 'inhouse_orders.index') {
                            $order_detail_route = route('inhouse_orders.show', encrypt($order->id));
                            }
                            @endphp
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{ route('shipment_orders.show', $order->id) }}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @endcan
                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                href="{{ route('invoice.download', $order->id) }}"
                                title="{{ translate('Download Invoice') }}">
                                <i class="las la-download"></i>
                            </a>
                            @can('delete_order')
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                data-href="{{ route('orders.destroy', $order->id) }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
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
</script>
@endsection