@extends('backend.layouts.app')

@section('content')


<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Categories')}}</h1>
        </div>
        @can('add_product_category')
        <div class="col-md-6 text-md-right">
            <a href="{{ route('categories.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New category')}}</span>
            </a>
        </div>
        @endcan
    </div>
</div>
<div class="card">
    <div class="card-header d-block d-md-flex">
    <h5 class="mb-0 h6">{{ translate('Categories') }}</h5>

    <form id="sort_categories" action="" method="GET" class="d-flex flex-wrap gap-2 align-items-end">
        
        <!-- Search -->
        <div>
            <input type="text" class="form-control" name="search" 
                   value="{{ $sort_search ?? '' }}" 
                   placeholder="{{ translate('Type name & Enter') }}" style="min-width: 200px;">
        </div>

        <!-- Parent Category -->
        <div>
            <label class="form-label mb-1 small">{{ translate('Parent Category') }}</label>
            <select name="parent_id" id="parent_id" class="form-control" style="min-width: 200px;">
                <option value="">{{ translate('All') }}</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}" 
                            {{ old('parent_id', $parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Child Category -->
        <div id="child_div" style="{{ !empty($childCategories) ? '' : 'display:none;' }}">
            <label class="form-label mb-1 small">{{ translate('Sub Category') }}</label>
            <select name="child_id" id="child_id" class="form-control" style="min-width: 200px;">
                <option value="">{{ translate('All') }}</option>
                @foreach($childCategories as $child)
                    <option value="{{ $child->id }}" 
                            {{ old('child_id', $child_id) == $child->id ? 'selected' : '' }}>
                        {{ $child->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Grandchild Category -->
        <div id="grandchild_div" style="{{ !empty($grandchildCategories) ? '' : 'display:none;' }}">
            <label class="form-label mb-1 small">{{ translate('Sub-Sub Category') }}</label>
            <select name="grandchild_id" id="grandchild_id" class="form-control" style="min-width: 200px;">
                <option value="">{{ translate('All') }}</option>
                @foreach($grandchildCategories as $gc)
                    <option value="{{ $gc->id }}" 
                            {{ old('grandchild_id', $grandchild_id) == $gc->id ? 'selected' : '' }}>
                        {{ $gc->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="pt-1">
            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">{{ translate('Reset') }}</a>
        </div>
    </form>
</div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Color')}}</th>
                    <th>{{translate('Lite Color')}}</th>
                    <th>{{translate('Tagline')}}</th>
                    <th data-breakpoints="lg">{{ translate('Parent Category') }}</th>
                    <th data-breakpoints="lg">{{ translate('Order Level') }}</th>
                    <th data-breakpoints="lg">{{ translate('Level') }}</th>
                    <th data-breakpoints="lg">{{translate('Banner')}}</th>
                    <th data-breakpoints="lg">{{translate('Icon')}}</th>
                    <th data-breakpoints="lg">{{translate('Cover Image')}}</th>
                    <th data-breakpoints="lg">{{translate('Featured')}}</th>
                    <th data-breakpoints="lg">{{translate('Status')}}</th>
                    <th data-breakpoints="lg">Best Seller</th>
                    <th data-breakpoints="lg">{{translate('Save Big')}}</th>
                    <th data-breakpoints="lg">{{translate('Top Pick')}}</th>
                    <th data-breakpoints="lg">{{translate('Commission')}}</th>
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $key => $category)
                <tr>
                    <td>{{ ($key+1) + ($categories->currentPage() - 1)*$categories->perPage() }}</td>
                    <td class="d-flex align-items-center">
                        {{ $category->name }}
                        @if($category->digital == 1)
                        <img src="{{ static_asset('assets/img/digital_tag.png') }}" alt="{{translate('Digital')}}"
                            class="ml-2 h-25px" style="cursor: pointer;" title="DIgital">
                        @endif
                    </td>
                    <td>{{ $category->color }}</td>
                    <td>{{ $category->lite_color }}</td>
                    <td>{{ $category->tagline }}</td>

                    <td>
                        @php
                        $parent = \App\Models\Category::where('id', $category->parent_id)->first();
                        $parent_parent = null;
                        if($parent != null){
                        $parent_parent = \App\Models\Category::where('id', $parent->parent_id)->first();
                        }
                        @endphp
                        @if ($parent != null)
                        {{ $parent->name }}
                        @if ($parent_parent != null)
                        ({{ $parent_parent->name }})
                        @endif
                        @else
                        —
                        @endif
                    </td>
                    <td>{{ $category->order_level }}</td>
                    <td>{{ $category->level }}</td>
                    <td>
                        @if($category->banner != null)
                        <img src="{{ uploaded_asset($category->banner) }}" alt="{{translate('Banner')}}" class="h-50px">
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        @if($category->icon != null)
                        <span class="avatar avatar-square avatar-xs">
                            <img src="{{ uploaded_asset($category->icon) }}" alt="{{translate('icon')}}">
                        </span>
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        @if($category->cover_image != null)
                        <img src="{{ uploaded_asset($category->cover_image) }}" alt="{{translate('Cover Image')}}"
                            class="h-50px">
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" onchange="update_featured(this)" value="{{ $category->id }}" <?php
                                if($category->featured == 1) echo "checked";?>>
                            <span></span>
                        </label>
                    </td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" onchange="update_status(this)" value="{{ $category->id }}" <?php
                                if($category->status == 1) echo "checked";?>>
                            <span></span>
                        </label>
                    </td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" onchange="seller_status(this)" value="{{ $category->id }}" <?php
                                if($category->best_seller == 1) echo "checked";?>>
                            <span></span>
                        </label>
                    </td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" onchange="update_save_big(this)" value="{{ $category->id }}" <?php
                                if($category->save_big == 1) echo "checked";?>>
                            <span></span>
                        </label>
                    </td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" onchange="update_top_pick(this)" value="{{ $category->id }}" <?php
                                if($category->is_top_pick == 1) echo "checked";?>>
                            <span></span>
                        </label>
                    </td>
                    <td>{{ $category->commision_rate }} %</td>
                    <td class="text-right">
                        @can('edit_product_category')
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                            href="{{route('categories.edit', ['id'=>$category->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}"
                            title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        @endcan
                        @can('delete_product_category')
                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                            data-href="{{route('categories.destroy', $category->id)}}"
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
            {{ $categories->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection


@section('modal')
@include('modals.delete_modal')
@endsection


@section('script')
<script type="text/javascript">
    function update_featured(el){
            var status = el.checked ? 1 : 0;
            $.post('{{ route('categories.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured categories updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_status(el){
            var status = el.checked ? 1 : 0;
            $.post('{{ route('categories.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function seller_status(el){
            var status = el.checked ? 1 : 0;
            $.post('{{ route('seller.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Best Seller updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_save_big(el){
            var status = el.checked ? 1 : 0;
            $.ajax({
                url: '{{ route('categories.save_big') }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', id: el.value, status: status },
                success: function(data) {
                    if (data.success) {
                        AIZ.plugins.notify('success', '{{ translate('Save Big updated successfully') }}');
                    } else {
                        el.checked = !el.checked; // revert toggle
                        AIZ.plugins.notify('danger', data.message || '{{ translate('Something went wrong') }}');
                    }
                },
                error: function(xhr) {
                    el.checked = !el.checked; // revert toggle
                    var msg = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : '{{ translate('Something went wrong') }}';
                    AIZ.plugins.notify('danger', msg);
                }
            });
        }

        function update_top_pick(el){
            var status = el.checked ? 1 : 0;
            $.ajax({
                url: '{{ route('categories.top_pick') }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', id: el.value, status: status },
                success: function(data) {
                    if (data.success) {
                        AIZ.plugins.notify('success', '{{ translate('Top pick updated successfully') }}');
                    } else {
                        el.checked = !el.checked; // revert toggle
                        AIZ.plugins.notify('danger', data.message || '{{ translate('Something went wrong') }}');
                    }
                },
                error: function(xhr) {
                    el.checked = !el.checked; // revert toggle
                    var msg = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : '{{ translate('Something went wrong') }}';
                    AIZ.plugins.notify('danger', msg);
                }
            });
        }
</script>

<script type="text/javascript">
    $(document).ready(function() {

        function loadChildren(targetDivId, targetSelectId, parentId, selectedId = null) {
            if (!parentId) {
                $(targetDivId).hide();
                return;
            }

            $(targetSelectId).html('<option value="">Loading...</option>');
            $(targetDivId).show();

            $.ajax({
                url: "{{ url('/admin/categories/get-children') }}",
                type: "POST",
                data: { 
                    parent_id: parentId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    var options = '<option value="">All</option>';
                    $.each(data, function(key, value) {
                        var selected = (selectedId == value.id) ? 'selected' : '';
                        options += `<option value="${value.id}" ${selected}>${value.name}</option>`;
                    });
                    $(targetSelectId).html(options);
                },
                error: function(xhr) {
                    console.log('Error loading children:', xhr.responseText);
                    $(targetSelectId).html('<option value="">Failed to load</option>');
                }
            });
        }

        // When Parent changes
        $('#parent_id').on('change', function() {
            var parentId = $(this).val();
            var selectedChild = "{{ $child_id ?? '' }}";   // Preserve selected value

            loadChildren('#child_div', '#child_id', parentId, selectedChild);

            // Reset grandchild
            $('#grandchild_id').html('<option value="">Select Sub Category First</option>');
            $('#grandchild_div').hide();
        });

        // When Child changes
        $('#child_id').on('change', function() {
            var childId = $(this).val();
            var selectedGrand = "{{ $grandchild_id ?? '' }}";

            loadChildren('#grandchild_div', '#grandchild_id', childId, selectedGrand);
        });

        // On page load - Restore cascading dropdowns after filter
        @if($parent_id)
            // First load children of selected parent
            setTimeout(function() {
                loadChildren('#child_div', '#child_id', "{{ $parent_id }}", "{{ $child_id ?? '' }}");

                @if($child_id)
                    // Then load grandchildren if child is selected
                    setTimeout(function() {
                        loadChildren('#grandchild_div', '#grandchild_id', "{{ $child_id }}", "{{ $grandchild_id ?? '' }}");
                    }, 800);
                @endif
            }, 300);
        @endif

    });
</script>
@endsection