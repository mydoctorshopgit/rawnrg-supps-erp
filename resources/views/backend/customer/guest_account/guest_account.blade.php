@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{ translate('List Guest Customers') }}</h1>
        </div>
        {{-- <div class="col text-right">
            <a href="{{ route('credit_customer') }}" class="btn btn-circle btn-info">
                <span>{{ translate('Add Account') }}</span>
            </a>
        </div> --}}
    </div>
</div>

<br>
 
<div class="card">
    <form class="" id="sort_customers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('Guest Customers')}}</h5>
            </div>
            
            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal">{{translate('Delete selection')}}</a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type email or name & Enter') }}">
                </div>
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
        
        input:checked + .slider {
          background-color: #2196F3;
        }
        
        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }
        
        input:checked + .slider:before {
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
                     
                        <th data-breakpoints="lg">{{translate(' Customer Name')}}</th>
                        <th data-breakpoints="lg">{{translate('Email')}}</th>
                        <th data-breakpoints="lg">{{translate('Mobile No')}}</th>
                        <th class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user as  $user)
                           
                    <td>{{$user->id }}</td>
               
                    <td>{{$user->name}} {{$user->last_name}}</td>
                      <td>{{ $user->email }}<br>
                           @if ($user->is_verified == '1')
                            <span class="badge-success p-1 ">Verified</span>
                            @elseif ($user->is_verified == '0')
                            <span class="badge-warning p-1 ">Pending</span>
                            @endif
                        </td>
                    <td>{{$user->mobile_number ?? 'N/A'}}</td>

                   
                    <td class="text-right">
                         @can('login_as_customer')
                            <a  href="{{route('guest_customer', $user->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit this Customer') }}">
                                <i class="las la-edit"></i>
                            </a>
                        @endcan
                        @can('login_as_customer')
                     
                        <a   href="{{route('customer_guest.view', $user->id)}}" class="btn btn-soft-success btn-icon btn-circle btn-sm"  title="{{ translate('View') }}">
                            <i class="las la-eye"></i>
                        </a>
                    @endcan
                         @can('login_as_customer')
                     
                        {{-- <a href="{{ route('customer_guest_delete', $user->id) }}" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="{{ translate('Delete') }}">
                            <i class="las la-trash"></i>
                        </a> --}}

                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                data-href="{{ route('customer_guest_delete', $user->id) }}"
                                title="{{ translate('Trash') }}">
                                <i class="las la-trash"></i>
                            </a>
                    @endcan
                    
                    
           
                    </td>
                </tr>
        @endforeach

                </tbody>
            </table>
            <div class="aiz-pagination">
                {{-- {{ $customers->appends(request()->input())->links() }} --}}
            </div>
        </div>
    </form>
</div>

<!-- Modal for Checkbox Action -->
<!-- Modal for Checkbox Action -->
{{-- <div class="modal fade" id="customModal" tabindex="-1" aria-hidden="true">
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
                    <textarea class="form-control" id="commentInput" rows="3" placeholder="Enter Comment" name="comment"></textarea>
                    
                     
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button  type="button" class="btn btn-danger" id="rejectButton" style="margin-right:10px;">Reject</button>
                    <button type="button" class="btn btn-success" id="approvedButton">Approved</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div> --}}



@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')
@endsection

@section('script')
<script>
    $(document).on("change", ".check-all", function() {
        $(".check-one").prop("checked", this.checked);
    });

    function confirm_ban(url) {
        $('#confirm-ban').modal('show', { backdrop: 'static' });
        document.getElementById('confirmation').setAttribute('href', url);
    }

    function confirm_unban(url) {
        $('#confirm-unban').modal('show', { backdrop: 'static' });
        document.getElementById('confirmationunban').setAttribute('href', url);
    }

    function bulk_delete() {
        var selectedIds = [];
        $(".check-one:checked").each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-customer-delete') }}",
                type: 'POST',
                data: { ids: selectedIds },
                success: function (response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        } else {
            alert("Please select at least one customer.");
        }
    }

    function showCustomModal(checkbox) {
        if (checkbox.checked) {
            let userId = checkbox.getAttribute("data-user-id");
            document.getElementById("userIdInput").value = userId;
            $("#customModal").modal("show");
        }
    }

    $(document).ready(function () {
        $(".check-all").change(function() {
            $(".check-one").prop("checked", this.checked);
        });

        $("#customModal").on("hidden.bs.modal", function () {
            $(".showModalCheckbox").prop("checked", false);
        });
    });
document.getElementById('rejectButton').addEventListener('click', function() {


const formData = new FormData(document.getElementById('AccountApproved'));


// Perform AJAX request to send data to the server
fetch('{{ route('rejectAccount') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                'content'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {



        Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        window.location.href = "{{ route('customer_credit.list') }}?status=success";
                    })




    })
    .catch(error => {
        console.error('Error:', error);
    });
});
document.getElementById('approvedButton').addEventListener('click', function() {


const formData = new FormData(document.getElementById('AccountApproved'));


// Perform AJAX request to send data to the server
fetch('{{ route('approvedAccount') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                'content'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {



        Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        window.location.href = "{{ route('customer_credit.list') }}?status=success";
                    })
       




    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
@endsection
