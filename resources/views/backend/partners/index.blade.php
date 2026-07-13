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
                <h1 class="h3">{{ translate('Partners List') }}</h1>
            </div>
            <div class="col text-right">
                <a href="{{ route('partners.create') }}" class="btn btn-circle btn-info">
                    <span>Add Partner</span>
                </a>
            </div>
        </div>
    </div>

    <br>

    <div class="card">
        <form id="sort_customers" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-0 h6">Partners</h5>
                </div>

            </div>
            <style>
                .switch {
                    position: relative;
                    display: inline-block;
                    width: 60px;
                    height: 34px;
                }

                .switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    -webkit-transition: .4s;
                    transition: .4s;
                }

                .slider:before {
                    position: absolute;
                    content: "";
                    height: 26px;
                    width: 26px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    -webkit-transition: .4s;
                    transition: .4s;
                }

                input:checked+.slider {
                    background-color: #2196F3;
                }

                input:focus+.slider {
                    box-shadow: 0 0 1px #2196F3;
                }

                input:checked+.slider:before {
                    -webkit-transform: translateX(26px);
                    -ms-transform: translateX(26px);
                    transform: translateX(26px);
                }

                /* Rounded sliders */
                .slider.round {
                    border-radius: 34px;
                }

                .slider.round:before {
                    border-radius: 50%;
                }
            </style>
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
                            <th>Title</th>
                            <th>Image</th>
                            <th>Active</th>


                            <th class="text-right">{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($partners as $partner)
                            <tr>
                                <td>
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="selected_ids[]" class="check-one"
                                            value="{{ $partner->id }}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </td>
                                <td>{{ $partner->name }}</td>
                                <td>
                                    @if ($partner->image != null)
                                        <span class="avatar avatar-square avatar-xs">
                                            <img src="{{ uploaded_asset($partner->image) }}" alt="{{ translate('image') }}">
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" value="{{ $partner->id }}" onchange="change_status(this)" {{ $partner->status == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>

                                </td>

                                <td class="text-right">
                                    @can('login_as_customer')
                                        <a href="{{ route('partners.edit', $partner->id) }}"
                                            class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            title="{{ translate('Edit this Partner') }}">
                                            <i class="las la-edit"></i>
                                        </a>


                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                            data-href="{{ route('partners.destroy', $partner->id) }}"
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
                    {{ $partners->appends(request()->input())->links() }}

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


    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
            });
        @endif
    </script>
@endsection
