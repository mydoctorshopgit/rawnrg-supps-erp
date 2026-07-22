@extends('backend.layouts.app')
@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">Trending Banner — Create</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <form class="p-4" action="{{ route('topPickBannar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @php $bannars = new \App\Models\Bannars(); @endphp
                @include('backend.bannars._partials.form_fields')
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
function toggleBannerFields() {
    var type = document.getElementById('banner_type').value;
    document.getElementById('simple_fields').style.display  = type === 'simple'  ? '' : 'none';
    document.getElementById('product_fields').style.display = type === 'product' ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleBannerFields);
</script>
@endsection
