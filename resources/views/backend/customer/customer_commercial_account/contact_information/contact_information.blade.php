<h5 class="mb-4">Contact Information</h5>
      
<form id="contactInformationForm">
  <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">

<div class="row mb-3">

  <div class="col-md-6">
    <label for="firstName" class="form-label">First Name</label>
    <input type="text" class="form-control" id="contactFirstName" name="firstName" placeholder="Type here" value="{{ $customerDetail?->contactInformation?->first_name ?? '' }}">
    <span class="error-message" id="contactFirstName-error" style="color:red; display:none;">This field is required.</span>

  </div>
  <div class="col-md-6">
    <label for="lastName" class="form-label">Last Name</label>
    <input type="text" class="form-control" id="contactLastName" name="lastName" placeholder="Type here" value="{{ $customerDetail?->contactInformation?->last_name ?? '' }}">
    <span class="error-message" id="contactLastName-error" style="color:red; display:none;">This field is required.</span>

  </div>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="contactEmail" name="contact_email" placeholder="Type email here" value="{{ $customerDetail?->contactInformation?->email ?? '' }}">
    <span class="error-message" id="contactEmail-error" style="color:red; display:none;">This field is required.</span>

  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="officeNumber" class="form-label">Office Number</label>
    <div class="input-group">
      <span class="input-group-text">+44</span>
      <input type="text" class="form-control" id="contactOfficeNumber" name="officeNumber" placeholder="" value="{{ $customerDetail?->contactInformation?->office_number ?? '' }}">
      
    </div>
    <span class="error-message" id="contactOfficeNumber-error" style="color:red; display:none;">This field is required.</span>
  </div>
  <div class="col-md-6">
    <label for="mobileNumber" class="form-label">Mobile Number</label>
    <div class="input-group">
      <span class="input-group-text">+44</span>
      <input type="text" class="form-control" id="contactMobileNumber" name="mobileNumber" placeholder="" value="{{ $customerDetail?->contactInformation?->mobile_number ?? '' }}">
      
    </div>
    <span class="error-message" id="contactMobileNumber-error" style="color:red; display:none;">This field is required.</span>
  </div>
</div>

<div class="d-flex justify-content-end">
 
    <button type="button" id="contactBackButton" class="btn btn-secondary me-2">Back</button>
    <button type="button" id="contactInformationButton" class="btn btn-primary">Next</button>
 
</div>

