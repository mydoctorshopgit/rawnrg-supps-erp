@extends('backend.layouts.app')

@section('content')


    <div class="page-content">
        <div class="aiz-titlebar text-left mt-2 pb-2 px-3 px-md-2rem border-bottom border-gray">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3">{{ translate(' Register Customer Account') }}</h1>
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
                            {{ translate('Personal Details ') }}
                        </a>
                    </li>
                    <!-- Files & Media -->
                    <li class="nav-item">
                        <a class="nav-link" id="files-and-media-tab" href="#files_and_media" data-toggle="tab"
                            data-target="#files_and_media" type="button" role="tab" aria-controls="files_and_media"
                            aria-selected="false">
                            {{ translate('Delivery Address') }}
                        </a>
                    </li>
                    <!-- Price & Stock -->

                </ul>
            </div>

            <!-- tab content -->
            <div class="flex-grow-1 p-sm-3 p-lg-2rem mb-2rem mb-md-0">
                <!-- Error Meassages -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div class="tab-content">
                    <!-- General -->
                    <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="bg-white p-3 p-sm-2rem">
                            <!-- Product Information -->
                            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                {{ translate('Personal Detail') }}</h5>
                            <form method="POST" id="register_credit_form">
                                @csrf
                                <input type="hidden" name="user_id" id="userId" value="{{ $user->id ?? '' }}">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="organization_type" class="form-label">Health Organization </label>
                                        <select class="form-control aiz-selectpicker" id="organization_type"
                                            name="organization_type" required>
                                            <option value="">Select Health Organization </option>
                                            <option value="1"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '1' ? 'selected' : '' }}>
                                                GP Surgery</option>
                                            <option value="2"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '2' ? 'selected' : '' }}>
                                                NHS Hospitals</option>
                                            <option value="3"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '3' ? 'selected' : '' }}>
                                                Private Clinic</option>
                                            <option value="4"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '4' ? 'selected' : '' }}>
                                                Private Hospitals</option>
                                            <option value="5"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '5' ? 'selected' : '' }}>
                                                Dental Practice</option>
                                            <option value="6"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '6' ? 'selected' : '' }}>
                                                Optometry Clinic</option>
                                            <option value="7"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '7' ? 'selected' : '' }}>
                                                Pharmacy</option>
                                            <option value="8"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '8' ? 'selected' : '' }}>
                                                Podiatry Clinic</option>
                                            <option value="9"
                                                {{ isset($user) && $user?->registerCredit->organization_type == '9' ? 'selected' : '' }}>
                                                Other</option>
                                        </select>
                                        <span class="error-message" id="organization_type-error"
                                            style="color:red; display:none;">This field is required.</span>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="first_name_common" class="form-label">Business Name</label>
                                        <input type="text" class="form-control" id="company_name"
                                        name="company_name"
                                        value="{{ $user?->registerCredit->company_name ?? '' }}"
                                        placeholder="Type here" required>
                                    </div>
                                </div>

                                <!-- Static Fields (Common for All) -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="first_name_common" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name_common" name="first_name"
                                            value="{{ $user?->name ?? '' }}" placeholder="Type here">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name_common" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name_common" name="last_name"
                                            value="{{ $user?->last_name ?? '' }}" placeholder="Type here">
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label for="email_common" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email_common" name="email"
                                            value="{{ $user?->email ?? '' }}" placeholder="Type here">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password"
                                            name="password" placeholder="Type here" required>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password" placeholder="Type here" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="mobile_number_common" class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile_number_common"
                                            value="{{ $user?->registerCredit?->mobile_number ?? '' }}"
                                            name="mobile_number" placeholder="Type here">
                                    </div>
                                </div>
                                <!-- Dynamic Section -->
                                <div id="dynamic-sections">
                                    <div id="section-1-2" class="dynamic-content d-none">
                             
                                        <div class="row g-3 mt-2">
                                                                            <div class="col-md-6">
                                                <label for="phone_number" class="form-label">Phone Number</label>
                                                 <!--<input type="number" class="form-control" id="phone_number"-->
                                                 <!--   name="phone_number"-->
                                                 <!--   value="{{ $user?->registerCredit?->mobile_number}}"-->
                                                 <!--   placeholder="Type here">-->
                                                  <input type="text" class="form-control" id="mobile_number_common"
                                            value="{{ $user?->registerCredit?->mobile_number ?? '' }}"
                                            name="mobile_number" placeholder="Type here">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="statement_email" class="form-label">Invoice/Statement
                                                    Email</label>
                                                <input type="email" class="form-control" id="statement_email"
                                                    name="statement_email"
                                                    value="{{ $user?->registerCredit->statement_email ?? '' }}"
                                                    placeholder="Type here">
                                            </div>
                                        </div>
                                        <!--<div class="row g-3 mt-2">-->
                                        <!--    <div class="col-md-12">-->
                                        <!--        <label for="phone_number" class="form-label">Phone Number</label>-->
                                        <!--        <input type="number" class="form-control" id="phone_number"-->
                                        <!--            name="phone_number"-->
                                        <!--            value="{{ $user?->registerCredit->phone_number ?? '' }}"-->
                                        <!--            placeholder="Type here" required>-->
                                        <!--    </div>-->
                                         
                                        <!--</div>-->
                                    </div>

                                

                                    <div id="section-9" class="dynamic-content d-none">
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-12">
                                                <label for="organization_name" class="form-label">Enter Health
                                                    Organization Name</label>
                                                <input type="text" class="form-control" id="organization_name"
                                                    name="organization_name"
                                                    value="{{ $user?->registerCredit->organization_name ?? '' }}"
                                                    placeholder="Type here">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Placeholder for Dynamic Content -->
                                <div id="dynamic-section"></div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" id="register/creditButton"
                                        class="btn btn-primary">Next</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Files & Media -->
                    <div class="tab-pane fade" id="files_and_media" role="tabpanel"
                        aria-labelledby="files-and-media-tab">
                        <div class="bg-white p-3 p-sm-2rem">
                            <!-- Product Files & Media -->
                            <h5 class="mb-3 pb-3 fs-17 fw-700" style="border-bottom: 1px dashed #e4e5eb;">
                                {{ translate('Delivery Address') }}</h5>
                            {{-- line  - 1 --}}
                            <form method="POST" id="delivery_form">
                                <input type="hidden" name="delivery_id" id="deliveryId"> <!-- Hidden field for editing -->

                                <input type="hidden" name="user_id" id="userId" value="{{ $user->id ?? '' }}">

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label for="post_code" class="form-label">Post Code</label>
                                        <input type="text" class="form-control" id="post_code" name="post_code"
                                            placeholder="Type here"  value="">
                                        <span class="error-message" id="post_code-error"
                                            style="color:red; display:none;">This
                                            field is required.</span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="address1" class="form-label">Address 1</label>
                                        <input type="text" class="form-control" id="address1" name="address1"
                                            placeholder="Type here"  value="">
                                        <span class="error-message" id="address2-error"
                                            style="color:red; display:none;">This
                                            field is required.</span>
                                    </div>


                                </div>
                                {{-- line  - 2 --}}
                                <div class="row g-3 mt-2">

                                    <div class="col-md-6">
                                        <label for="address2" class="form-label">Address 2 </label>
                                        <input type="text" class="form-control" id="address2" name="address2"
                                            placeholder="Type here"  value="">
                                        <span class="error-message" id="address2-error"
                                            style="color:red; display:none;">This
                                            field is required.</span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="address3" class="form-label">Address 3</label>
                                        <input type="text" class="form-control" id="address3" name="address3"
                                            placeholder="Type here"  value="">
                                        <span class="error-message" id="address3-error"
                                            style="color:red; display:none;">This
                                            field is required.</span>
                                    </div>


                                </div>
                                {{--  --}}
                                <div class="row g-3 mt-2">

                                    <div class="col-md-6">
                                        <label for="town" class="form-label"> Town</label>
                                        <input type="text" class="form-control" id="town" name="town"
                                            placeholder="Type here"  value="">
                                        <span class="error-message" id="town-error" style="color:red; display:none;">This
                                            field
                                            is required.</span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                            placeholder="Type here"  value="">
                                        <span class="error-message" id="city-error" style="color:red; display:none;">This
                                            field
                                            is required.</span>
                                    </div>


                                </div>
                                {{-- line  - 4 --}}
                                <div class="row g-3 mt-2">

                                    <div class="col-md-6">
                                        <label for="county" class="form-label">County</label>
                                        <input type="text" class="form-control" id="county" name="county"
                                            placeholder="Type here"  value="">
                                        <span class="error-message" id="county-error"
                                            style="color:red; display:none;">This field
                                            is required.</span>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="country" class="form-label">Country<super>*</super></label>
                                        <select id="country" name="country" class="form-control aiz-selectpicker">
                                            <option value="" disabled selected>Select</option>
                                            @foreach ($country as $country)
                                                <option value="{{ $country->id }}" {{-- {{ isset($user) && $user?->registerCredit?->creditDelivery?->first()?->country == $country->id ? 'selected' : '' }} --}}>
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error-message" id="country-error"
                                            style="color:red; display:none;">This
                                            field is required.</span>
                                    </div>


                                </div>
                                {{-- line  - 5 --}}
                                <br>
                                <button type="button" id="submitButton" val=""
                                    class="btn btn-primary">Save</button>
                                <button type="button" id="updatedeliveryButton" val=""
                                    class="btn btn-primary d-none">Update</button>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" id="headBackButton" class="btn btn-outline-secondary "
                                        style="margin-right:6px;">Back</button>
                                    <button type="button" id="changeStatus" class="btn btn-primary">Submit</button>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Postcode</th>
                                                <th>Address</th>
                                                <th>Town</th>
                                                <th>City</th>
                                                <th>County</th>
                                                <th>Country</th>
                                                <th>Option</th>
                                            </tr>
                                        </thead>
                                        <tbody id="deliveryAddressTableBody">
                                            @foreach ($user?->creditDelivery ?? [] as $delivery)
                                                <tr>
                                                    <td>{{ $delivery->post_code }}</td>
                                                    <td>{{ $delivery->address1 }}</td>
                                                    <td>{{ $delivery->town }}</td>
                                                    <td>{{ $delivery->city }}</td>
                                                    <td>{{ $delivery->county }}</td>
                                                    <td>{{ optional($country->firstWhere('id', $delivery->country))->name }}
                                                        </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-soft-primary btn-icon btn-circle btn-sm editDeliveryAddress"
                                                            data-id="{{ $delivery->id }}">
                                                            <i class="las la-edit"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>





                </div>

            </div>
        </div>
    </div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Treeview js -->
    <script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#treeview").hummingbird();

            var main_id = '{{ old('category_id') }}';
            var selected_ids = [];
            @if (old('category_ids'))
                selected_ids = @json(old('category_ids'));
            @endif
            for (let i = 0; i < selected_ids.length; i++) {
                const element = selected_ids[i];
                $('#treeview input:checkbox#' + element).prop('checked', true);
                $('#treeview input:checkbox#' + element).parents("ul").css("display", "block");
                $('#treeview input:checkbox#' + element).parents("li").children('.las').removeClass("la-plus")
                    .addClass('la-minus');
            }
            if (main_id) {
                $('#treeview input:radio[value=' + main_id + ']').prop('checked', true);
            }
        });

        $('form').bind('submit', function(e) {
            if ($(".action-btn").attr('attempted') == 'true') {
                //stop submitting the form because we have already clicked submit.
                e.preventDefault();
            } else {
                $(".action-btn").attr("attempted", 'true');
            }
            // Disable the submit button while evaluating if the form should be submitted
            // $("button[type='submit']").prop('disabled', true);

            // var valid = true;

            // if (!valid) {
            // e.preventDefault();

            ////Reactivate the button if the form was not submitted
            // $("button[type='submit']").button.prop('disabled', false);
            // }
        });

        $("[name=shipping_type]").on("change", function() {
            $(".flat_rate_shipping_div").hide();

            if ($(this).val() == 'flat_rate') {
                $(".flat_rate_shipping_div").show();
            }

        });

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

        $('input[name="unit_price"]').on('keyup', function() {
            update_sku();
        });

        $('input[name="name"]').on('keyup', function() {
            update_sku();
        });

        function delete_row(em) {
            $(em).closest('.form-group row').remove();
            update_sku();
        }

        function delete_variant(em) {
            $(em).closest('.variant').remove();
        }

        function update_sku() {
            $.ajax({
                type: "POST",
                url: '{{ route('products.sku_combination') }}',
                data: $('#choice_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data);
                    AIZ.uploader.previewGenerate();
                    AIZ.plugins.fooTable();
                    if (data.trim().length > 1) {
                        $('#show-hide-div').hide();
                    } else {
                        $('#show-hide-div').show();
                    }
                }
            });
        }

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });

            update_sku();
        });
    </script>
    <script>
        $(document).ready(function() {
            var hash = document.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
            } else {
                $('.nav-tabs a[href="#general"]').tab('show');
            }

            // Change hash for page-reload
            $('.nav-tabs a').on('shown.bs.tab', function(e) {
                window.location.hash = e.target.hash;
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            var organizationType = document.getElementById("organization_type");
            var dynamicSection = document.getElementById("dynamic-section");

            function updateDynamicContent() {
                var selectedValue = organizationType.value;

                // Clear previous content
                dynamicSection.innerHTML = '';

                // Select the correct section
                if (selectedValue === '1' || selectedValue === '2') {
                    dynamicSection.innerHTML = document.getElementById("section-1-2").innerHTML;
                } else if (['3', '4', '5', '6', '7', '8'].includes(selectedValue)) {
                    dynamicSection.innerHTML = document.getElementById("section-3-8").innerHTML;
                } else if (selectedValue === '9') {
                    dynamicSection.innerHTML = document.getElementById("section-9").innerHTML;
                }
            }

            // Run once on page load to set the correct section for editing
            updateDynamicContent();

            // Change event listener
            organizationType.addEventListener("change", updateDynamicContent);
        });
        document.getElementById('register/creditButton').addEventListener('click', function() {


            const formData = new FormData(document.getElementById('register_credit_form'));


            // Perform AJAX request to send data to the server
            fetch('{{ route('customer_register.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                if (data.message && data.message === 'Email already exists. Please use a different email address.') {
                    // Show SweetAlert if duplicate email is found
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Email',
                        text: 'Email already exists. Please use a different email address.',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    // Move to next tab if no error
                    document.querySelector('#files-and-media-tab').click();
                }
         




                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        document.getElementById('headBackButton').addEventListener('click', function() {
            document.querySelector('#general-tab').click(); // Previous tab pe switch karein
        });
        document.getElementById('submitButton').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('delivery_form'));

            fetch('{{ route('delivery_form.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Delivery address added:", data); // Debug log

                    // Ensure the table body exists
                    const tableBody = document.getElementById('deliveryAddressTableBody');

                    const row = document.createElement('tr');
                    row.innerHTML = `
      
        <td>

        <input type="hidden" name="deliveryAddressId" value="${data.id || ''}">
            ${data.postcode || '-'}</td>
        <td>${data.address1 || '-'}</td>
        <td>${data.town || '-'}</td>
        <td>${data.city || '-'}</td>
        <td>${data.county || '-'}</td>
        <td>${data.country || '-'}</td>
          <td>
            <button class="btn btn-danger btn-sm delete-row-btn" title="Delete">
                <i class="las la-trash"></i>
            </button>
        </td>
    `;
     tableBody.appendChild(row);  
 const deleteButton = row.querySelector('.delete-row-btn');
    deleteButton.addEventListener('click', () => {
    // Confirm deletion

    // Get the ID from the hidden input
    const deliveryAddressId = row.querySelector('input[name="deliveryAddressId"]').value;

    // Send an AJAX request to delete the record from the database
    fetch('{{ route("delivery-address.delete") }}', {
        method: 'POST', // Laravel typically uses POST for such actions
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ deliveryAddressId }), // Include the ID in the body
    })
        .then(response => {
            if (response.ok) {
                console.log(`Row with ID ${deliveryAddressId} deleted successfully`);
                // Remove the row from the table
                row.remove();
            } else {
                console.error('Failed to delete the entry. Check server logs for details.');
            }
        })
        .catch(error => {
            console.error('Error deleting entry:', error);
        });
});

                    // if (data.alert === 'success') {
                    //     Swal.fire({
                    //         title: 'Success!',
                    //         text: data.message || 'Data saved successfully!',
                    //         icon: 'success',
                    //         confirmButtonText: 'Ok'
                    //     }).then(() => {
                    //         window.location.href = "{{ route('customer_credit.list') }}?status=success";            });
                    // } else {
                    //     Swal.fire({
                    //         title: 'Error!',
                    //         text: data.message || 'An error occurred while saving the data.',
                    //         icon: 'error',
                    //         confirmButtonText: 'Try Again'
                    //     });
                    // }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // edit
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".editDeliveryAddress").forEach(button => {
                button.addEventListener("click", function() {
                    let deliveryId = this.dataset.id; // Get the deliveryId from the data attribute
                    console.log(deliveryId);
                    let method = "POST";
                    let url = `{{ url('/admin/delivery_credit_address') }}/${deliveryId}/edit`;

                    fetch(url, {
                            method: method,
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    "meta[name='csrf-token']").getAttribute("content")
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                let delivery = data.data;

                                // Populate form fields with fetched delivery data
                                document.getElementById("deliveryId").value = delivery.id;
                            
                                document.getElementById("post_code").value = delivery
                                    .post_code;
                                document.getElementById("address1").value = delivery
                                    .address1;
                                document.getElementById("address2").value = delivery
                                    .address2;
                                document.getElementById("address3").value = delivery
                                    .address3;
                                document.getElementById("town").value = delivery.town;
                                document.getElementById("city").value = delivery.city;
                                document.getElementById("county").value = delivery
                                    .county;
                                document.getElementById("country").value = delivery
                                    .country;

                                // Update the button text
                                document.getElementById("submitButton").classList.add("d-none"); // Hide Save button
                             document.getElementById("updatedeliveryButton").classList.remove("d-none"); // Hide Save button
               


                            }
                        })
                        .catch(error => console.error("Error fetching data:", error));
                });
            });
        });
// update
document.getElementById('updatedeliveryButton').addEventListener('click', function() {
    let deliveryId = document.getElementById("deliveryId").value;

    const formData = new FormData(document.getElementById('delivery_form'));

    // Perform AJAX request to send data to the server
    fetch(`{{ route('delivery_credit_update', '') }}/${deliveryId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message || 'Delivery updated successfully!',
                confirmButtonColor: '#28a745'
            }).then(() => {
                location.reload(); // Reload the page after user clicks OK
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'An error occurred. Please try again.',
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong! Please check the console.',
            confirmButtonColor: '#d33'
        });
    });
});

// let updateButton = document.getElementById("updatedeliveryButton");
                    
//                     // Ensure button exists before adding event listener
//                     if (updateButton) {
//                         updateButton.addEventListener("click", function () {
//                             let deliveryId = document.getElementById("deliveryId").value; // Get the delivery ID from the hidden input field
                    
//                     if (!deliveryId) {
//                         console.error("Delivery ID not found");
//                         return;
//                     }
                    
//                     let url = `/doctorShop/admin/delivery_credit_update/${deliveryId}`;
//                     let form = document.getElementById("delivery_form");
                   
                    
//                             if (!form) {
//                                 console.error("Form not found");
//                                 return;
//                             }
                    
//                             let formData = new FormData(form);
//                             formData.append("_method", "POST");
                    
//                             fetch(url, {
//                                 method: "POST",
//                                 headers: {
//                                     "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
//                                 },
//                                 body: formData
//                             })
//                             .then(response => response.json())
//                             .then(data => {
//                                 if (data.success) {
//                                     alert(data.message);
//                                     location.reload();
//                                 } else {
//                                     alert("Update failed. Please try again.");
//                                 }
//                             })
//                             .catch(error => console.error("Error updating data:", error));
//                         });
                      
//                     } else {
//                         console.error("Update button not found in DOM");
//                     }
                 
        // change Status
        document.getElementById('changeStatus').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('delivery_form'));

            fetch('{{ route('RegisterStatus') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {


                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        window.location.href = "{{ route('customer_register.list') }}?status=success";
                    })

                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
@endsection
