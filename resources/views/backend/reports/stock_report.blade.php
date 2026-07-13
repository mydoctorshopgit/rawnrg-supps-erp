@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
       <h1 class="h3">{{translate('Product stock out report')}}</h1>
	</div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <!--card body-->
            <div class="card-body">
                <form action="{{ route('stock_report.index') }}" method="GET">
                    <div class="form-group row offset-lg-2">
                        <label class="col-md-2 col-form-label">{{translate('Select Date Range')}} :</label>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" class="aiz-date-range form-control"
                                name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                                data-separator=" to " data-advanced-range="true" autocomplete="off" required>                    
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                        </div>
                        @php
                            $date = request()->has('date') ? request()->input('date') : 'default';
                        @endphp
                    
                    <div class="col-md-2">
                        <a class="btn btn-primary" href="{{ url('admin/stock/pdf/' . urlencode($date)) }}">
                            {{ translate('PDF Export') }}
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-primary" href="{{ url('admin/stock/excel/' . urlencode($date)) }}">
                            {{ translate('Excel Export') }}
                        </a>
                    </div>
                    
                    
                    
                </form>
                <table class="table table-bordered aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Pack Qty</th>
                            <th>Sold Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $count = 0;
                        $counter = 1;
                        @endphp
                       @foreach ($products as $key => $product)
                                    @php
                                      $pqty = 0;
                                        foreach ($product->orderDetails->where('status','!=',10) as $key => $stock) {
                                            $pqty += $stock->quantity;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{$counter}}</td>
                                        <td>{{ $product->getTranslation('product_code') }}</td>
                                        <td>{{ $product->getTranslation('name') }}</td>
                                        <td>{{ round($product->pack_qty,2) }}</td>
                                        <td>{{ $pqty }}</td>
                                    </tr>
                                    @php
                                    $count += $pqty;
                                    $counter++;
                                    @endphp
                                @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total Stock</td>
                            <td>{{$count}}</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="aiz-pagination mt-4">
                    {{ $products->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //   function sendStatement(element) {
    //     var order_id = element.getAttribute('order-id');
    //     var order_id = element.getAttribute('order-id');
    //     // var email = element.getAttribute('email');
    //     // console.log(email);jh
    //     // return false
    //     fetch('', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': '{{ @csrf_token() }}'
    //         },
    //         body: JSON.stringify({
    //             order_id: order_id,
    //             order_id: order_id,
              
    //         })
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         // Assuming `AIZ.plugins.notify` is your custom notification plugin
    //         AIZ.plugins.notify('success', 'Statement sent Successfully');

    //         // window.location.href = "{{ route('inhouse_orders.index') }}";
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //     });
    // };

</script>
@endsection
