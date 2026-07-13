@extends('backend.layouts.app')

@section('content')



<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Category Information')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('categories.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Color')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Color')}}" id="color" name="color"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Lite Color')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Lite Color e.g. #f0f4ff')}}" id="lite_color" name="lite_color"
                                class="form-control">
                            <small class="text-muted">{{ translate('Will be applied to all child categories automatically') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Tagline')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Tagline')}}" id="tagline" name="tagline"
                                class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Short Description')}}</label>
                        <div class="col-md-9">
                            <textarea placeholder="{{translate('Short Description')}}" id="short_description" name="short_description"
                                class="form-control" rows="3">{{ old('short_description') }}</textarea>
                            <small class="text-muted">{{ translate('Brief description for category listings') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Overview')}}</label>
                        <div class="col-md-9">
                            <textarea class="aiz-text-editor" name="overview">{{ old('overview') }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Our Range')}}</label>
                        <div class="col-md-9">
                            <textarea class="aiz-text-editor" name="our_range">{{ old('our_range') }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Why Us')}}</label>
                        <div class="col-md-9">
                            <textarea class="aiz-text-editor" name="why_us">{{ old('why_us') }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('FAQs')}}</label>
                        <div class="col-md-9">
                            <div id="faqs-wrapper">
                                <div class="faq-row mb-2">
                                    <div class="input-group mb-2">
                                        <input type="text" name="faqs[0][question]" class="form-control" placeholder="{{ translate('Question') }}">
                                    </div>
                                    <div class="input-group">
                                        <textarea name="faqs[0][answer]" class="form-control" rows="3" placeholder="{{ translate('Answer') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="add-faq-btn">{{ translate('Add FAQ') }}</button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Description')}}</label>
                        <div class="col-md-9">
                            <textarea class="aiz-text-editor" name="content_description">{{ old('content_description') }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Type')}}</label>
                        <div class="col-md-9">
                            <select name="digital" onchange="categoriesByType(this.value)" required
                                class="form-control aiz-selectpicker mb-2 mb-md-0">
                                <option value="0">{{translate('Physical')}}</option>
                                <option value="1">{{translate('Digital')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Parent Category')}}</label>
                        <div class="col-md-9">
                            <select class="select2 form-control aiz-selectpicker" name="parent_id" data-toggle="select2"
                                data-placeholder="Choose ..." data-live-search="true">
                                @include('backend.product.categories.categories_option', ['categories' => $categories])
                                {{-- <option value="0">{{ translate('No Parent') }}</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @foreach ($category->childrenCategories as $childCategory)
                                @include('categories.child_category', ['child_category' => $childCategory])
                                @endforeach
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">
                            {{translate('Ordering Number')}}
                        </label>
                        <div class="col-md-9">
                            <input type="number" name="order_level" class="form-control" id="order_level"
                                placeholder="{{translate('Order Level')}}">
                            <small>{{translate('Higher number has high priority')}}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Banner')}} <small>({{
                                translate('200x200') }})</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{
                                        translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="banner" class="selected-files" data-image-alt="banner_alt" data-image-alt-type="multiple">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Icon')}} <small>({{
                                translate('32x32') }})</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{
                                        translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="icon" class="selected-files" data-image-alt="icon_alt" data-image-alt-type="multiple" >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Cover Image')}}
                            <small>({{ translate('360x360') }})</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{
                                        translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="cover_image" class="selected-files" data-image-alt="cover_image_alt" data-image-alt-type="multiple" >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Background Image')}}</label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{
                                        translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="background_image" class="selected-files" data-image-alt="banner_alt" data-image-alt-type="multiple">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Meta Title')}}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="meta_title"
                                placeholder="{{translate('Meta Title')}}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Meta Description')}}</label>
                        <div class="col-md-9">
                            <textarea name="meta_description" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    @if (get_setting('category_wise_commission') == 1)
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Commission Rate')}}</label>
                        <div class="col-md-9 input-group">
                            <input type="number" lang="en" min="0" step="0.01"
                                placeholder="{{translate('Commission Rate')}}" id="commision_rate" name="commision_rate"
                                class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Filtering Attributes')}}</label>
                        <div class="col-md-9">
                            <select class="select2 form-control aiz-selectpicker" name="filtering_attributes[]"
                                data-toggle="select2" data-placeholder="Choose ..." data-live-search="true" multiple>
                                @foreach (\App\Models\Attribute::all() as $attribute)
                                <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script type="text/javascript">
    function categoriesByType(val){
        $('select[name="parent_id"]').html('');
        AIZ.plugins.bootstrapSelect('refresh');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"POST",
            url:'{{ route('categories.categories-by-type') }}',
            data:{
               digital: val
            },
            success: function(data) {
                $('select[name="parent_id"]').html(data);
                AIZ.plugins.bootstrapSelect('refresh');
            }
        });
    }

    let faqIndex = 1;
    $('#add-faq-btn').on('click', function () {
        $('#faqs-wrapper').append(
            `<div class="faq-row mb-3">
                <div class="input-group mb-2">
                    <input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="{{ translate('Question') }}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-faq-btn">{{ translate('Remove') }}</button>
                    </div>
                </div>
                <div class="input-group">
                    <textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="3" placeholder="{{ translate('Answer') }}"></textarea>
                </div>
            </div>`
        );
        faqIndex++;
    });

    $(document).on('click', '.remove-faq-btn', function () {
        $(this).closest('.faq-row').remove();
    });
</script>

@endsection