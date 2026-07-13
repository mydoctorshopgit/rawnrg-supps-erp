@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">Client {{ !empty($review) ? 'Update' : 'Create'  }}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
          
            <form class="p-4" action="{{ !empty($review) ? route('client-reviews.update',$review->id) : route('client-reviews.store') }}" method="POST" enctype="multipart/form-data">
              
                @csrf

                @if (!empty($review))
                    @method('PUT')
                @endif
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Name </label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="Name" id="name" name="name" value="{{  !empty($review) ? $review->name : ''  }}" class="form-control" required>
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">Role </label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="Role" id="name" name="role" value="{{  !empty($review) ? $review->role : ''  }}" class="form-control" required>
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="signinSrEmail">Profile Image </label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="image" value="{{ !empty($review) ? $review->image : ''  }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        @error('image')
                           <p class="text-danger">{{ $message }}</p>
                       @enderror
                    </div>
                </div>

                <div class="form-group row" id="category">
                    <label class="col-md-3 col-from-label">
                        {{translate('Rating Star')}} 
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-9">
                        <select class="form-control aiz-selectpicker" name="rating" id="rating" data-live-search="true" required>
                          
                            @foreach ([1,2,3,4,5] as $value)
                            <option {{ !empty($review) && $review->rating == $value ? 'selected' : '' }} value="{{ $value }}">
                                {{ $value }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                 <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        {{translate('Comment')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-9">
                        <textarea name="comment" rows="5" placeholder="Comment..." class="form-control" required="">{{ !empty($review) ? $review->comment : '' }}</textarea>
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
