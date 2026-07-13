<!-- Account Payable Section -->
<h5 class="mb-4">Account Payable</h5>
<form id="accountPayableForm">
  <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">

  <div class="row mb-3">
    <div class="col-md-6">
      <label for="firstName" class="form-label">First Name</label>
      <input type="text" class="form-control" id="payFirstName" name="firstName" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->first_name ?? '' }}">
      <span class="error-message" id="payFirstName-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="lastName" class="form-label">Last Name</label>
      <input type="text" class="form-control" id="payLastName" name="lastName" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->last_name ?? '' }}">
      <span class="error-message" id="payLastName-error" style="color:red; display:none;">This field is required.</span>

    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-12">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="payEmail" name="contact_email" placeholder="Type email here" value="{{ $customerDetail?->accountPayable?->first()?->email ?? '' }}">
      <span class="error-message" id="payEmail-error" style="color:red; display:none;">This field is required.</span>

    </div>
  </div>
  <div class="row mb-3">
    <div class="col-md-6">
      <label for="officeNumber" class="form-label">Office Number</label>
      <div class="input-group">
        <span class="input-group-text">+44</span>
        <input type="text" class="form-control" id="payOfficeNumber" name="officeNumber" placeholder="" value="{{ $customerDetail?->accountPayable?->first()?->office_number ?? '' }}">
        
      </div>
      <span class="error-message" id="payOfficeNumber-error" style="color:red; display:none;">This field is required.</span>
    </div>
    <div class="col-md-6">
      <label for="mobileNumber" class="form-label">Mobile Number</label>
      <div class="input-group">
        <span class="input-group-text">+44</span>
        <input type="text" class="form-control" id="payMobileNumber" name="mobileNumber" placeholder="" value="{{ $customerDetail?->accountPayable?->first()?->mobile_number ?? '' }}">
        
      </div>
      <span class="error-message" id="payMobileNumber-error" style="color:red; display:none;">This field is required.</span>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <label for="firstName" class="form-label">Order Confirmation Email</label>
      <input type="text" class="form-control" id="payFirstName" name="confirmationEmail" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->confirmation_email ?? '' }}">
      <span class="error-message" id="payFirstName-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="lastName" class="form-label">Invoice/Statement Email</label>
      <input type="text" class="form-control" id="payLastName" name="statementEmail" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->statement_email ?? '' }}">
      <span class="error-message" id="payLastName-error" style="color:red; display:none;">This field is required.</span>

    </div>
  </div>

<hr>

<!-- Address Detail Section -->
<h5 class="mb-4">Address Information</h5>

  <div class="row g-3">
    <div class="col-md-6">
      <label for="registerPostcode" class="form-label">Post Code</label>
      <input type="text" class="form-control" id="payPostcode" name="postcode" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->post_code ?? '' }}">
      <span class="error-message" id="payPostcode-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerAddress1" class="form-label">Address 1</label>
      <input type="text" class="form-control" id="payAddress1" name="address1" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->address1 ?? '' }}">
      <span class="error-message" id="payAddress1-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerAddress2" class="form-label">Address 2</label>
      <input type="text" class="form-control" id="payAddress2" name="address2" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->address2 ?? '' }}">
      <span class="error-message" id="payAddress2-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerTown" class="form-label">Town</label>
      <input type="text" class="form-control" id="payTown" name="town" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->town ?? '' }}">
      <span class="error-message" id="payTown-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="registerCity" class="form-label">City</label>
      <input type="text" class="form-control" id="payCity" name="city" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->city ?? '' }}">
      <span class="error-message" id="payCity-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-6">
      <label for="payCountry" class="form-label">Country</label>
      <select class="form-select" id="payCountry" name="country" required>
        <option value="" disabled selected>Select</option>
        @foreach ( $country as $country )
            <option value="{{ $country->id }}" {{ $customerDetail?->headOffice?->first()?->country == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
        @endforeach
    </select>
      <span class="error-message" id="payCountry-error" style="color:red; display:none;">This field is required.</span>

    </div>
  </div>


<hr>

<!-- Bank Information Section -->
<h5 class="mb-4">Bank Details</h5>

  <div class="row g-3">
    <div class="col-md-4">
      <label for="accountName" class="form-label">Account Name</label>
      <input type="text" class="form-control" id="payAccountName" name="accountName" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->account_name ?? '' }}">
      <span class="error-message" id="payAccountName-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-4">
      <label for="bankName" class="form-label">Bank Name</label>
      <input type="text" class="form-control" id="payBankName" name="bankName" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->bank_name ?? '' }}">
      <span class="error-message" id="payBankName-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-4">
      <label for="shortCode" class="form-label">Short Code</label>
      <input type="text" class="form-control" id="payShortCode" name="shortCode" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->short_code ?? '' }}">
      <span class="error-message" id="payShortCode-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-4">
      <label for="accountNumber" class="form-label">Account Number</label>
      <input type="text" class="form-control" id="payAccountNumber" name="accountNumber" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->account_number ?? '' }}">
      <span class="error-message" id="payAccountNumber-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-4">
      <label for="iban" class="form-label">IBAN</label>
      <input type="text" class="form-control" id="payIban" name="iban" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->iban ?? '' }}">
      <span class="error-message" id="payIban-error" style="color:red; display:none;">This field is required.</span>

    </div>
    <div class="col-md-4">
      <label for="swiftCode" class="form-label">SWIFT/BIC</label>
      <input type="text" class="form-control" id="paySwiftCode" name="swiftCode" placeholder="Type here" value="{{ $customerDetail?->accountPayable?->first()?->swift_code ?? '' }}">
      <span class="error-message" id="paySwiftCode-error" style="color:red; display:none;">This field is required.</span>

    </div>
  </div>
  <div class="d-flex justify-content-end mt-3">
    <button type="button" id="payBackButton" class="btn btn-secondary me-2">Back</button>
    <button type="button" id="accountPayableButton" class="btn btn-success">Finish</button>
  </div>
</form>
