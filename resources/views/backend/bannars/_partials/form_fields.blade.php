{{--
    Shared banner form fields partial.
    Variables expected:
      $bannars  — model instance (for edit) or null (for create)
--}}
@php $type = $bannars->banner_type ?? 'simple'; @endphp

{{-- Banner Type --}}
<div class="form-group row">
    <label class="col-sm-3 col-from-label">Banner Type</label>
    <div class="col-sm-9">
        <select name="banner_type" id="banner_type" class="form-control" onchange="toggleBannerFields()">
            <option value="simple"  {{ $type === 'simple'  ? 'selected' : '' }}>Simple Banner</option>
            <option value="product" {{ $type === 'product' ? 'selected' : '' }}>Product Banner</option>
        </select>
    </div>
</div>

{{-- Image (always shown) --}}
<div class="form-group row">
    <label class="col-md-3 col-form-label">Image</label>
    <div class="col-md-9">
        <div class="input-group" data-toggle="aizuploader" data-type="image">
            <div class="input-group-prepend">
                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
            </div>
            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
            <input type="hidden" name="image" value="{{ $bannars->image ?? '' }}" class="selected-files">
        </div>
        <div class="file-preview box sm"></div>
    </div>
</div>

{{-- Background Image (always shown) --}}
<div class="form-group row">
    <label class="col-md-3 col-form-label">Background Image</label>
    <div class="col-md-9">
        <div class="input-group" data-toggle="aizuploader" data-type="image">
            <div class="input-group-prepend">
                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
            </div>
            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
            <input type="hidden" name="background_image" value="{{ $bannars->background_image ?? '' }}" class="selected-files">
        </div>
        <div class="file-preview box sm">
        </div>
    </div>
</div>

{{-- URL (applies to both banner types) --}}
<div class="form-group row">
    <label class="col-sm-3 col-from-label">URL</label>
    <div class="col-sm-9">
        <input type="text" name="url" class="form-control"
               placeholder="https://example.com/page" value="{{ $bannars->url ?? '' }}">
        <small class="text-muted">Optional link when the banner is tapped.</small>
    </div>
</div>

{{-- Simple Banner Fields --}}
<div id="simple_fields">
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">Label Text</label>
        <div class="col-sm-9">
            <input type="text" name="title" class="form-control"
                   placeholder="Label text" value="{{ $bannars->title ?? '' }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">Badge Text</label>
        <div class="col-sm-9">
            <input type="text" name="badge_text" class="form-control"
                   placeholder="Badge text" value="{{ $bannars->badge_text ?? '' }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">Description</label>
        <div class="col-sm-9">
            <textarea name="description" rows="4" class="form-control"
                      placeholder="Description...">{{ $bannars->description ?? '' }}</textarea>
        </div>
    </div>
</div>

{{-- Product Banner Fields --}}
<div id="product_fields" style="display:none;">
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">SKU</label>
        <div class="col-sm-9">
            <input type="text" name="sku" class="form-control"
                   placeholder="Product SKU" value="{{ $bannars->sku ?? '' }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">Product Title</label>
        <div class="col-sm-9">
            <input type="text" name="product_title" class="form-control"
                   placeholder="Product title" value="{{ $bannars->product_title ?? '' }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">Price</label>
        <div class="col-sm-9">
            <input type="number" step="0.01" name="price" class="form-control"
                   placeholder="0.00" value="{{ $bannars->price ?? '' }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">VAT</label>
        <div class="col-sm-9">
            <input type="number" step="0.01" name="vat" class="form-control"
                   placeholder="0.00" value="{{ $bannars->vat ?? '' }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-from-label">Button Text</label>
        <div class="col-sm-9">
            <input type="text" name="button_text" class="form-control"
                   placeholder="e.g. Shop Now" value="{{ $bannars->button_text ?? '' }}">
        </div>
    </div>
</div>
