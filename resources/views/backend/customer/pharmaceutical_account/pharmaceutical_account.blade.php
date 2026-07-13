@extends('backend.layouts.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        </script>
    @endif
    <div class="container">
        <h5 class="mb-4">Pharmaceutical Account</h5>
        <form action="{{ route('pharmaceutical_account.store') }}" method="post">
            @csrf


            <input type="hidden" name="user_id" id="userId" value="{{ $user->id ?? '' }}">


            {{-- line  - 1 --}}

            {{-- line  - 2 --}}
            <div class="row g-3 mt-3">

                <div class="col-md-6">
                    <label for="first_name" class="form-label">License Holder First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Type here"
                        value="{{ $user?->pharmaceuticalAccount?->holder_first_name ?? '' }}">
                    <span class="error-message" id="first_name-error" style="color:red; display:none;">This field is
                        required.</span>
                </div>

                <div class="col-md-6">
                    <label for="last_name" class="form-label">License Holder Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Type here"
                        value="{{ $user?->pharmaceuticalAccount?->holder_last_name ?? '' }}">
                    <span class="error-message" id="last_name-error" style="color:red; display:none;">This field is
                        required.</span>
                </div>


            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label for="company_name" class="form-label">Bussiness Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Type here"
                        value="{{ $user?->pharmaceuticalAccount?->company_name ?? '' }}">
                    <span class="error-message" id="company_name-error" style="color:red; display:none;">This field is
                        required.</span>
                </div>

                <div class="col-md-6">
                    <label for="account_number" class="form-label">Account Number</label>
                    <input type="number" class="form-control" id="account_number" name="account_number"
                        placeholder="Type here" value="{{ $user?->pharmaceuticalAccount?->account_number ?? '' }}">
                    <span class="error-message" id="account_number-error" style="color:red; display:none;">This field is
                        required.</span>
                </div>


            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Type here"
                        >
                </div>
                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                        placeholder="Type here" >
                </div>
            </div>
            {{-- line  - 3 --}}
            <div class="row mb-3 mt-3">
    <div class="col-md-12">
        <label for="license_type" class="form-label">License Type</label>
        <select class="form-control aiz-selectpicker" id="license_type" name="license_type">
            <option value="">Choose..</option>

            @php
                $license_type = optional($user->pharmaceuticalAccount)->license_type;
            @endphp

            <option value="1" {{ $license_type == '1' ? 'selected' : '' }}>GMC</option>
            <option value="2" {{ $license_type == '2' ? 'selected' : '' }}>GPC</option>
            <option value="3" {{ $license_type == '3' ? 'selected' : '' }}>HPC</option>
            <option value="4" {{ $license_type == '4' ? 'selected' : '' }}>JDC</option>
            <option value="5" {{ $license_type == '5' ? 'selected' : '' }}>NMC</option>
            <option value="6" {{ $license_type == '6' ? 'selected' : '' }}>WDL</option>
            <option value="7" {{ $license_type == '7' ? 'selected' : '' }}>GPSC</option>
            <option value="8" {{ $license_type == '8' ? 'selected' : '' }}>Other</option>
        </select>

        <span class="error-message" id="license_type-error" style="color:red; display:none;">This field is required.</span>
    </div>
</div>

         <!-- This is where dynamic content will be placed -->
         <div id="dynamic-sections"  >
            <div class="row g-3 mt-2 d-flex col-md-12">
                <div id="section-1" class="dynamic-content d-none col-md-6">
                    <label for="license_number" class="form-label">License Number</label>
                    <input type="text" class="form-control" id="license_number" name="license_number"
                        value="{{ $user?->pharmaceuticalAccount?->license_number ?? '' }}" placeholder="Type here" >
                </div>
        
                <div id="section-2" class="dynamic-content d-none col-md-6" style="margin-left:8px;">
                    <label for="license_name" class="form-label">License Name</label>
                    <input type="text" class="form-control" id="license_name"
                        value="{{ $user?->pharmaceuticalAccount?->license_name ?? '' }}" name="license_name"
                        placeholder="Type here" >
                </div>
            </div>
        </div>
        
        
        <div id="dynamic-section" style="display: flex; margin-left:-18px;" ></div> 
        
            {{-- line  - 4 --}}
            <div class="row g-3 mt-3">

                <div class="col-md-6">
                    <label for="registration_date" class="form-label">License Registration Date </label>
                    <input type="date" class="form-control" id="registration_date " name="registration_date"
                        placeholder="Type here" value="{{ $user?->pharmaceuticalAccount?->registration_date ?? '' }}">
                    <span class="error-message" id="registration_date-error" style="color:red; display:none;">This field
                        is
                        required.</span>
                </div>

                <div class="col-md-6">
                    <label for="holder_email" class="form-label">License Holder Email</label>
                    <input type="email" class="form-control" id="holder_email" name="holder_email"
                        placeholder="Type here" value="{{ $user?->pharmaceuticalAccount?->holder_email ?? '' }}">
                    <span class="error-message" id="holder_email-error" style="color:red; display:none;">This field is
                        required.</span>
                </div>

            </div>
           
            {{-- line  - 5 --}}
            <style>
                .terms-conditions {
                    font-size: 14px;
                    /* Increased font size */
                    line-height: 1.6;
                }

                .terms-conditions ul {
                    padding-left: 20px;
                }

                .terms-conditions a {
                    font-weight: bold;
                    color: #007bff;
                    /* Bootstrap primary color */
                    text-decoration: none;
                }

                .terms-conditions a:hover {
                    text-decoration: underline;
                }

                .form-check-label {
                    font-size: 14px;
                    /* Increased font size for checkbox label */
                }
            </style>

            <div class="terms-conditions mt-5">
                <p><strong>By registering on Doctor Shop, you agree to the following terms and conditions:</strong></p>

                <ul>
                    <li>You have read, understood, and accepted our <a href="terms-and-conditions.html"
                            target="_blank">Terms & Conditions</a>.</li>
                    <li>You comply with all applicable pharmaceutical regulations, including any local, state, and federal
                        laws.</li>
                    <li>The information provided during the registration process is accurate and truthful.</li>
                    <li>You are authorized to act on behalf of the pharmaceutical entity you represent, if applicable.</li>
                    <li>Your personal and business information will be stored securely and not shared without your consent.
                    </li>
                </ul>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">
                        I acknowledge and agree to the above terms.
                    </label>
                </div>
            </div>


            {{-- <div class="row g-3 mt-3">

                <div class="col-md-6">
                    <label for="Signature" class="form-label">Signature </label>
                    <input type="text" class="form-control" id="Signature " name="Signature" placeholder="Type here"
                        required value="{{ $user?->pharmaceuticalAccount?->Signature?? '' }}">
                    <span class="error-message" id="Signature-error" style="color:red; display:none;">This field is
                        required.</span>
                </div>



            </div> --}}



            <div class="d-flex justify-content-end mt-4">
                <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    var organizationType = document.getElementById("license_type");
    var dynamicSection = document.getElementById("dynamic-section");

    function updateDynamicContent() {
        var selectedValue = organizationType.value;
        dynamicSection.innerHTML = ''; // Clear previous content

        if (['1', '2', '3', '4', '5', '6', '7'].includes(selectedValue)) {
            var clone1 = document.getElementById("section-1").cloneNode(true);
            clone1.classList.remove("d-none"); // Show the section
            dynamicSection.appendChild(clone1);
        } else if (selectedValue === '8') {
            var clone2 = document.getElementById("section-2").cloneNode(true);
            var clone1 = document.getElementById("section-1").cloneNode(true);
            clone2.classList.remove("d-none");
            clone1.classList.remove("d-none");

            dynamicSection.appendChild(clone1);
            dynamicSection.appendChild(clone2);
        }
    }

    // Run once on page load to set the correct section for editing
    updateDynamicContent();

    // Change event listener
    organizationType.addEventListener("change", updateDynamicContent);
});

    </script>
@endsection
