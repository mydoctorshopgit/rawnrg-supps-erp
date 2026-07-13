@extends('backend.layouts.app')

@section('content')

@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({ title: 'Success!', text: "{{ session('success') }}", icon: 'success', confirmButtonText: 'OK' });
    });
</script>
@endif
@if (session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({ title: 'Error!', text: "{{ session('error') }}", icon: 'error', confirmButtonText: 'Try Again' });
    });
</script>
@endif

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{ translate('Hero Banners') }}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('heroBannar.create') }}" class="btn btn-circle btn-info">
                <span>Add Banner</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">Banners</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Title / Product</th>
                    <th>Image</th>
                    <th>Active</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bannars as $bannar)
                <tr>
                    <td>{{ $bannar->id }}</td>
                    <td>
                        @if(($bannar->banner_type ?? 'simple') === 'product')
                            <span class="badge badge-info">Product</span>
                        @else
                            <span class="badge badge-secondary">Simple</span>
                        @endif
                    </td>
                    <td>
                        @if(($bannar->banner_type ?? 'simple') === 'product')
                            <strong>{{ $bannar->product_title }}</strong><br>
                            <small class="text-muted">SKU: {{ $bannar->sku }}</small>
                        @else
                            {{ $bannar->title }}
                        @endif
                    </td>
                    <td>
                        @if($bannar->image)
                            <span class="avatar avatar-square avatar-xs">
                                <img src="{{ uploaded_asset($bannar->image) }}" alt="banner image">
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox"
                                   data-user-id="{{ $bannar->id }}"
                                   onchange="updateStatus(this)"
                                   {{ $bannar->status == 1 ? 'checked' : '' }}>
                            <span></span>
                        </label>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('heroBannar.edit', $bannar->id) }}"
                           class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                           title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a href="{{ route('heroBannar.delete', $bannar->id) }}"
                           class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                           title="{{ translate('Delete') }}"
                           onclick="return confirm('Delete this banner?')">
                            <i class="las la-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('modal')
@include('modals.delete_modal')
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateStatus(el) {
    var id     = el.getAttribute('data-user-id');
    var status = el.checked ? 1 : 0;

    fetch("{{ route('heroStatus.update') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id, status: status })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
            el.checked = !el.checked; // revert
        }
    })
    .catch(() => { el.checked = !el.checked; });
}
</script>
@endsection
