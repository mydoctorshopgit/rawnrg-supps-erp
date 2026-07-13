@extends('backend.layouts.app')

@section('content')
    <div class="page-content">
        <div class="aiz-titlebar text-left mt-2 pb-2 px-3 px-md-2rem border-bottom border-gray">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3">{{ translate('Edit Product') }}</h1>
                </div>
                {{-- <div class="col text-right">
                <a class="btn has-transition btn-xs p-0 hov-svg-danger" href="{{ route('home') }}" 
                    target="_blank" data-toggle="tooltip" data-placement="top" data-title="{{ translate('View Tutorial Video') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19.887" height="16" viewBox="0 0 19.887 16">
                        <path id="_42fbab5a39cb8436403668a76e5a774b" data-name="42fbab5a39cb8436403668a76e5a774b" d="M18.723,8H5.5A3.333,3.333,0,0,0,2.17,11.333v9.333A3.333,3.333,0,0,0,5.5,24h13.22a3.333,3.333,0,0,0,3.333-3.333V11.333A3.333,3.333,0,0,0,18.723,8Zm-3.04,8.88-5.47,2.933a1,1,0,0,1-1.473-.88V13.067a1,1,0,0,1,1.473-.88l5.47,2.933a1,1,0,0,1,0,1.76Zm-5.61-3.257L14.5,16l-4.43,2.377Z" transform="translate(-2.17 -8)" fill="#9da3ae"/>
                    </svg>
                </a>
            </div> --}}
            </div>
        </div>

        <div class="d-sm-flex">
            <!-- page side nav -->
            <div class="page-side-nav c-scrollbar-light px-3 py-2">
                <ul class="nav nav-tabs flex-sm-column border-0" role="tablist" aria-orientation="vertical">
                    <!-- General -->
                    <li class="nav-item">
                        <a class="nav-link" id="general-tab" href="#general" data-toggle="tab" data-target="#general"
                            type="button" role="tab" aria-controls="general" aria-selected="true">
                            {{ translate('General') }}
                        </a>
                    </li>
                    <!-- Files & Media -->
                    <li class="nav-item">
                        <a class="nav-link" id="files-and-media-tab" href="#files_and_media" data-toggle="tab"
                            data-target="#files_and_media" type="button" role="tab" aria-controls="files_and_media"
                            aria-selected="false">
                            {{ translate('Files & Media') }}
                        </a>
                    </li>
                    <!-- Price & Stock -->
                    <li class="nav-item">
                        <a class="nav-link" id="price-and-stocks-tab" href="#price_and_stocks" data-toggle="tab"
                            data-target="#price_and_stocks" type="button" role="tab" aria-controls="price_and_stocks"
                            aria-selected="false">
                            {{ translate('Price & Stock') }}
                        </a>
                    </li>
                    <!-- SEO -->
                    <li class="nav-item">
                        <a class="nav-link" id="seo-tab" href="#seo" data-toggle="tab" data-target="#seo"
                            type="button" role="tab" aria-controls="seo" aria-selected="false">
                            {{ translate('SEO') }}
                        </a>
                    </li>
                    <!-- Shipping -->
                    <li class="nav-item">
                        <a class="nav-link" id="shipping-tab" href="#shipping" data-toggle="tab" data-target="#shipping"
                            type="button" role="tab" aria-controls="shipping" aria-selected="false">
                            {{ translate('Shipping') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- tab content -->
            <div class="flex-grow-1 p-sm-3 p-lg-2rem mb-2rem mb-md-0">
                <!-- Error Meassages -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
                     id="choice_form">
                    @csrf
                    <input name="_method" type="hidden" value="POST">
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <input type="hidden" name="tab" id="tab">

                    <ul class="nav nav-tabs nav-fill border-light language-bar">
                        @foreach (get_all_active_language() as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3"
                                    href="{{ route('products.admin.edit', ['id' => $product->id, 'lang' => $language->code]) }}">
                                    <img src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                                        height="11" class="mr-1">
                                    <span>{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">
                        <!-- General -->
                        <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <!-- Product Information -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Product Information') }}</h5>
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col-xxl-7 col-xl-6">
                                            <!-- Product Name -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-xxl-3 col-from-label fs-13">{{ translate('Product Name') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-xxl-9">
                                                    <input type="text" class="form-control" name="name" id="product_name"
                                                        placeholder="{{ translate('Product Name') }}"
                                                        value="{{ $product->name }}">
                                                                                                             <small class="text-info text-bold">Note: The product slug will be automatically generated based on the product name. You can review or edit it if needed.</small>

                                                </div>
                                            </div>
                                            <!-- Brand -->
                                            <div class="form-group row" id="brand">
                                                <label
                                                    class="col-xxl-3 col-from-label fs-13">{{ translate('Brand') }}</label>
                                                <div class="col-xxl-9">
                                                    <select class="form-control aiz-selectpicker" name="brand_id"
                                                        id="brand_id" data-live-search="true">
                                                        <option value="">{{ translate('Select Brand') }}</option>
                                                        @foreach (\App\Models\Brand::all() as $brand)
                                                            <option value="{{ $brand->id }}"
                                                                @if ($product->brand_id == $brand->id) selected @endif>
                                                                {{ $brand->getTranslation('name') }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small
                                                        class="text-muted">{{ translate("You can choose a brand if you'd like to display your product by brand.") }}</small>
                                                </div>
                                            </div>
                                            <!-- Unit -->
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">{{ translate('Unit') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-xxl-9">
                                                    <input type="text" class="form-control" name="unit"
                                                        placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}"
                                                        value="{{ $product->unit }}">
                                                </div>
                                            </div>
                                            <!-- Weight -->
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">{{ translate('Weight') }}
                                                    <small>({{ translate('In Kg') }})</small></label>
                                                <div class="col-xxl-9">
                                                    <input type="number" class="form-control" name="weight"
                                                        value="{{ $product->weight }}" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">Pack Quantity
                                                    </label>
                                                <div class="col-xxl-9">
                                                    <input type="text" class="form-control" name="pack_qty"
                                                        value="{{ $product->pack_qty }}" 
                                                        >
                                                </div>
                                            </div>
                                                                                    <div class="form-group row">
                                            <label class="col-xxl-3 col-from-label fs-13">Product Code <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-xxl-9">
                                                <input type="text" placeholder="{{ translate('product code') }}"
                                                    name="product_code" class="form-control"
                                                    value="{{ $product->product_code }}">
                                            </div>
                                        </div>
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">Unit Price
                                                    </label>
                                                <div class="col-xxl-9">
                                                    <input type="number" class="form-control" name="unit_price"
                                                        value="{{ $product->unit_price }}" 
                                                        >
                                                </div>
                                            </div>
                                            <!-- Quantity -->
                                            <div class="form-group row">
                                                <label
                                                    class="col-xxl-3 col-from-label fs-13">{{ translate('Minimum Purchase Qty') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-xxl-9">
                                                    <input type="number" lang="en" class="form-control"
                                                        name="min_qty"
                                                        value="{{  $product->min_qty }}"
                                                        min="1">
                                                    <small
                                                        class="text-muted">{{ translate('The minimum quantity needs to be purchased by your customer.') }}</small>
                                                </div>
                                            </div>
                                            <!-- Tags -->
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">{{ translate('Tags') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-xxl-9">
                                                    <input type="text" class="form-control aiz-tag-input"
                                                        name="tags[]" id="tags" value="{{ $product->tags }}"
                                                        placeholder="{{ translate('Type to add a tag') }}"
                                                        data-role="tagsinput">
                                                    <small
                                                        class="text-muted">{{ translate('This is used for search. Input those words by which cutomer can find this product.') }}</small>
                                                </div>
                                            </div>

                                            @if (addon_is_activated('pos_system'))
                                                <!-- Barcode -->
                                                <div class="form-group row">
                                                    <label
                                                        class="col-xxl-3 col-from-label fs-13">{{ translate('Barcode') }}</label>
                                                    <div class="col-xxl-9">
                                                        <input type="text" class="form-control" name="barcode"
                                                            placeholder="{{ translate('Barcode') }}"
                                                            value="{{ $product->barcode }}">
                                                    </div>
                                                </div>
                                            @endif

                                            @if (addon_is_activated('refund_request'))
                                                <!-- refund_request -->
                                                <div class="form-group row mt-4 mb-4">
                                                    <label
                                                        class="col-xxl-3 col-from-label fs-13">{{ translate('Refundable') }}</label>
                                                    <div class="col-xxl-9">
                                                        <label class="aiz-switch aiz-switch-success mb-0">
                                                            <input type="checkbox" name="refundable"
                                                                @if ($product->refundable == 1) checked @endif
                                                                value="1">
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                         @php
                                    $old_categories = $product
                                    ->categories()
                                    ->pluck('category_id')
                                    ->toArray();
                                    @endphp
                                    <div class="col-xxl-5 col-xl-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 h6">{{ translate('Product Category') }}</h5>
                                                <h6 class="float-right fs-13 mb-0">
                                                    {{ translate('Select Main') }}
                                                    <span class="position-relative main-category-info-icon">
                                                        <i class="las la-question-circle fs-18 text-info"></i>
                                                        <span
                                                            class="main-category-info bg-soft-info p-2 position-absolute d-none border">{{
                                                            translate('This will be used for commission based
                                                            calculations and homepage category wise product Show.')
                                                            }}</span>
                                                    </span>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="h-300px overflow-auto c-scrollbar-light">
                                                    <ul class="category-tree list-unstyled">
                                        @foreach ($categories as $category)
                                            <li>
                                                <input type="checkbox" name="category_ids[]"
                                                       value="{{ $category->id }}"
                                                       class="category-checkbox"
                                                       {{ in_array($category->id, $old_categories) ? 'checked' : '' }}>
                                    
                                                <span class="category-toggle" data-toggle="category-{{ $category->id }}">
                                                    {{ $category->getTranslation('name') }}
                                                </span>
                                    
                                                <ul id="category-{{ $category->id }}" class="child-categories d-none ms-4">
                                                    @foreach ($category->childrenCategories as $child)
                                                        <li>
                                                            <input type="checkbox" name="category_ids[]"
                                                                   value="{{ $child->id }}"
                                                                   class="category-checkbox"
                                                                   {{ in_array($child->id, $old_categories) ? 'checked' : '' }}>
                                    
                                                            <span class="category-toggle" data-toggle="category-{{ $child->id }}">
                                                                {{ $child->getTranslation('name') }}
                                                            </span>
                                    
                                                            <ul id="category-{{ $child->id }}" class="child-categories d-none ms-4">
                                                                @foreach ($child->childrenCategories as $third)
                                                                    <li>
                                                                        <input type="checkbox" name="category_ids[]"
                                                                               value="{{ $third->id }}"
                                                                               class="category-checkbox"
                                                                               {{ in_array($third->id, $old_categories) ? 'checked' : '' }}>
                                    
                                                                        {{ $third->getTranslation('name') }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            // Toggle subcategories on click
                                            document.querySelectorAll('.category-toggle').forEach(function (toggle) {
                                                toggle.addEventListener('click', function () {
                                                    const targetId = toggle.getAttribute('data-toggle');
                                                    const target = document.getElementById(targetId);
                                                    if (target) {
                                                        target.classList.toggle('d-none');
                                                    }
                                                });
                                            });
                                    
                                            // Auto-expand any parent UL if a checkbox inside is already checked
                                            document.querySelectorAll('.category-checkbox:checked').forEach(function (checkedBox) {
                                                let parent = checkedBox.closest('ul.child-categories');
                                                while (parent) {
                                                    parent.classList.remove('d-none'); // Show the category level
                                                    // Move up to next parent UL if exists
                                                    parent = parent.parentElement.closest('ul.child-categories');
                                                }
                                            });
                                    
                                            // (Optional) Log checked category IDs
                                            document.querySelectorAll('.category-checkbox').forEach(function (checkbox) {
                                                checkbox.addEventListener('change', function () {
                                                    if (checkbox.checked) {
                                                        console.log('Checked category ID:', checkbox.value);
                                                    } else {
                                                        console.log('Unchecked category ID:', checkbox.value);
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label class="fs-13">{{ translate('Short Description') }}</label>
                                        <div class="">
                                            <textarea class="aiz-text-editor" name="short_description">{{ $product->short_description }}</textarea>
                                        </div>
                                    </div>
                                
                                    <!-- Description -->
                                    <div class="form-group">
                                        <label class="fs-13">{{ translate('Description') }}</label>
                                        <div class="">
                                            <textarea class="aiz-text-editor" name="description">{{ $product->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="fs-13">{{ translate('Information') }}</label>
                                        <div class="">
                                            <textarea class="aiz-text-editor" name="information">{{ $product->information }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <h5 class="mb-3 mt-5 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Status') }}</h5>
                                <div class="w-100">
                                    <!-- Featured -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Featured') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0 d-block">
                                                <input type="checkbox" name="featured" value="1"
                                                    @if ($product->featured == 1) checked @endif>
                                                <span></span>
                                            </label>
                                            <small
                                                class="text-muted">{{ translate('If you enable this, this product will be granted as a featured product.') }}</small>
                                        </div>
                                    </div>
                                    <!-- Todays Deal -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Todays Deal') }}</label>
                                        <div class="col-md-9">
                                            <label class="aiz-switch aiz-switch-success mb-0 d-block">
                                                <input type="checkbox" name="todays_deal" value="1"
                                                    @if ($product->todays_deal == 1) checked @endif>
                                                <span></span>
                                            </label>
                                            <small
                                                class="text-muted">{{ translate('If you enable this, this product will be granted as a todays deal product.') }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Flash Deal -->
                                <h5 class="mb-3 mt-4 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Flash Deal') }}
                                    <small
                                        class="text-muted">({{ translate('If you want to select this product as a flash deal, you can use it') }})</small>
                                </h5>
                                <div class="w-100">
                                    <!-- Add To Flash -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Add To Flash') }}</label>
                                        <div class="col-xxl-9">
                                            <select class="form-control aiz-selectpicker" name="flash_deal_id"
                                                id="video_provider">
                                                <option value="">{{ translate('Choose Flash Title') }}</option>
                                                @foreach (\App\Models\FlashDeal::where('status', 1)->get() as $flash_deal)
                                                    <option value="{{ $flash_deal->id }}"
                                                        @if ($product->flash_deal_product && $product->flash_deal_product->flash_deal_id == $flash_deal->id) selected @endif>
                                                        {{ $flash_deal->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Discount -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Discount') }}</label>
                                        <div class="col-xxl-9">
                                            <input type="number" name="flash_discount" value="{{ $product->discount }}"
                                                min="0" step="0.01" class="form-control">
                                        </div>
                                    </div>
                                    <!-- Discount Type -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-from-label">{{ translate('Discount Type') }}</label>
                                        <div class="col-xxl-9">
                                            <select class="form-control aiz-selectpicker" name="flash_discount_type"
                                                id="">
                                                <option value="">{{ translate('Choose Discount Type') }}</option>
                                                <option value="amount" @if ($product->discount_type == 'amount') selected @endif>
                                                    {{ translate('Flat') }}
                                                </option>
                                                <option value="percent" @if ($product->discount_type == 'percent') selected @endif>
                                                    {{ translate('Percent') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vat & TAX -->
                                <h5 class="mb-3 mt-4 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Vat & TAX') }}</h5>
                                <div class="w-100">
                                    @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                                        <label for="name">
                                            {{ $tax->name }}
                                            <input type="hidden" value="{{ $tax->id }}" name="tax_id[]">
                                        </label>

                                        @php
                                            $tax_amount = 0;
                                            $tax_type = '';
                                            foreach ($tax->product_taxes as $row) {
                                                if ($product->id == $row->product_id) {
                                                    $tax_amount = $row->tax;
                                                    $tax_type = $row->tax_type;
                                                }
                                            }
                                        @endphp

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <input type="number" lang="en" min="0"
                                                    value="{{ $tax_amount }}" step="0.01"
                                                    placeholder="{{ translate('Tax') }}" name="tax[]"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <select class="form-control aiz-selectpicker" name="tax_type[]">
                                                    <option value="amount"
                                                        @if ($tax_type == 'amount') selected @endif>
                                                        {{ translate('Flat') }}
                                                    </option>
                                                    <option value="percent"
                                                        @if ($tax_type == 'percent') selected @endif>
                                                        {{ translate('Percent') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Files & Media -->
                        <div class="tab-pane fade" id="files_and_media" role="tabpanel"
                            aria-labelledby="files-and-media-tab">
                            <div class="bg-white p-3 p-sm-2rem">
                                <!-- Product Files & Media -->
                                <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                    {{ translate('Product Files & Media') }}</h5>
                                <div class="w-100">
                                    <!-- Gallery Images -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                            for="signinSrEmail">{{ translate('Gallery Images') }}
                                            <small>(600x600)</small></label>
                                        <div class="col-md-9">
                                            <div class="input-group" data-toggle="aizuploader" data-type="image"
                                                data-multiple="true">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                        {{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                <input type="hidden" name="photos" value="{{ $product->photos }}"
                                                    class="selected-files" data-image-alt="gallery_alt" data-image-alt-type="multiple" data-alt-value="{{ $product->gallery_alt ?? "" }}">
                                            </div>
                                            <div class="file-preview box sm">
                                            </div>
                                            <small
                                                class="text-muted">{{ translate('These images are visible in product details page gallery. Use 600x600 sizes images.') }}</small>
                                        </div>
                                    </div>
                                    <!-- Thumbnail Image -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label"
                                            for="signinSrEmail">{{ translate('Thumbnail Image') }}
                                            <small>(300x300)</small></label>
                                        <div class="col-md-9">
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                        {{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                <input type="hidden" name="thumbnail_img"
                                                    value="{{ $product->thumbnail_img }}" class="selected-files" data-image-alt="thumbnail_alt" data-image-alt-type="multiple" data-alt-value="{{ !empty($product->thumbnail_alt) ? $product->thumbnail_alt : '[]' }}">
                                            </div>
                                            <div class="file-preview box sm">
                                            </div>
                                            <small
                                                class="text-muted">{{ translate('This image is visible in all product box. Use 300x300 sizes image. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <!-- Video Provider -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Video Provider') }}</label>
                                    <div class="col-md-9">
                                        <select class="form-control aiz-selectpicker" name="video_provider"
                                            id="video_provider">
                                            <option value="youtube" <?php if ($product->video_provider == 'youtube') {
                                                echo 'selected';
                                            } ?>>{{ translate('Youtube') }}
                                            </option>
                                            <option value="dailymotion" <?php if ($product->video_provider == 'dailymotion') {
                                                echo 'selected';
                                            } ?>>{{ translate('Dailymotion') }}
                                            </option>
                                            <option value="vimeo" <?php if ($product->video_provider == 'vimeo') {
                                                echo 'selected';
                                            } ?>>{{ translate('Vimeo') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Video Link -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Video Link') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="video_link"
                                            value="{{ $product->video_link }}"
                                            placeholder="{{ translate('Video Link') }}">
                                        <small
                                            class="text-muted">{{ translate("Use proper link without extra parameter. Don't use short share link/embeded iframe code.") }}</small>
                                    </div>
                                </div>
                                <!-- PDF Specification -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label"
                                        for="signinSrEmail">{{ translate('PDF Specification') }}</label>
                                    <div class="col-md-9">
                                        <div class="input-group" data-toggle="aizuploader" data-type="document">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                    {{ translate('Browse') }}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="pdf" value="{{ $product->pdf }}"
                                                class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Price & Stock -->
                     <div class="tab-pane fade" id="price_and_stocks" role="tabpanel"
                        aria-labelledby="price-and-stocks-tab">
                        <div class="bg-white p-3 p-sm-2rem">
                            <!-- Tab Title -->
                            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                {{ translate('Product price & stock') }}
                            </h5>
                            <!-- Add New Stock Button -->
                            <div class="form-group row mb-4">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" onclick="addNewStock()">Add New
                                        Stock</button>
                                </div>
                            </div>
                            <div id="stocks-container">

                                @php
                                    // Priority: use flashed full input (includes newly added rows) if validation failed
                                    // Fallback: original DB records
                                    $stocksToShow = old('stocks', null) !== null
                                        ? old('stocks')                     // ← this is array from request
                                        : $product_stocks->map(fn($s, $i) => [
                                            'id'                => $s->id,
                                            'deleted'           => 0,
                                            'photos'            => $s->photos,
                                            'thumbnail_img'     => $s->thumbnail_img,
                                            'thumbnail_alt'     => $s->thumbnail_alt,
                                            'gallery_alt'       => $s->gallery_alt,
                                            'pip_code'          => $s->pip_code,
                                            'sku'               => $s->sku,
                                            'variant'           => $s->variant,
                                            'flavour'           => $s->flavour ?? '',
                                            'color'             => $s->color ?? '',
                                            'qty'               => $s->qty,
                                            'pack_qty'          => $s->pack_qty ?? '',
                                            'price'             => $s->price,
                                            'description'       => $s->description,
                                            'short_description' => $s->short_description,
                                            'features'          => $s->features ?? '',
                                        ])->values()->toArray();

                                    // Keep track of next index for JS addNewStock()
                                    $nextIndex = count($stocksToShow);
                                @endphp

                            @foreach ($stocksToShow as $index => $stock)
                            @php
                                $isDeleted = old("stocks.$index.deleted", $stock['deleted'] ?? 0) == '1';
                            @endphp
                                    <div class="stock-entry" data-index="{{ $index }}" style="{{ $isDeleted ? 'display:none' : '' }}">
                                        <input type="hidden" name="stocks[{{ $index }}][id]" value="{{ old("stocks.$index.id", $stock['id'] ?? '') }}">
                                        <input type="hidden" name="stocks[{{ $index }}][deleted]" value="{{ $isDeleted ? '1' : '0' }}" class="delete-flag">

                                        <div class="w-100">

                                            <!-- Gallery Images -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">{{ translate('Gallery Images') }} <small>(600x600)</small></label>
                                                <div class="col-md-9">
                                                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                        </div>
                                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                        <input type="hidden"
                                                            name="stocks[{{ $index }}][photos]"
                                                            value="{{ old("stocks.$index.photos") !== null ? (is_array(old("stocks.$index.photos")) ? json_encode(old("stocks.$index.photos")) : old("stocks.$index.photos")) : (isset($stock['photos']) ? json_encode($stock['photos']) : '') }}"
                                                            class="selected-files"
                                                            data-image-alt="stocks[{{ $index }}][gallery_alt]"
                                                            data-image-alt-type="multiple"
                                                            data-alt-value='{{ old("stocks.$index.gallery_alt") !== null ? json_encode(old("stocks.$index.gallery_alt")) : (isset($stock["gallery_alt"]) ? json_encode($stock["gallery_alt"]) : json_encode([])) }}'>
                                                    </div>
                                                    <div class="file-preview box sm"></div>
                                                    <small class="text-muted">{{ translate('These images are visible in product details page gallery. Use 600x600 sizes images.') }}</small>
                                                    @error("stocks.$index.photos")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Thumbnail Image -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label">{{ translate('Thumbnail Image') }} <small>(300x300)</small></label>
                                                <div class="col-md-9">
                                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                        </div>
                                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                        <input type="hidden" name="stocks[{{ $index }}][thumbnail_img]"
                                                            value="{{ old("stocks.$index.thumbnail_img", $stock['thumbnail_img'] ?? '') }}"
                                                            class="selected-files" data-image-alt="stocks[{{ $index }}][thumbnail_alt]" data-image-alt-type="multiple" data-alt-value='{{ old("stocks.$index.thumbnail_alt") !== null ? json_encode(old("stocks.$index.thumbnail_alt")) : (!empty($stock["thumbnail_alt"]) ? json_encode($stock["thumbnail_alt"]) : json_encode([])) }}'>
                                                    </div>
                                                    <div class="file-preview box sm"></div>
                                                    <small class="text-muted">{{ translate('This image is visible in all product box. Use 300x300 sizes image...') }}</small>
                                                    @error("stocks.$index.thumbnail_img")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Pip Code -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Pip Code') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-6">
                                                    <input type="text" name="stocks[{{ $index }}][pip_code]"
                                                        value="{{ old("stocks.$index.pip_code", $stock['pip_code'] ?? '') }}"
                                                        class="form-control" placeholder="{{ translate('pip code') }}">
                                                    @error("stocks.$index.pip_code")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Product Code (SKU) -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Product Code') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-6">
                                                    <input type="text" name="stocks[{{ $index }}][sku]"
                                                        value="{{ old("stocks.$index.sku", $stock['sku'] ?? '') }}"
                                                        class="form-control" placeholder="{{ translate('product code') }}">
                                                    @error("stocks.$index.sku")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Size / Variant -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Size') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-6">
                                                    <input type="text" name="stocks[{{ $index }}][variant]"
                                                        value="{{ old("stocks.$index.variant", $stock['variant'] ?? '') }}"
                                                        class="form-control" placeholder="{{ translate('Size') }}">
                                                    @error("stocks.$index.variant")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Flavour -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Flavour') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-6">
                                                    <input type="text" name="stocks[{{ $index }}][flavour]"
                                                        value="{{ old("stocks.$index.flavour", $stock['flavour'] ?? '') }}"
                                                        class="form-control" placeholder="{{ translate('flavour') }}">
                                                    @error("stocks.$index.flavour")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Color -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Color') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-6">
                                                    <input type="text" name="stocks[{{ $index }}][color]"
                                                        value="{{ old("stocks.$index.color", $stock['color'] ?? '') }}"
                                                        class="form-control" placeholder="{{ translate('color') }}">
                                                    @error("stocks.$index.color")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Quantity -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Quantity') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-6">
                                                    <input type="text" name="stocks[{{ $index }}][qty]"
                                                        value="{{ old("stocks.$index.qty", $stock['qty'] ?? '') }}"
                                                        class="form-control" placeholder="{{ translate('quantity') }}">
                                                    @error("stocks.$index.qty")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Price') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-6">
                                                    <input type="text" name="stocks[{{ $index }}][price]"
                                                        value="{{ old("stocks.$index.price", $stock['price'] ?? '') }}"
                                                        class="form-control" placeholder="{{ translate('price') }}">
                                                    @error("stocks.$index.price")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Pack Quantity -->
                                            <div class="form-group row">
                                                <label class="col-xxl-3 col-from-label fs-13">{{ translate('Pack Quantity') }}</label>
                                                <div class="col-xxl-9">
                                                    <input type="text" name="stocks[{{ $index }}][pack_qty]"
                                                        value="{{ old("stocks.$index.pack_qty", $stock['pack_qty'] ?? '') }}"
                                                        class="form-control">
                                                    @error("stocks.$index.pack_qty")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Description') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-12">
                                                    <textarea class="aiz-text-editor" name="stocks[{{ $index }}][description]">
                                            {{ old("stocks.$index.description", $stock['description'] ?? '') }}
                                                    </textarea>
                                                    @error("stocks.$index.description")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Short Description -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Short Description') }} <span class="text-danger">*</span></label>
                                                <div class="col-md-12">
                                                    <textarea class="aiz-text-editor" name="stocks[{{ $index }}][short_description]">
                                                    {{ old("stocks.$index.short_description", $stock['short_description'] ?? '') }}
                                                    </textarea>
                                                    @error("stocks.$index.short_description")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Features -->
                                            <div class="form-group row">
                                                <label class="col-md-3 col-from-label">{{ translate('Features') }}</label>
                                                <div class="col-md-12">
                                                    <textarea class="aiz-text-editor" name="stocks[{{ $index }}][features]">{{ old("stocks.$index.features", $stock['features'] ?? '') }}</textarea>
                                                    <small class="text-muted">{{ translate('Key features or highlights for this variant.') }}</small>
                                                    @error("stocks.$index.features")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Delete Button -->
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-danger" onclick="deleteStock(this)">Delete Stock</button>
                                                </div>
                                            </div>

                                            <hr>
                                        </div>
                                    </div>
                            @endforeach
                                            {{-- @foreach ($product_stocks as $index => $stock)
                                            <div class="stock-entry" data-index="{{ $index }}">
                                                <input type="hidden" name="stocks[{{ $index }}][id]" value="{{ $stock->id }}">
                                                <input type="hidden" name="stocks[{{ $index }}][deleted]" value="0"
                                                    class="delete-flag">
                                                <div class="w-100">
                                                    <!-- Gallery Images -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{
                                                            translate('Gallery Images') }}
                                                            <small>(600x600)</small></label>
                                                        <div class="col-md-9">
                                                            <div class="input-group" data-toggle="aizuploader" data-type="image"
                                                                data-multiple="true">
                                                                <div class="input-group-prepend">
                                                                    <div
                                                                        class="input-group-text bg-soft-secondary font-weight-medium">
                                                                        {{ translate('Browse') }}</div>
                                                                </div>
                                                                <div class="form-control file-amount">{{ translate('Choose File') }}
                                                                </div>
                                                                <input type="hidden" name="stocks[{{ $index }}][photos]"
                                                                    value="{{ $stock->photos }}" class="selected-files">
                                                            </div>
                                                            <div class="file-preview box sm"></div>
                                                            <small class="text-muted">{{ translate('These images are visible in
                                                                product details page gallery. Use 600x600 sizes images.') }}</small>
                                                        </div>
                                                    </div>
                                                    <!-- Thumbnail Image -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{
                                                            translate('Thumbnail Image') }}
                                                            <small>(300x300)</small></label>
                                                        <div class="col-md-9">
                                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                                <div class="input-group-prepend">
                                                                    <div
                                                                        class="input-group-text bg-soft-secondary font-weight-medium">
                                                                        {{ translate('Browse') }}</div>
                                                                </div>
                                                                <div class="form-control file-amount">{{ translate('Choose File') }}
                                                                </div>
                                                                <input type="hidden" name="stocks[{{ $index }}][thumbnail_img]"
                                                                    value="{{ $stock->thumbnail_img }}" class="selected-files">
                                                            </div>
                                                            <div class="file-preview box sm"></div>
                                                            <small class="text-muted">{{ translate('This image is visible in all
                                                                product box. Use 300x300 sizes image. Keep some blank space around
                                                                main object of your image as we had to crop some edge in different
                                                                devices to make it responsive.') }}</small>
                                                        </div>
                                                    </div>
                                                    <!-- Pip Code -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Pip Code') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" placeholder="{{ translate('pip code') }}"
                                                                name="stocks[{{ $index }}][pip_code]" class="form-control"
                                                                value="{{ $stock->pip_code }}">
                                                        </div>
                                                    </div>
                                                    <!-- Product Code -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Product Code') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" placeholder="{{ translate('product code') }}"
                                                                name="stocks[{{ $index }}][sku]" class="form-control"
                                                                value="{{ $stock->sku }}">
                                                        </div>
                                                    </div>
                                                    <!-- Variation -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Size') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" placeholder="{{ translate('Size') }}"
                                                                name="stocks[{{ $index }}][variant]" class="form-control"
                                                                value="{{ $stock->variant }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Flavour') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" placeholder="{{ translate('flavour') }}"
                                                                name="stocks[{{ $index }}][flavour]" class="form-control"
                                                                value="{{ $stock->flavour }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Color') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" placeholder="{{ translate('color') }}"
                                                                name="stocks[{{ $index }}][color]" class="form-control"
                                                                value="{{ $stock->color }}">
                                                        </div>
                                                    </div>
                                                    <!-- Quantity -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Quantity') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" placeholder="{{ translate('quantity') }}"
                                                                name="stocks[{{ $index }}][qty]" class="form-control"
                                                                value="{{ $stock->qty }}">
                                                        </div>
                                                    </div>
                                                    <!-- Price -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Price') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" placeholder="{{ translate('price') }}"
                                                                name="stocks[{{ $index }}][price]" class="form-control"
                                                                value="{{ $stock->price }}">
                                                        </div>
                                                    </div>
                                                        <div class="form-group row">
                                                            <label class="col-xxl-3 col-from-label fs-13">Pack Quantity
                                                                </label>
                                                            <div class="col-xxl-9">
                                                                <input type="text" class="form-control" name="stocks[{{ $index }}][pack_qty]"
                                                                    value="{{ $stock->pack_qty }}" 
                                                                    >
                                                            </div>
                                                        </div>
                                                    <!-- Description -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Description') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="col-md-12">
                                                            <textarea class="aiz-text-editor"
                                                                name="stocks[{{ $index }}][description]">{{ $stock->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <!-- Short Description -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Short Description') }}
                                                            <span class="text-danger">*</span></label>
                                                        <div class="col-md-12">
                                                            <textarea class="aiz-text-editor"
                                                                name="stocks[{{ $index }}][short_description]">{{ $stock->short_description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <!-- Delete Button -->
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="deleteStock(this)">Delete Stock</button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            @endforeach --}}
                                        </div>
                                    </div>
                                </div>
                                    </div>

                                    <!-- SEO -->
                                    <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                        <div class="bg-white p-3 p-sm-2rem">
                                            <!-- tab Title -->
                                            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                                {{ translate('SEO Meta Tags') }}</h5>
                                            <div class="w-100">
                                                <!-- Meta Title -->
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-from-label">{{ translate('Meta Title') }}</label>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="meta_title"
                                                            value="{{ $product->meta_title }}"
                                                            placeholder="{{ translate('Meta Title') }}">
                                                    </div>
                                                </div>
                                                <!-- Description -->
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                                                    <div class="col-md-9">
                                                        <textarea name="meta_description" rows="8" class="form-control">{{ $product->meta_description }}</textarea>
                                                    </div>
                                                </div>
                                                <!-- Meta Image -->
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label"
                                                        for="signinSrEmail">{{ translate('Meta Image') }}</label>
                                                    <div class="col-md-9">
                                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                                    {{ translate('Browse') }}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose File') }}
                                                            </div>
                                                            <input type="hidden" name="meta_img" value="{{ $product->meta_img }}"
                                                                class="selected-files">
                                                        </div>
                                                        <div class="file-preview box sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Slug -->
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label">{{ translate('Slug') }}</label>
                                                    <div class="col-md-8">
                                                        <input type="text" placeholder="{{ translate('Slug') }}" id="slug"
                                                            name="slug" value="{{ $product->slug }}" class="form-control">
                                                    </div>
                                                </div>

                                                <!-- FAQ -->
                                                <div class="form-group">
                                                    <label class="col-from-label fw-700 mb-2">{{ translate('FAQ') }}</label>
                                                    <small class="d-block text-muted mb-3">{{ translate('Add frequently asked questions and answers for this product.') }}</small>

                                                    @php
                                                        $existingFaqs = [];
                                                        if (!empty($product->faq)) {
                                                            $decoded = json_decode($product->faq, true);
                                                            $existingFaqs = is_array($decoded) ? $decoded : [];
                                                        }
                                                        // If validation failed, old() takes priority
                                                        if (old('faq_questions')) {
                                                            $existingFaqs = [];
                                                            foreach (old('faq_questions') as $fi => $q) {
                                                                $existingFaqs[] = ['question' => $q, 'answer' => old("faq_answers.$fi", '')];
                                                            }
                                                        }
                                                        if (empty($existingFaqs)) {
                                                            $existingFaqs[] = ['question' => '', 'answer' => ''];
                                                        }
                                                    @endphp

                                                    <div id="faq-container">
                                                        @foreach($existingFaqs as $fi => $faq)
                                                        <div class="faq-entry border rounded p-3 mb-3">
                                                            <div class="form-group row mb-2">
                                                                <label class="col-md-2 col-from-label">{{ translate('Question') }}</label>
                                                                <div class="col-md-10">
                                                                    <input type="text"
                                                                           name="faq_questions[{{ $fi }}]"
                                                                           class="form-control"
                                                                           placeholder="{{ translate('Enter question') }}"
                                                                           value="{{ $faq['question'] ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mb-0">
                                                                <label class="col-md-2 col-from-label">{{ translate('Answer') }}</label>
                                                                <div class="col-md-10">
                                                                    <textarea name="faq_answers[{{ $fi }}]"
                                                                              rows="3"
                                                                              class="form-control"
                                                                              placeholder="{{ translate('Enter answer') }}">{{ $faq['answer'] ?? '' }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="text-right mt-2">
                                                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="removeFaq(this)">
                                                                    <i class="las la-trash"></i> {{ translate('Remove') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>

                                                    <button type="button" class="btn btn-sm btn-soft-primary mt-1" onclick="addFaq()">
                                                        <i class="las la-plus"></i> {{ translate('Add Question') }}
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shipping -->
                                    <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                        <div class="bg-white p-3 p-sm-2rem">
                                            <!-- Shipping Configuration -->
                                            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                                {{ translate('Shipping Configuration') }}</h5>
                                            <div class="w-100">
                                                <!-- Cash On Delivery -->
                                                @if (get_setting('cash_payment') == '1')
                                                    <div class="form-group row">
                                                        <label
                                                            class="col-md-3 col-from-label">{{ translate('Cash On Delivery') }}</label>
                                                        <div class="col-md-9">
                                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                                <input type="checkbox" name="cash_on_delivery" value="1"
                                                                    @if ($product->cash_on_delivery == 1) checked @endif>
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p>
                                                        {{ translate('Cash On Delivery option is disabled. Activate this feature from here') }}
                                                        <a href="{{ route('activation.index') }}"
                                                            class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index', 'shipping_configuration.edit', 'shipping_configuration.update']) }}">
                                                            <span
                                                                class="aiz-side-nav-text">{{ translate('Cash Payment Activation') }}</span>
                                                        </a>
                                                    </p>
                                                @endif

                                                @if (get_setting('shipping_type') == 'product_wise_shipping')
                                                    <!-- Free Shipping -->
                                                    <div class="form-group row">
                                                        <label
                                                            class="col-md-3 col-from-label">{{ translate('Free Shipping') }}</label>
                                                        <div class="col-md-9">
                                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                                <input type="radio" name="shipping_type" value="free"
                                                                    @if ($product->shipping_type == 'free') checked @endif>
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <!-- Flat Rate -->
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-from-label">{{ translate('Flat Rate') }}</label>
                                                        <div class="col-md-9">
                                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                                <input type="radio" name="shipping_type" value="flat_rate"
                                                                    @if ($product->shipping_type == 'flat_rate') checked @endif>
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <!-- Shipping cost -->
                                                    <div class="flat_rate_shipping_div" style="display: none">
                                                        <div class="form-group row">
                                                            <label
                                                                class="col-md-3 col-from-label">{{ translate('Shipping cost') }}</label>
                                                            <div class="col-md-9">
                                                                <input type="number" lang="en" min="0"
                                                                    value="{{ $product->shipping_cost }}" step="0.01"
                                                                    placeholder="{{ translate('Shipping cost') }}"
                                                                    name="flat_shipping_cost" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Is Product Quantity Mulitiply -->
                                                    <div class="form-group row">
                                                        <label
                                                            class="col-md-3 col-from-label">{{ translate('Is Product Quantity Mulitiply') }}</label>
                                                        <div class="col-md-9">
                                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                                <input type="checkbox" name="is_quantity_multiplied" value="1"
                                                                    @if ($product->is_quantity_multiplied == 1) checked @endif>
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p>
                                                        {{ translate('Product wise shipping cost is disable. Shipping cost is configured from here') }}
                                                        <a href="{{ route('shipping_configuration.index') }}"
                                                            class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index', 'shipping_configuration.edit', 'shipping_configuration.update']) }}">
                                                            <span
                                                                class="aiz-side-nav-text">{{ translate('Shipping Configuration') }}</span>
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>

                                            <!-- Estimate Shipping Time -->
                                            <h5 class="mb-3 mt-4 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                                {{ translate('Estimate Shipping Time') }}</h5>
                                            <div class="w-100">
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-from-label">{{ translate('Shipping Days') }}</label>
                                                    <div class="col-md-9">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="est_shipping_days"
                                                                value="{{ $product->est_shipping_days }}" min="1"
                                                                step="1" placeholder="{{ translate('Shipping Days') }}">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"
                                                                    id="inputGroupPrepend">{{ translate('Days') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Update Button -->
                                    <div class="mt-4 text-right">
                                        <button type="button" onclick="submitFormWithTab()" name="button"
                                            class="mx-2 btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success action-btn">{{ translate('Update') }}</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @endsection

@section('script')
    <!-- Treeview js -->
    <script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

   <script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

<script type="text/javascript">
// ── FAQ repeater ────────────────────────────────────────────────────────────
let faqIndex = document.querySelectorAll('#faq-container .faq-entry').length;

function addFaq() {
    const idx = faqIndex++;
    const html = `
        <div class="faq-entry border rounded p-3 mb-3">
            <div class="form-group row mb-2">
                <label class="col-md-2 col-from-label">{{ translate('Question') }}</label>
                <div class="col-md-10">
                    <input type="text" name="faq_questions[${idx}]" class="form-control"
                           placeholder="{{ translate('Enter question') }}">
                </div>
            </div>
            <div class="form-group row mb-0">
                <label class="col-md-2 col-from-label">{{ translate('Answer') }}</label>
                <div class="col-md-10">
                    <textarea name="faq_answers[${idx}]" rows="3" class="form-control"
                              placeholder="{{ translate('Enter answer') }}"></textarea>
                </div>
            </div>
            <div class="text-right mt-2">
                <button type="button" class="btn btn-sm btn-soft-danger" onclick="removeFaq(this)">
                    <i class="las la-trash"></i> {{ translate('Remove') }}
                </button>
            </div>
        </div>`;
    document.getElementById('faq-container').insertAdjacentHTML('beforeend', html);
}

function removeFaq(btn) {
    const entry = btn.closest('.faq-entry');
    if (document.querySelectorAll('#faq-container .faq-entry').length > 1) {
        entry.remove();
    } else {
        entry.querySelector('input').value = '';
        entry.querySelector('textarea').value = '';
    }
}
// ── end FAQ repeater ────────────────────────────────────────────────────────
</script>

<script type="text/javascript">
    $(document).ready(function() {
            show_hide_shipping_div();

            $("#treeview").hummingbird();
            var main_id = '{{ $product->category_id != null ? $product->category_id : 0 }}';
            var selected_ids = '{{ implode(',', $old_categories) }}';
            if (selected_ids != '') {
                const myArray = selected_ids.split(",");
                for (let i = 0; i < myArray.length; i++) {
                    const element = myArray[i];
                    $('#treeview input:checkbox#' + element).prop('checked', true);
                    $('#treeview input:checkbox#' + element).parents("ul").css("display", "block");
                    $('#treeview input:checkbox#' + element).parents("li").children('.las').removeClass("la-plus")
                        .addClass('la-minus');
                }
            }
            $('#treeview input:radio[value=' + main_id + ']').prop('checked', true);
        });

        $("[name=shipping_type]").on("change", function() {
            show_hide_shipping_div();
        });

        function show_hide_shipping_div() {
            var shipping_val = $("[name=shipping_type]:checked").val();

            $(".flat_rate_shipping_div").hide();

            if (shipping_val == 'flat_rate') {
                $(".flat_rate_shipping_div").show();
            }
        }

        function add_more_customer_choice_option(i, name) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('products.add-more-choice-option') }}',
                data: {
                    attribute_id: i
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    $('#customer_choice_options').append('\
                    <div class="form-group row">\
                        <div class="col-md-3">\
                            <input type="hidden" name="choice_no[]" value="' + i + '">\
                            <input type="text" class="form-control" name="choice[]" value="' + name +
                        '" placeholder="{{ translate('Choice Title') }}" readonly>\
                        </div>\
                        <div class="col-md-8">\
                            <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_' + i + '[]" multiple>\
                                ' + obj + '\
                            </select>\
                        </div>\
                    </div>');
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });


        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
            } else {
                $('#colors').prop('disabled', false);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            update_sku();
        });

        $(document).on("change", ".attribute_choice", function() {
            update_sku();
        });

        $('#colors').on('change', function() {
            update_sku();
        });

        function delete_row(em) {
            $(em).closest('.form-group').remove();
            update_sku();
        }

        function delete_variant(em) {
            $(em).closest('.variant').remove();
        }

        function update_sku() {
            $.ajax({
                type: "POST",
                url: '{{ route('products.sku_combination_edit') }}',
                data: $('#choice_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data);
                    setTimeout(() => {
                        AIZ.uploader.previewGenerate();
                    }, "2000");
                    AIZ.plugins.fooTable();
                    if (data.trim().length > 1) {
                        $('#show-hide-div').hide();
                    } else {
                        $('#show-hide-div').show();
                    }
                }
            });
        }

        AIZ.plugins.tagify();

        $(document).ready(function() {
            update_sku();

            $('.remove-files').on('click', function() {
                $(this).parents(".col-md-4").remove();
            });
        });

        $('#choice_attributes').on('change', function() {
            $.each($("#choice_attributes option:selected"), function(j, attribute) {
                flag = false;
                $('input[name="choice_no[]"]').each(function(i, choice_no) {
                    if ($(attribute).val() == $(choice_no).val()) {
                        flag = true;
                    }
                });
                if (!flag) {
                    add_more_customer_choice_option($(attribute).val(), $(attribute).text());
                }
            });

            var str = @php echo $product->attributes @endphp;

            $.each(str, function(index, value) {
                flag = false;
                $.each($("#choice_attributes option:selected"), function(j, attribute) {
                    if (value == $(attribute).val()) {
                        flag = true;
                    }
                });
                if (!flag) {
                    $('input[name="choice_no[]"][value="' + value + '"]').parent().parent().remove();
                }
            });

            update_sku();
        });
</script>
<script>
    $(document).ready(function() {
            var hash = document.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
                $('#tab').val(location.hash.substr(1));
            } else {
                $('.nav-tabs a[href="#general"]').tab('show');
                $('#tab').val('general');
            }

            // Change hash for page-reload
            $('.nav-tabs a').on('shown.bs.tab', function(e) {
                window.location.hash = e.target.hash;
            });
        });

        function submitFormWithTab() {
            var hash = document.location.hash;
            if (hash) {
                $('#tab').val(location.hash.substr(1));
            } else {
                $('#tab').val('');
            }
            $('#choice_form').submit();
        }
</script>

<!-- JavaScript for Adding and Deleting Stocks -->
<script>
    // Base index off the actual rendered rows, not just DB count
    // This stays correct whether we're showing old() data or DB data
    let stockIndex = {{ $nextIndex }};

    function addNewStock() {
        const idx = stockIndex;
        const stockTemplate = `
            <div class="stock-entry" data-index="${idx}">
                <input type="hidden" name="stocks[${idx}][id]" value="">
                <input type="hidden" name="stocks[${idx}][deleted]" value="0" class="delete-flag">
                <div class="w-100">
                    <!-- Gallery Images -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Gallery Images') }}
                            <small>(600x600)</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="stocks[${idx}][photos]" value="" class="selected-files">
                            </div>
                            <div class="file-preview box sm"></div>
                            <small class="text-muted">{{ translate('These images are visible in product details page gallery. Use 600x600 sizes images.') }}</small>
                        </div>
                    </div>
                    <!-- Thumbnail Image -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Thumbnail Image') }}
                            <small>(300x300)</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="stocks[${idx}][thumbnail_img]" value="" class="selected-files">
                            </div>
                            <div class="file-preview box sm"></div>
                            <small class="text-muted">{{ translate('This image is visible in all product box. Use 300x300 sizes image.') }}</small>
                        </div>
                    </div>
                    <!-- Pip Code -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Pip Code') }}</label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('pip code') }}" name="stocks[${idx}][pip_code]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Product Code -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Product Code') }} <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('product code') }}" name="stocks[${idx}][sku]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Size -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Size') }}</label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('size') }}" name="stocks[${idx}][variant]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Flavour -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Flavour') }}</label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('flavour') }}" name="stocks[${idx}][flavour]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Color -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Color') }}</label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('Color') }}" name="stocks[${idx}][color]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Quantity -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Quantity') }} <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('quantity') }}" name="stocks[${idx}][qty]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Price -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Price') }} <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('price') }}" name="stocks[${idx}][price]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Pack Quantity -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Pack Quantity') }}</label>
                        <div class="col-md-6">
                            <input type="text" placeholder="{{ translate('pack_quantity') }}" name="stocks[${idx}][pack_qty]" class="form-control" value="">
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                        <div class="col-md-12">
                            <textarea class="aiz-text-editor" name="stocks[${idx}][description]"></textarea>
                        </div>
                    </div>
                    <!-- Short Description -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Short Description') }}</label>
                        <div class="col-md-12">
                            <textarea class="aiz-text-editor" name="stocks[${idx}][short_description]"></textarea>
                        </div>
                    </div>
                    <!-- Features -->
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Features') }}</label>
                        <div class="col-md-12">
                            <textarea class="aiz-text-editor" name="stocks[${idx}][features]"></textarea>
                            <small class="text-muted">{{ translate('Key features or highlights for this variant.') }}</small>
                        </div>
                    </div>
                    <!-- Delete Button -->
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="deleteStock(this)">Delete Stock</button>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>`;

        document.getElementById('stocks-container').insertAdjacentHTML('beforeend', stockTemplate);
        stockIndex++;

        loadSummernoteAssets(function () {
            initEditors();
        });
    }

    function deleteStock(button) {
        const stockEntry = button.closest('.stock-entry');
        const idInput    = stockEntry.querySelector('input[name$="[id]"]');
        const stockId    = idInput ? idInput.value.trim() : '';

        if (stockId) {
            // Existing DB record — mark deleted and hide, backend will delete it
            const deleteFlag = stockEntry.querySelector('.delete-flag');
            if (deleteFlag) deleteFlag.value = '1';
            stockEntry.style.display = 'none';
        } else {
            // New unsaved row — just remove from DOM entirely, nothing to delete in DB
            stockEntry.remove();
        }
    }


    // 🔹 Helper: Load CSS/JS dynamically
function loadSummernoteAssets(callback) {
    function loadCss(href, id) {
        if (!document.getElementById(id)) {
            const link = document.createElement('link');
            link.id = id;
            link.rel = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
        }
    }

    function loadJs(src, id, onLoad) {
        if (!document.getElementById(id)) {
            const script = document.createElement('script');
            script.id = id;
            script.src = src;
            script.onload = onLoad;
            document.body.appendChild(script);
        } else if (onLoad) {
            onLoad();
        }
    }

    // Load CSS
    loadCss("https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css", "summernote-css");

    // Load JS
    loadJs("https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js", "summernote-js", function () {
        loadJs("{{ static_asset('assets/summernote-image-attributes-master/summernote-image-attributes.js') }}", "summernote-image-attributes", callback);
    });
}

// 🔹 Helper: Initialize editors
function initEditors() {
    $(".aiz-text-editor").not('.summernote-initialized').each(function () {
        var $this = $(this);
        $this.addClass('summernote-initialized');

        $this.summernote({
            minHeight: 200,
            toolbar: [
                ["font", ["bold", "underline", "italic", "clear"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["insert", ["link", "picture", "video", "imageAttributes"]],
                ["view", ["fullscreen", "undo", "redo"]],
            ]
        });
    });
}
</script>

{{-- <script>
    document.getElementById('product_name').addEventListener('input', function () {
        let text = this.value;

        let slug = text
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '') // remove special chars
            .replace(/\s+/g, '-')        // replace spaces with -
            .replace(/-+/g, '-');        // remove multiple -

        document.getElementById('slug').value = slug;
    });
</script> --}}
@endsection
