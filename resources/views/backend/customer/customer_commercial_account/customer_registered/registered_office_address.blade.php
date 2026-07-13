<h5 class="mb-4">Registered Office Address</h5>
<form id="registerOfficeForm">
  @csrf
  <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">

  <div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" id="sameAsHeadOffice" />
    <label class="form-check-label" for="sameAsHeadOffice">Select if address is the same as 'Head Office'.</label>
  </div>
  <div class="row g-3">
    <div class="col-md-6">
      <label for="registerPostcode" class="form-label">Post Code</label>
      <input type="text" class="form-control" id="registerPostcode" name="postcode" placeholder="Type here" value="{{ $customerDetail?->registerOffice?->first()?->postcode ?? '' }}" />
      <span class="error-message" id="registerPostcode-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerAddress1" class="form-label">Address 1</label>
      <input type="text" class="form-control" id="registerAddress1" name="address1" placeholder="Type here" value="{{ $customerDetail?->registerOffice?->first()?->address1 ?? '' }}" />
      <span class="error-message" id="registerAddress1-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerAddress2" class="form-label">Address 2</label>
      <input type="text" class="form-control" id="registerAddress2" name="address2" placeholder="Type here" value="{{ $customerDetail?->registerOffice?->first()?->address2 ?? '' }}"/>

    </div>
    <div class="col-md-6">
      <label for="registerAddress3" class="form-label">Address 3</label>
      <input type="text" class="form-control" id="registerAddress3" name="address3" placeholder="Type here" value="{{ $customerDetail?->registerOffice?->first()?->address3 ?? '' }}"/>

    </div>
    <div class="col-md-6">
      <label for="registerTown" class="form-label">Town</label>
      <input type="text" class="form-control" id="registerTown" name="town" placeholder="Type here" value="{{ $customerDetail?->registerOffice?->first()?->town ?? '' }}"/>
      <span class="error-message" id="registerTown-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerCity" class="form-label">City</label>
      <input type="text" class="form-control" id="registerCity" name="city" placeholder="Type here" value="{{ $customerDetail?->registerOffice?->first()?->city ?? '' }}"/>
      <span class="error-message" id="registerCity-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerCounty" class="form-label">County</label>
      <input type="text" class="form-control" id="registerCounty" name="county" placeholder="Type here" value="{{ $customerDetail?->registerOffice?->first()?->county ?? '' }}"/>
      <span class="error-message" id="registerCounty-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerCountry" class="form-label">Country</label>
      <select class="form-select" id="registerCountry" name="country" >
        <option value="" disabled selected>Select</option>
        @foreach ( $country as $country )
            <option value="{{ $country->id }}" {{ $customerDetail?->headOffice?->first()?->country == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
        @endforeach
    </select>
      <span class="error-message" id="registerCountry-error" style="color:red; display:none;">This field is required.</span>
    </div>
  </div>
  <div class="d-flex justify-content-end">
    <button type="button"  id="registerBackButton" class="btn btn-outline-secondary me-3">Back</button>
    <button type="submit" class="btn btn-primary">Next</button>
    </div>
</form>
