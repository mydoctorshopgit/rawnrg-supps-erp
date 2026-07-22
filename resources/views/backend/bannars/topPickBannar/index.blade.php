@extends('backend.layouts.app')
@section('content')
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
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="h3">{{ translate('List Trending Bannars') }}</h1>
            </div>
            <div class="col text-right">
                <a href="{{ route('topPickBannar.create') }}" class="btn btn-circle btn-info">
                    <span>Add Bannar</span>
                </a>
            </div>
        </div>
    </div>

    <br>

    <div class="card">
        <form id="sort_customers" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-0 h6">Bannars</h5>
                </div>
            </div>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>Tittle</th>
                            <th>Image</th>
                            <th>Active</th>
                            <th class="text-right">{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bannars as $bannar)
                            <tr>
                                <td>{{ $bannar->title }}</td>
                                <td>
                                    @if ($bannar->image != null)
                                        <span class="avatar avatar-square avatar-xs">
                                            <img src="{{ uploaded_asset($bannar->image) }}" alt="{{ translate('image') }}">
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" data-user-id="{{ $bannar->id }}" onchange="showCustomModal(this)"
                                            {{ $bannar->status == 7 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>

                                <td class="text-right">
                                    @can('login_as_customer')
                                        <a href="{{ route('topPickBannar.edit', $bannar->id) }}"
                                            class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            title="{{ translate('Edit this Customer') }}">
                                            <i class="las la-edit"></i>
                                        </a>

                                        <a href="{{ route('topPickBannar.delete', $bannar->id) }}"
                                            class="btn btn-soft-danger btn-icon btn-circle btn-sm"
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
                    {{-- {{ $users->appends(request()->input())->links() }} --}}
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
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
    <script>
        function showCustomModal(el) {

            let id = el.getAttribute('data-user-id');
            let status = el.checked ? 6 : 0;

            fetch("{{ route('topPickStatus.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        id: id,
                        status: status
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log("Status updated");
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        }
    </script>
@endsection
