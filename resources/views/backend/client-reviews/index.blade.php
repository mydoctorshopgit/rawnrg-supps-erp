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

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="h3">{{ translate('Client Reviews List') }}</h1>
            </div>
            <div class="col text-right">
                <a href="{{ route('client-reviews.create') }}" class="btn btn-circle btn-info">
                    <span>Add Review</span>
                </a>
            </div>
        </div>
    </div>

    <br>

    <div class="card">
        <form id="sort_customers" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-0 h6">Client Reviews</h5>
                </div>

            </div>
          
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-group">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Image</th>
                            <th>Rating</th>
                           
                            <th class="text-right">{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviews as $review)
                            <tr>
                                <td>
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="selected_ids[]" class="check-one"
                                            value="{{ $review->id }}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </td>
                                <td>{{ $review->name }}</td>
                                <td>{{ $review->role }}</td>
                                <td>
                                    @if ($review->image != null)
                                        <span class="avatar avatar-square avatar-xs">
                                            <img src="{{ uploaded_asset($review->image) }}" alt="{{ translate('image') }}">
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $review->rating }}</td>
                               
                                <td class="text-right">
                                    @can('login_as_customer')
                                        <a href="{{ route('client-reviews.edit', $review->id) }}"
                                            class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            title="{{ translate('Edit this Partner') }}">
                                            <i class="las la-edit"></i>
                                        </a>


                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                            data-href="{{ route('client-reviews.destroy', $review->id) }}"
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
                    {{ $reviews->appends(request()->input())->links() }}

                </div>
            </div>
        </form>
    </div>

    <!-- Modal for Checkbox Action -->
@endsection
@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')
@endsection

@section('script')
    {{-- SweetAlert2 success/error flash alerts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <script type="text/javascript">
        function change_status(el){
            var status = 0;
            if(el.checked){
                var status = 1;
            }
            $.post("{{ route('partners.change-status') }}", {_token:"{{ csrf_token() }}", id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', "{{ translate('Change blog status successfully') }}");
                }
                else{
                    AIZ.plugins.notify('danger', "{{ translate('Something went wrong') }}");
                }
            });
        }
    </script>


@endsection
