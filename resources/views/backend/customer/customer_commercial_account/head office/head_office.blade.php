<h5 class="mb-4">Head Office</h5>
<form id="headOfficeForm">
    @csrf
    <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="postcode" class="form-label">Post Code</label>
            <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Type here" required value="{{ $customerDetail?->headOffice->first()->postcode ?? '' }}">
            <span class="error-message" id="postcode-error" style="color:red; display:none;">This field is required.</span>
        </div>
        <div class="col-md-6">
            <label for="address1" class="form-label">Address  1</label>
            <input type="text" class="form-control" id="address1" name="address1" placeholder="Type here" required value="{{ $customerDetail?->headOffice?->first()?->address1 ?? '' }}">
            <span class="error-message" id="address1-error" style="color:red; display:none;">This field is required.</span>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="address2" class="form-label">Address  2</label>
            <input type="text" class="form-control" id="address2" name="address2" placeholder="Type here" value="{{ $customerDetail?->headOffice?->first()?->address2 ?? '' }}">

        </div>
        <div class="col-md-6">
            <label for="address3" class="form-label">Address  3</label>
            <input type="text" class="form-control" id="address3" name="address3" placeholder="Type here" value="{{ $customerDetail?->headOffice?->first()?->address3 ?? '' }}">

        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="town" class="form-label">Town</label>
            <input type="text" class="form-control" id="town" name="town" placeholder="Type here" value="{{ $customerDetail?->headOffice?->first()?->town ?? '' }}">
            <span class="error-message" id="town-error" style="color:red; display:none;">This field is required.</span>

        </div>
        <div class="col-md-6">
            <label for="city" class="form-label">City</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="Type here" value="{{ $customerDetail?->headOffice?->first()?->city ?? '' }}">
            <span class="error-message" id="city-error" style="color:red; display:none;">This field is required.</span>

        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="county" class="form-label">County</label>
            <input type="text" class="form-control" id="county" name="county" placeholder="Type here" value="{{ $customerDetail?->headOffice?->first()?->county ?? '' }}">
            <span class="error-message" id="county-error" style="color:red; display:none;">This field is required.</span>

        </div>
        <div class="col-md-6">
            <label for="country" class="form-label">Country*</label>
            <select class="form-select" id="country" name="country" required>
                <option value="" disabled selected>Select</option>
                @foreach ( $country as $country )
                    <option value="{{ $country->id }}" {{ $customerDetail?->headOffice?->first()?->country == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                @endforeach
            </select>
            <span class="error-message" id="country-error" style="color:red; display:none;">This field is required.</span>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <button type="button" id="headBackButton" class="btn btn-outline-secondary me-3">Back</button>
        <button type="button" id="submitButton" class="btn btn-primary">Next</button>
    </div>
</form>


