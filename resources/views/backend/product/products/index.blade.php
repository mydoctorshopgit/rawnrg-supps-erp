@extends('backend.layouts.app')

@section('content')


<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All products')}}</h1>
        </div>
        @if($type != 'Seller' && auth()->user()->can('add_new_product'))
        <div class="col text-right">
            <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Product')}}</span>
            </a>
        </div>
        @endif
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
            </div>

            @can('product_delete')
            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item confirm-alert" href="javascript:void(0)" data-target="#bulk-delete-modal">
                        {{translate('Delete selection')}}</a>
                </div>
            </div>
            @endcan

            @if($type == 'Seller')
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id"
                    onchange="sort_products()">
                    <option value="">{{ translate('All Sellers') }}</option>
                    @foreach (($sellers ?? collect()) as $seller)
                    <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>
                        {{ optional($seller->shop)->name }} ({{ $seller->name }})
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($type == 'All' && get_setting('vendor_system_activation') == 1)
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id"
                    onchange="sort_products()">
                    <option value="">{{ translate('All Sellers') }}</option>
                    @foreach (($sellers ?? collect()) as $seller)
                    <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>{{ $seller->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="type" id="type"
                    onchange="sort_products()">
                    <option value="">{{ translate('Sort By') }}</option>
                    <option value="rating,desc" @isset($col_name , $query) @if($col_name=='rating' && $query=='desc' )
                        selected @endif @endisset>{{translate('Rating (High > Low)')}}</option>
                    <option value="rating,asc" @isset($col_name , $query) @if($col_name=='rating' && $query=='asc' )
                        selected @endif @endisset>{{translate('Rating (Low > High)')}}</option>
                    <option value="num_of_sale,desc" @isset($col_name , $query) @if($col_name=='num_of_sale' &&
                        $query=='desc' ) selected @endif @endisset>{{translate('Num of Sale (High > Low)')}}</option>
                    <option value="num_of_sale,asc" @isset($col_name , $query) @if($col_name=='num_of_sale' &&
                        $query=='asc' ) selected @endif @endisset>{{translate('Num of Sale (Low > High)')}}</option>
                    <option value="unit_price,desc" @isset($col_name , $query) @if($col_name=='unit_price' &&
                        $query=='desc' ) selected @endif @endisset>{{translate('Base Price (High > Low)')}}</option>
                    <option value="unit_price,asc" @isset($col_name , $query) @if($col_name=='unit_price' &&
                        $query=='asc' ) selected @endif @endisset>{{translate('Base Price (Low > High)')}}</option>
                </select>
            </div>

            <div class="col-md-5 ml-auto">
                <form method="GET" action="{{ route('products.all') }}">

                    <select class="form-control aiz-selectpicker" name="category_id" data-live-search="true"
                        onchange="this.form.submit()">

                        <option value="">Select Category</option>

                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id')==$category->id ? 'selected' : ''
                            }}>

                            {{ $category->name }}
                            {{ $category->best_seller == 1 ? '(best seller)' : '' }}
                        </option>
                        @endforeach

                    </select>

                </form>
            </div>

            <div class="col-md-2">
                                            <form method="GET" action="{{ route('products.all') }}">

                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"
                        @isset($sort_search) value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type & Enter') }}">
                </div>
                                            <form method="GET" action="{{ route('products.all') }}">

            </div>

        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        @if(auth()->user()->can('product_delete'))
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
                        <th>{{translate('Name')}}</th>
                        @if($type == 'Seller' || $type == 'All')
                        <th data-breakpoints="lg">{{translate('Edit By')}}</th>
                        @endif
                        <th data-breakpoints="sm">Category</th>
                        <th data-breakpoints="sm">{{translate('Info')}}</th>
                        <th data-breakpoints="md">{{translate('Total Stock')}}</th>
                        <th data-breakpoints="lg">{{translate('Todays Deal')}}</th>
                        <th data-breakpoints="lg">{{translate('Published')}}</th>
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                        <th data-breakpoints="lg">{{translate('Approved')}}</th>
                        @endif
                        <th data-breakpoints="lg">{{translate('Featured')}}</th>
                        <th data-breakpoints="lg">{{translate('Trending')}}</th>
                        <th data-breakpoints="lg">{{translate('Monthly Deal')}}</th>
                        <th data-breakpoints="lg">{{translate('Top Pick')}}</th>
                        <!-- <th data-breakpoints="lg">{{translate('pharma')}}</th> -->
                        <th data-breakpoints="lg">Best seller</th>
                        <!-- <th data-breakpoints="lg">{{translate('Save Big')}}</th> -->
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $key => $product)
                    <tr>
                        @if(auth()->user()->can('product_delete'))
                        <td>
                            <div class="form-group d-inline-block">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one" name="id[]" value="{{$product->id}}">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </td>
                        @else
                        <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        @endif
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image"
                                        class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $product->name }}</span><br>
                                    <strong>SKU: </strong> {{ $product->product_code }}
                                </div>
                            </div>
                        </td>
                        <!--@if($type == 'Seller' || $type == 'All')-->
                        <!--    <td>{{ optional($product->user)->name }}</td>-->
                        <!--@endif-->
                        <td>{{ $product->edit_by ?? "admin" }}</td>
                        <td>{{ $product->main_category->name ?? "" }}</td>

                        <td>
                            <strong>{{translate('Num of Sale')}}:</strong> {{ $product->num_of_sale }}
                            {{translate('times')}} </br>
                            <strong>{{translate('Base Price')}}:</strong> {{ single_price($product->unit_price) }} </br>
                            <strong>{{translate('Rating')}}:</strong> {{ $product->rating }} </br>
                        </td>
                        <td>
                            @if($product->digital == 1)
                            <span class="badge badge-inline badge-info">{{ translate('Digital Product') }}</span>
                            @else
                            @php
                            $qty = 0;
                            if($product->variant_product) {
                            foreach ($product->stocks as $key => $stock) {
                            $qty += $stock->qty;
                            echo $stock->variant.' - '.$stock->qty.'<br>';
                            }
                            }
                            else {
                            //$qty = $product->current_stock;
                            $qty = optional($product->stocks->first())->qty;
                            echo $qty;
                            }
                            @endphp
                            @if($qty <= $product->low_stock_quantity)
                                <span class="badge badge-inline badge-danger">{{ translate('Low') }}</span>
                                @endif
                                @endif

                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox"
                                    <?php if ($product->todays_deal == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox"
                                    <?php if ($product->published == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_approved(this)" value="{{ $product->id }}" type="checkbox" <?php
                                    if ($product->approved == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @endif
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input class="js-featured-toggle" value="{{ $product->id }}" type="checkbox" <?php
                                    if ($product->featured == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input class="js-trending-toggle" value="{{ $product->id }}" type="checkbox"
                                    {{ $product->is_trending == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input class="js-monthly-deal-toggle" value="{{ $product->id }}" type="checkbox"
                                    {{ $product->monthly_deal == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input class="js-top-pick-toggle" value="{{ $product->id }}" type="checkbox"
                                    {{ $product->is_top_pick == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <!-- <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_pharma(this)" value="{{ $product->id }}" type="checkbox" <?php
                                    if ($product->pharmaceutical_product == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td> -->
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" class="seller-toggle" value="{{ $product->id }}" {{
                                    $product->best_seller == 1 ? 'checked' : '' }}>
                                <span></span>
                            </label>
                        </td>
                        <!-- <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_save_big(this)" value="{{ $product->id }}" type="checkbox" <?php
                                    if ($product->save_big == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td> -->
                        <td class="text-right">
                            <!--<a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{ route('product', $product->slug) }}" target="_blank" title="{{ translate('View') }}">-->
                            <!--    <i class="las la-eye"></i>-->
                            <!--</a>-->
                            @can('product_edit')
                            @if ($type == 'Seller')
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{route('products.seller.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}"
                                title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @else
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{route('products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}"
                                title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @endif
                            @endcan
                            <!-- @can('product_duplicate')
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                href="{{route('products.duplicate', ['id'=>$product->id, 'type'=>$type]  )}}"
                                title="{{ translate('Duplicate') }}">
                                <i class="las la-copy"></i>
                            </a>
                            @endcan -->
                            @can('product_delete')
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                data-href="{{route('products.destroy', $product->id)}}"
                                title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
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

    $(document).on("change", ".check-all", function () {
        if (this.checked) {
            // Iterate each checkbox
            $('.check-one:checkbox').each(function () {
                this.checked = true;
            });
        } else {
            $('.check-one:checkbox').each(function () {
                this.checked = false;
            });
        }

    });

  
    function update_todays_deal(el) {
        if (el.checked) {
            var status = 1;
        }
        else {
            var status = 0;
        }
        $.post('{{ route('products.todays_deal') }}', { _token: '{{ csrf_token() }}', id: el.value, status: status }, function (data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
            }
            else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }
    function (el) {
        if (el.checked) {
            var status = 1;
        }
        else {
            var status = 0;
        }
        $.post('{{ route('products.todays_deal') }}', { _token: '{{ csrf_token() }}', id: el.value, status: status }, function (data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
            }
            else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }

    function update_published(el) {
        if (el.checked) {
            var status = 1;
        }
        else {
            var status = 0;
        }
        $.post('{{ route('products.published') }}', { _token: '{{ csrf_token() }}', id: el.value, status: status }, function (data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
            }
            else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }

    function update_approved(el) {
        if (el.checked) {
            var approved = 1;
        }
        else {
            var approved = 0;
        }
        $.post('{{ route('products.approved') }}', {
            _token: '{{ csrf_token() }}',
            id: el.value,
            approved: approved
        }, function (data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate('Product approval update successfully') }}');
            }
            else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }

    function update_pharma(el) {
        if (el.checked) {
            var status = 1;
        }
        else {
            var status = 0;
        }
        $.post('{{ route('products.pharma') }}', { _token: '{{ csrf_token() }}', id: el.value, status: status }, function (data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate('pharma products updated successfully') }}');
            }
            else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }

    function sort_products(el) {
        $('#sort_products').submit();
    }

    function bulk_delete() {
        var data = new FormData($('#sort_products')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('bulk-product-delete')}}",
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response == 1) {
                    location.reload();
                }
            }
        });
    }

</script>
<script>
    function seller_status(el) {

        var status = el.checked ? 1 : 0;

        $.post('{{ route('product_seller.status') }}', {
            _token: '{{ csrf_token() }}',
            id: el.value,
            status: status
        }, function (res) {

            if (res.status == 1) {
                AIZ.plugins.notify('success', res.message);
            } else {
                AIZ.plugins.notify('danger', res.message);
                el.checked = !el.checked;
            }

        });
    }

    $(document).on('change', '.seller-toggle', function () {
        seller_status(this);
    });

    function update_save_big(el) {
        var status = el.checked ? 1 : 0;
        $.post('{{ route('products.update_save_big') }}', { _token: '{{ csrf_token() }}', id: el.value, status: status }, function (data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate('Save Big updated successfully') }}');
            } else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }
</script>

<script>
      $(document).ready(function () {
        //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');

        // Delegated listener — replaces inline onchange="update_featured(this)"
        $(document).on('change', '.js-featured-toggle', function () {
            var status = this.checked ? 1 : 0;
            var el = this;
            $.post('{{ route('products.featured') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        });

        $(document).on('change', '.js-trending-toggle', function () {
            console.log('working')
            var status = this.checked ? 1 : 0;
            var el = this;
            $.post('{{ route('products.update_trending') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Trending products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        });

        $(document).on('change', '.js-monthly-deal-toggle', function () {
            var status = this.checked ? 1 : 0;
            var el = this;
            $.post('{{ route('products.update_monthly_deals') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Monthly Deals products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        });

        $(document).on('change', '.js-top-pick-toggle', function () {
            var status = this.checked ? 1 : 0;
            var el = this;
            $.post('{{ route('products.update_top_pick') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Top pick products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        });
    });
</script>
@endsection