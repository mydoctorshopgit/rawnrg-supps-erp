<h5 class="mb-4">Login</h5>
<form id="LoginForm">
    <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="aname" name="name" placeholder="Type here" value="{{ $customerDetail?->user?->first()?->name ?? '' }}">
            <span class="error-message" id="aname-error" style="color:red; display:none;" >This field is required.</span>

        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" id="aemail" name="email" placeholder="Type here" value="{{ $customerDetail?->user?->first()?->email ?? '' }}">
            <span class="error-message" id="aemail-error" style="color:red; display:none;">This field is required.</span>

        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="text" class="form-control" id="apassword" name="password" placeholder="Type here" value="{{ $customerDetail?->user?->first()?->password ?? '' }}">
            <span class="error-message" id="apassword-error" style="color:red; display:none;">This field is required.</span>

        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" id="loginBackButton" class="btn btn-outline-secondary me-3">Back</button>
        <button type="button" id="loginButton" class="btn btn-primary">Next</button>
    </div>
</form>
