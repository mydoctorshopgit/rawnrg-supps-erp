@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">Partner {{ !empty($partner) ? 'Update' : 'Create'  }}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
          
            <form class="p-4" action="{{ !empty($partner) ? route('partners.update',$partner->id) : route('partners.store') }}" method="POST" enctype="multipart/form-data">
              
                @csrf

                @if (!empty($partner))
                    @method('PUT')
                @endif
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Name </label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="Name" id="name" name="name" value="{{  !empty($partner) ? $partner->name : ''  }}" class="form-control" required>
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="signinSrEmail">Image </label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="image" value="{{ !empty($partner) ? $partner->image : ''  }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        @error('image')
                           <p class="text-danger">{{ $message }}</p>
                       @enderror
                    </div>
                </div>
            
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
