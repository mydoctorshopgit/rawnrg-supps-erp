@extends('backend.layouts.app')

@section('content')


      <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><strong>Customer Product Assign</strong></h5>
        </div>
        <div class="card-body">
            <form class="form-horizontal" action="{{ route('customer_product_bulk_upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                 <div class="form-group row" id="brand">
                    <div class="col-xxl-9">
                    <label class="col-from-label fs-13">Company Name</label>
                        <select class="form-control aiz-selectpicker" name="customer_detail_id" id="customer_id" data-live-search="true" required>
                            <option value="">Select Customers</option>
                            @foreach ($all_customers as $customer)
                            {{-- @if(isset($customer->contactInformation)) --}}
                            <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                            {{-- @endif --}}
                            @endforeach
                        </select>
                       
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <div class="custom-file">
                            <label class="custom-file-label">
                                <input type="file" name="bulk_file" class="custom-file-input" required>
                                <span class="custom-file-name">{{ translate('Choose File')}}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-info">{{translate('Upload CSV')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
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
                     
                        <th data-breakpoints="lg">Company Name</th>
                        <th data-breakpoints="lg">Product code</th>
                        <th data-breakpoints="lg">Product Name</th>
                        <th data-breakpoints="lg">Brand Name</th>
                        <th data-breakpoints="lg">NHSSC</th>
                        <th data-breakpoints="lg">Pack Qty</th>
                        <th data-breakpoints="lg">Unit Price</th>
                        <th data-breakpoints="lg">Pack Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer_products as  $product)
                                <td>{{$product->id }}</td>
                                <td>{{$product->customer_detail->company_name}}</td>
                                <td>{{$product->product_code }}</td>
                                <td>{{$product->products->name }}</td>
                                 <td>{{$product->brand_name }}</td>
                                <td>{{$product->nhssc_npc}}</td>
                                <td>{{$product->pack_qty}}</td>
                                <td>{{$product->unit_price}}</td>
                                <td>{{$product->pack_price}}</td>
                                <td class="text-right">
                                
                                    @can('delete_customer')
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('customers.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $customer_products->links() }}
            </div>
        </div>
@endsection
