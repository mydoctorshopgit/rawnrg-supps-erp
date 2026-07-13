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
            <h1 class="h3">{{ translate('List Middle Bannars') }}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('middleBannar.create') }}" class="btn btn-circle btn-info">
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

            <!-- <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{ translate('Bulk Action') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item confirm-alert" href="javascript:void(0)" data-target="#bulk-delete-modal">{{
                        translate('Delete selection') }}</a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search)
                        value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type email or name & Enter') }}">
                </div>
            </div> -->
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
                        <th>Tittle</th>
                        <th>Image</th>
                        <th>Active</th>
                       
                      
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bannars as $bannar)
                    <tr>
                        <td>
                            <label class="aiz-checkbox">
                                <input type="checkbox" name="selected_ids[]" class="check-one" value="{{ $bannar->id }}">
                                <span class="aiz-square-check"></span>
                            </label>
                        </td>
                        <td>{{ $bannar->title }}</td>
                        <td>
                        @if($bannar->image != null)
                        <span class="avatar avatar-square avatar-xs">
                            <img src="{{ uploaded_asset($bannar->image) }}" alt="{{translate('image')}}">
                        </span>
                        @else
                        —
                        @endif
                    </td>
                       <td>
                            <label class="switch">
                                <input type="checkbox" data-user-id="{{ $bannar->id }}" onchange="showCustomModal(this)"
                                    {{ $bannar->status == 2 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>

                        </td>
                        <!-- <td>{{ $bannar->description }}</td> -->
                        <td class="text-right">
                            @can('login_as_customer')
                            <a href="{{ route('middleBannar.edit', $bannar->id) }}"
                                class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                title="{{ translate('Edit this Customer') }}">
                                <i class="las la-edit"></i>
                            </a>
                         
                             <a href="{{ route('middleBannar.delete', $bannar->id) }}"
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

<!-- Modal for Checkbox Action -->
<!-- Modal for Checkbox Action -->
<div class="modal fade" id="customModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-3">
            <div class="modal-body">
                <form id=AccountApproved>
                    <input type="hidden" id="userIdInput" name="user_id" value="">

                    <div class="mb-3">
                        <label for="urlInput" class="form-label">URL</label>
                        <input type="text" class="form-control" id="urlInput" name="url" placeholder="Enter URL">
                    </div>
                    <div class="mb-3">
                        <label for="commentInput" class="form-label">Comment</label>
                        <textarea class="form-control" id="commentInput" rows="3" placeholder="Enter Comment"
                            name="comment"></textarea>

                        {{-- <input type="text" name="comment" id=""> --}}
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-danger" id="rejectButton"
                            style="margin-right:10px;">Reject</button>
                        <button type="button" class="btn btn-success" id="approvedButton">Approved</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
@section('modal')
<!-- Delete modal -->
@include('modals.delete_modal')
<!-- Bulk Delete modal -->
@include('modals.bulk_delete_modal')
@endsection
@section('script')
<script>
function showCustomModal(el) {

    let id = el.getAttribute('data-user-id');
    let status = el.checked ? 2 : 0; 

    fetch("{{ route('middleStatus.update') }}", {
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
        if(data.success){
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
@section('scripts')
{{-- SweetAlert2 success/error flash alerts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
           Swal.fire({
               icon: 'success',
               title: 'Success',
               text: '{{ session("success") }}',
               confirmButtonColor: '#3085d6',
           });
       @endif

       @if(session('error'))
           Swal.fire({
               icon: 'error',
               title: 'Error',
               text: '{{ session("error") }}',
               confirmButtonColor: '#d33',
           });
       @endif
</script>
@endsection