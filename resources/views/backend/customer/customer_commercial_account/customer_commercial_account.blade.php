
@extends('backend.layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  .body {
    font-family: Arial, sans-serif;
  }
  .sidebar {
    color: #0056b3;
    min-height: 100vh;
    padding-top: 20px;
  }
  .sidebar .nav-link {
    color: #0056b3;
    font-size: 14px;
    padding: 10px 15px;
  }
  .sidebar .nav-link.active {
      background-color: #dddddd;
  }
  .sidebar .nav-link:hover {
    background-color: #dff2f8;
  }
  .form-section {
    background-color: white;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    display: none; /* Hidden by default */
  }
  .form-section.active {
    display: block; /* Show only the active form */
  }
  .form-label {
    font-size: 14px;
  }
  .form-select {
    font-size: 14px;
  }
  .btn-primary {
    background-color: #007bff;
    border: none;
  }
</style>

{{-- <div class="body bg-light"> --}}

<div class="container-fluid">
<div class="row">

  <!-- Sidebar -->
  <div class="col-md-3 sidebar bg-light">
    <div class="p-3">
      <h5 class="mb-4">Customer Information</h5>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link " href="#" data-form="form1">Customer Details</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-form="form2">Head Office</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-form="form3">Register Office</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-form="form4">Delivery Address</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-form="form5">Contact Information</a>
        </li>
       
        <li class="nav-item">
          <a class="nav-link" href="#" data-form="form7">Shipping Terms</a>
        </li>
       
        <li class="nav-item">
          <a class="nav-link" href="#" data-form="form8">Account Payable</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Main Content -->
  <div class="col-md-9">
    <div class="p-4">
      <!-- Customer Details Form -->
      <div id="form1" class="form-section active">
        <h5 class="mb-4">Customer Details</h5>
        <form id="customerDetailsForm" action="{{ route('customer-details.store') }}" method="post">
            @csrf
            <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">
            <div class="row g-3">
                {{-- Company Name --}}
                  <div class="col-md-6">
                    <label for="Cname" class="form-label">Company Name</label>
                              <input type="text" class="form-control" id="Cname" name="Cname" placeholder="Type here" required value="{{ $customerDetail?->company_name?? '' }}">
                    <span class="error-message" id="companyType-error" style="color:red; display:none;">This field is required.</span>
                </div>
                <!-- Company Type -->
                <div class="col-md-6">
                    <label for="companyType" class="form-label">Company Type</label>
                    <select id="companyType" name="company_type" class="form-select">
                        <option value="" disabled selected>Select</option>
                        <option value="1" {{ isset($customerDetail) && $customerDetail->company_type == '1' ? 'selected' : '' }}>International</option>
                        <option value="2" {{ isset($customerDetail) && $customerDetail->company_type == '2' ? 'selected' : '' }}>Domestic</option>
                    </select>
                    <span class="error-message" id="companyType-error" style="color:red; display:none;">This field is required.</span>
                </div>
        
                <!-- Account Type -->
                <div class="col-md-6">
                    <label for="accountType" class="form-label">Account Type</label>
                    <select id="accountType" name="account_type" class="form-select">
                        <option value="" disabled selected>Select</option>
                        <option value="1" {{ isset($customerDetail) && $customerDetail->account_type == '1' ? 'selected' : '' }}>Credit</option>
                        <option value="2" {{ isset($customerDetail) && $customerDetail->account_type == '2' ? 'selected' : '' }}>Performa</option>
                    </select>
                    <span class="error-message" id="accountType-error" style="color:red; display:none;">This field is required.</span>
                </div>
        
                <!-- Business Structure -->
                <div class="col-md-6">
                    <label for="businessStructure" class="form-label">Business Structure</label>
                    <select id="businessStructure" name="business_structure" class="form-select">
                        <option value="" disabled selected>Select</option>
                        <option value="1" {{ isset($customerDetail) && $customerDetail->business_structure == '1' ? 'selected' : '' }}>Sole Trade</option>
                        <option value="2" {{ isset($customerDetail) && $customerDetail->business_structure == '2' ? 'selected' : '' }}>Partnership</option>
                        <option value="3" {{ isset($customerDetail) && $customerDetail->business_structure == '3' ? 'selected' : '' }}>Limited Company (LTD)</option>
                        <option value="4" {{ isset($customerDetail) && $customerDetail->business_structure == '4' ? 'selected' : '' }}>Limited Liability Partnership (LLP)</option>
                        <option value="5" {{ isset($customerDetail) && $customerDetail->business_structure == '5' ? 'selected' : '' }}>Non-Profit Organization</option>
                    </select>
                    <span class="error-message" id="businessStructure-error" style="color:red; display:none;">This field is required.</span>
                </div>
        
                <!-- Currency -->
                <div class="col-md-6">
                    <label for="currency" class="form-label">Currency</label>
                    <select id="currency" name="currency" class="form-select">
                        <option value="" disabled selected>Select</option>
                        <option value="1" {{ isset($customerDetail) && $customerDetail->currency == '1' ? 'selected' : '' }}>USD</option>
                        <option value="2" {{ isset($customerDetail) && $customerDetail->currency == '2' ? 'selected' : '' }}>EUR</option>
                        <option value="3" {{ isset($customerDetail) && $customerDetail->currency == '3' ? 'selected' : '' }}>GBP</option>
                    </select>
                    <span class="error-message" id="currency-error" style="color:red; display:none;">This field is required.</span>
                </div>
        
                <!-- VAT Rate -->
                <div class="col-md-6">
                    <label for="vatRate" class="form-label">VAT Rate</label>
                    <select id="vatRate" name="vat_rate" class="form-select">
                        <option value="" disabled selected>Select</option>
                        <option value="1" {{ isset($customerDetail) && $customerDetail->vat_rate == '1' ? 'selected' : '' }}>Standard Rate 20%</option>
                        <option value="2" {{ isset($customerDetail) && $customerDetail->vat_rate == '2' ? 'selected' : '' }}>Zero Rate 0%</option>
                    </select>
                    <span class="error-message" id="vatRate-error" style="color:red; display:none;">This field is required.</span>
                </div>
            </div>
        
            <div class="mt-4">
                <button type="button" id="nextButton" class="btn btn-primary px-4">Next</button>
            </div>
        </form>
        
      
      </div>

      <!-- Head Office Form -->
       <div id="form2" class="form-section">
        @include('backend.customer.customer_commercial_account.head office.head_office')
      </div>

      <!-- Placeholder for other forms -->
      <div id="form3" class="form-section">
      @include('backend.customer.customer_commercial_account.customer_registered.registered_office_address')
          </div>
          

      <div id="form4" class="form-section">
       @include('backend.customer.customer_commercial_account.delivery_address.delivery_address')
      </div>
     <div id="form5" class="form-section">
        @include('backend.customer.customer_commercial_account.contact_information.contact_information')
      </div>
      <div id="form6" class="form-section">
        
        @include('backend.customer.customer_commercial_account.customer_login.logins') 

      </div> 
     <div id="form7" class="form-section">
      @include('backend.customer.customer_commercial_account.shipping_terms.shipping_term') 
     </div>
   
      <div id="form8" class="form-section">
        @include('backend.customer.customer_commercial_account.account_payable.account_payable') 
      </div>
    
    </div>
  </div>
</div>
</div>

<script>
   document.addEventListener("DOMContentLoaded", function () {
    const companyTypeSelect = document.getElementById("companyType");
    const selectedValue = companyTypeSelect.value;
    const internationalShipping = document.getElementById("internationalShipping");
    const domesticShipping = document.getElementById("domesticShipping");
    if (selectedValue === "1") { // International
        internationalShipping.style.display = "block";
        domesticShipping.style.display = "none";
    }
    // Show Domestic Shipping Terms
    else if (selectedValue === "2") { // Domestic
        internationalShipping.style.display = "none";
        domesticShipping.style.display = "block";
    }
    companyTypeSelect.addEventListener("change", function () {
        const selectedValue = this.value;
        // Show International Shipping Terms
        if (selectedValue === "1") { // International
            internationalShipping.style.display = "block";
            domesticShipping.style.display = "none";
        }
        // Show Domestic Shipping Terms
        else if (selectedValue === "2") { // Domestic
            internationalShipping.style.display = "none";
            domesticShipping.style.display = "block";
        }
        // Hide all if no valid selection
        // else {
        //     internationalShipping.style.display = "none !important";
        //     domesticShipping.style.display = "none !important";
        // }
    });
});


// JavaScript for switching forms
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', function (e) {
    e.preventDefault();

    // Remove active class from all links
    document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
    this.classList.add('active');

    // Hide all forms
    document.querySelectorAll('.form-section').forEach(form => form.classList.remove('active'));

    // Show the selected form
    const formId = this.getAttribute('data-form');
    document.getElementById(formId).classList.add('active');
  });
});
// function addCustomerIdToUrl(customerId) {
//     // Get the current URL
//     let currentUrl = window.location.href;

//     // Check if the URL already has query parameters
//     if (currentUrl.includes('?')) {
//         // If there are existing query parameters, append customer_id using '&'
//         currentUrl += `&customer_id=${customerId}`;
//     } else {
//         // If no query parameters, add customer_id using '?'
//         currentUrl += `?customer_id=${customerId}`;
//     }

//     // Optionally, you can update the browser's address bar without reloading the page
//     window.history.pushState({ path: currentUrl }, '', currentUrl);

//     // If you want to redirect to the new URL instead of just updating the address bar
//     // window.location.href = currentUrl;
// }

// ajax insert form data
document.getElementById('nextButton').addEventListener('click', function () {
    
    // const errorMessages = document.querySelectorAll('.error-message');
    // errorMessages.forEach(msg => msg.style.display = 'none');

    // const requiredFields = ['companyType','accountType','businessStructure','currency','vatRate'];
    // let formIsValid = true;

    // requiredFields.forEach(field => {
    //     const input = document.getElementById(field);
    //     const errorSpan = document.getElementById(field + '-error');

    //     if (!input.value.trim()) {
    //         formIsValid = false;
    //         input.style.borderColor = 'red';
    //         errorSpan.style.display = 'inline';
    //     } else {
    //         input.style.borderColor = '';
    //     }
    // });

    // if (!formIsValid) {
    //     return;
    // }

    // Get form data
    const formData = new FormData(document.getElementById('customerDetailsForm'));
    

    // Perform AJAX request to send data to the server
    fetch('{{ route("customer-details.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
    

          
            // alert(data.message); 
            // Show success message
            // Hide current form and show the next one
            document.getElementById('form1').classList.remove('active');
            document.getElementById('form2').classList.add('active');
        
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

document.getElementById('submitButton').addEventListener('click', function () {
    // Reset error messages
    // const errorMessages = document.querySelectorAll('.error-message');
    // errorMessages.forEach(msg => msg.style.display = 'none');

    // const requiredFields = ['postcode', 'address1','town','city','county', 'country'];
    // let formIsValid = true;

    // requiredFields.forEach(field => {
    //     const input = document.getElementById(field);
    //     const errorSpan = document.getElementById(field + '-error');

    //     if (!input.value.trim()) {
    //         formIsValid = false;
    //         input.style.borderColor = 'red';
    //         errorSpan.style.display = 'inline';
    //     } else {
    //         input.style.borderColor = '';
    //     }
    // });

    // if (!formIsValid) {
    //     return;
    // }

    const formData = new FormData(document.getElementById('headOfficeForm'));

    fetch('{{ route("head-office.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                // alert(data.message);

                // Check if "Same as Head Office" checkbox is selected
                const sameAsHeadOffice = document.getElementById('sameAsHeadOffice')?.checked;
                if (sameAsHeadOffice) {
                    populateRegisteredOfficeFields(formData);
                }

                // Show Registered Office Form
                document.getElementById('form2').classList.remove('active');
                document.getElementById('form3').classList.add('active');
            } else {
                alert('Failed to save Head Office data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
    document.getElementById('headBackButton').addEventListener('click', function () {
        document.getElementById('form2').classList.remove('active');
        document.getElementById('form1').classList.add('active');
    });
    //register Office
document.getElementById('registerOfficeForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent form from submitting the traditional way
    // Reset error messages
    // const errorMessages = document.querySelectorAll('.error-message');
    // errorMessages.forEach(msg => msg.style.display = 'none');

    // const requiredFields = ['registerPostcode', 'registerAddress1','registerTown','registerCity','registerCounty', 'registerCountry'];
    // let formIsValid = true;

    // requiredFields.forEach(field => {
    //     const input = document.getElementById(field);
    //     const errorSpan = document.getElementById(field + '-error');
    //     if (field === 'registerCountry') {
    //         // Custom validation for select (country)
    //         if (input.value === 'Select') {
    //             formIsValid = false;
    //             input.style.borderColor = 'red';
    //             errorSpan.style.display = 'inline';
    //         } else {
    //             input.style.borderColor = '';
    //         }
    //     } else {

    //     if (!input.value.trim()) {
    //         formIsValid = false;
    //         input.style.borderColor = 'red';
    //         errorSpan.style.display = 'inline';
    //     } else {
    //         input.style.borderColor = '';
    //     }
    // }
    // });

    // if (!formIsValid) {
    //     return;
    // }

    // Get form data
    const formData = new FormData(this);

    // Perform AJAX request to send data to the server
    fetch('{{ route("register-office.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
         
                // alert(data.message);
            
            // Hide Form 3
            document.getElementById('form3').classList.remove('active');
            
            // Show Form 4
            document.getElementById('form4').classList.add('active');
        } else {
            // Optionally handle errors (for example, alerting the user)
            alert('Failed to save data. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
document.getElementById('submitdeliveryButton').addEventListener('click', function () {
    // const errorMessages = document.querySelectorAll('.error-message');
    // errorMessages.forEach(msg => msg.style.display = 'none');

    // const requiredFields = ['deliveryPostcode', 'deliveryAddress1','deliveryTown','deliveryCity','deliveryCounty', 'deliveryCountry'];
    // let formIsValid = true;

    // requiredFields.forEach(field => {
    //     const input = document.getElementById(field);
    //     const errorSpan = document.getElementById(field + '-error');
    //     if (field === 'registerCountry') {
    //         // Custom validation for select (country)
    //         if (input.value === 'Select') {
    //             formIsValid = false;
    //             input.style.borderColor = 'red';
    //             errorSpan.style.display = 'inline';
    //         } else {
    //             input.style.borderColor = '';
    //         }
    //     } else {

    //     if (!input.value.trim()) {
    //         formIsValid = false;
    //         input.style.borderColor = 'red';
    //         errorSpan.style.display = 'inline';
    //     } else {
    //         input.style.borderColor = '';
    //     }
    // }
    // });

    // if (!formIsValid) {
    //     return;
    // }

    // Get form data

    const formData = new FormData(document.getElementById('deliveryAddressForm'));

    // Perform AJAX request to send data to the server
    fetch('{{ route("delivery-address.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
      <td>${data.name || '-'}</td>
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
    console.log(row);
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

    // Reset the form (if exists)
    const form = document.getElementById('deliveryAddressForm');
    if (form) {
        form.reset();
    }
})
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the delivery address.');
    });
});



document.getElementById('nextDeliveryButton').addEventListener('click', function () {
    document.getElementById('form4').classList.remove('active');
    document.getElementById('form5').classList.add('active');
});
document.getElementById('deliveryBackButton').addEventListener('click', function () {
        document.getElementById('form4').classList.remove('active');
        document.getElementById('form3').classList.add('active');
    });
     document.getElementById('contactInformationButton').addEventListener('click', function () {
    // Get form data
    // const errorMessages = document.querySelectorAll('.error-message');
    // errorMessages.forEach(msg => msg.style.display = 'none');
    
    // const requiredFields = ['contactFirstName', 'contactLastName', 'contactEmail', 'contactOfficeNumber', 'contactMobileNumber'];
    // let formIsValid = true;
    
    // requiredFields.forEach(field => {
    //     const input = document.getElementById(field);
    //     const errorSpan = document.getElementById(field + '-error');
        
    //     if (!input.value.trim()) {
    //         formIsValid = false;
    //         input.style.borderColor = 'red';
    //         errorSpan.style.display = 'inline';
    //     } else {
    //         input.style.borderColor = '';
    //     }
    // });

    // if (!formIsValid) {
    //     return;
    // }

    const formData = new FormData(document.getElementById('contactInformationForm'));
    
    // Perform AJAX request to send data to the server
    fetch('{{ route("contact-information.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message || 'An error occurred. Please try again.',
                });
                throw new Error(data.message || 'An error occurred.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.id) {
            document.getElementById('form5').classList.remove('active');
            document.getElementById('form7').classList.add('active');
        }
    })
    // .catch(error => {
    //     console.error('Error:', error);
    // });
});


document.getElementById('contactBackButton').addEventListener('click', function () {
        document.getElementById('form5').classList.remove('active');
        document.getElementById('form4').classList.add('active');
    });
//     document.getElementById('loginButton').addEventListener('click', function () {
//     // Clear previous error messages
//     const errorMessages = document.querySelectorAll('.error-message');
//     errorMessages.forEach(msg => (msg.style.display = 'none'));

//     // List of required fields
//     const requiredFields = ['aname', 'aemail', 'apassword'];
//     let formIsValid = true;

//     // Validate form inputs
//     requiredFields.forEach(field => {
//         const input = document.getElementById(field);
//         const errorSpan = document.getElementById(field + '-error');

//         if (!input.value.trim()) {
//             formIsValid = false;
//             input.style.borderColor = 'red';
//             errorSpan.style.display = 'inline';
//         } else {
//             input.style.borderColor = '';
//         }
//     });

//     if (!formIsValid) {
//         return; // Stop execution if form is invalid
//     }

//     // Retrieve input values
//     const name = document.getElementById('aname').value.trim();
//     const email = document.getElementById('aemail').value.trim();
//     const password = document.getElementById('apassword').value.trim();

//     // Prepare form data
//     const formData = new FormData();
//     formData.append('name', name);
//     formData.append('email', email);
//     formData.append('password', password);

//     // Perform AJAX request to send data to the server
//     fetch('{{ route("login.store") }}', {
//             method: 'POST',
//             headers: {
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//             },
//         body: formData,
//     })
//     .then(response => {
//         if (!response.ok) {
//             return response.json().then(data => {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Oops...',
//                     text: data.message || 'An error occurred. Please try again.',
//                 });
//                 throw new Error(data.message || 'An error occurred.');
//             });
//         }
//         document.getElementById('form6').classList.remove('active');
//         document.getElementById('form7').classList.add('active');
//         return response.json();
//     })
   
//         .catch(error => {
//             console.error('Error:', error);
//             alert('An error occurred. Please try again later.');
//         });
// });


document.getElementById('shippingButton').addEventListener('click', function (e) {
 e.preventDefault();
    console.log('kadkfj');   
    // const errorMessages = document.querySelectorAll('.error-message');
    // errorMessages.forEach(msg => msg.style.display = 'none');

    // const requiredFields = ['order_value'];
    // let formIsValid = true;

    // requiredFields.forEach(field => {
    //     const input = document.getElementById(field);
    //     const errorSpan = document.getElementById(field + '-error');

    //     if (!input.value.trim()) {
    //         formIsValid = false;
    //         input.style.borderColor = 'red';
    //         errorSpan.style.display = 'inline';
    //     } else {
    //         formIsValid = true;
    //         input.style.borderColor = '';
    //     }
    // });
    // if (!formIsValid) {
    //     return;
    // }

    // if(formIsValid) {
        // Get form data
        const form = document.querySelector('#shippingTermForm');
        // if (!form) {
        //     console.error('Form not found');
        //     return;
        // // }
        const formData = new FormData(form);

        
        // Perform AJAX request to send data to the server
        fetch('{{ route("shipping_term.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                const accountPayableForm = document.querySelector('#accountPayableForm');
                if (accountPayableForm) {
                    accountPayableForm.insertAdjacentHTML(
                        'afterbegin',
                        `<input type="hidden" name="customer_id" value="${data.id}" />`
                    );
                } else {
                    console.error('accountPayableForm not found');
                }
    
                // Hide current form and show the next one
                document.getElementById('form7').classList.remove('active');
                document.getElementById('form8').classList.add('active');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });

});

document.getElementById('shippingBackButton').addEventListener('click', function () {
        document.getElementById('form7').classList.remove('active');
        document.getElementById('form5').classList.add('active');
    });
document.getElementById('accountPayableButton').addEventListener('click', function (e) {
    e.preventDefault();
    // const errorMessages = document.querySelectorAll('.error-message');
    // errorMessages.forEach(msg => msg.style.display = 'none');

    // const requiredFields = ['payPostcode', 'payAddress1','payTown','payCity', 'payCountry','payFirstName', 'payLastName','payEmail','payOfficeNumber','payMobileNumber'];
    // let formIsValid = true;

    // requiredFields.forEach(field => {
    //     const input = document.getElementById(field);
    //     const errorSpan = document.getElementById(field + '-error');
    //     if (field === 'payCountry') {
    //         // Custom validation for select (country)
    //         if (input.value === 'Select') {
    //             formIsValid = false;
    //             input.style.borderColor = 'red';
    //             errorSpan.style.display = 'inline';
    //         } else {
    //             input.style.borderColor = '';
    //         }
    //     } else {

    //     if (!input.value.trim()) {
    //         formIsValid = false;
    //         input.style.borderColor = 'red';
    //         errorSpan.style.display = 'inline';
    //     } else {
    //         input.style.borderColor = '';
    //     }
    // }
    // });

    // if (!formIsValid) {
    //     return;
    // }

    // Get form data
    const form = document.querySelector('#accountPayableForm');
    // if (!form) {
    //     console.error('Form not found');
    //     return;
    // }
    const formData = new FormData(form);

    // Perform AJAX request to send data to the server
    fetch('{{ route("account_payable.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.alert === 'success') {
            // SweetAlert for success
            Swal.fire({
            title: 'Success!',
            text: data.message || 'Data saved successfully!',
            icon: 'success',
            confirmButtonText: 'Ok'
        }).then(() => {
            window.location.href = "{{ route('commercial_account_index') }}?status=success";
        });

            // If there’s a form ID in the response, you can handle it, for example:

        } else if (data.alert === 'error') {
            // SweetAlert for error
            Swal.fire({
                title: 'Error!',
                text: data.message || 'An error occurred while saving the data.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        }
    })
    .catch(error => {
        // Handle errors
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
    });
});
document.getElementById('payBackButton').addEventListener('click', function () {
        document.getElementById('form8').classList.remove('active');
        document.getElementById('form7').classList.add('active');
    });
document.getElementById('sameAsHeadOffice').addEventListener('change', function () {
    const isChecked = this.checked;

    if (isChecked) {
        // Populate fields with Head Office data
        const headOfficeData = fetchHeadOfficeData();
        populateRegisteredOfficeFields(headOfficeData);
        // Disable fields (set readOnly)
        toggleRegisteredOfficeFields(true);
    } else {
        // Enable fields and clear values
        toggleRegisteredOfficeFields(false);
        clearRegisteredOfficeFields();
    }
});
document.getElementById('sameAsHeadOffice1').addEventListener('change', function () {
    const isChecked = this.checked;
    console.log("isChecked<<<<<<<", isChecked);
    if (isChecked) {
        // Populate fields with Head Office data
        const headOfficeData = fetchHeadOfficeData();
        const deliveryAddressForm = fetchHeadOfficeData();
        populateDeliveryAddressFields(headOfficeData);

        // Disable fields (set readOnly)
        toggleDeliveryAddressFields(true);
    } else {
        // Enable fields and clear values
        toggleDeliveryAddressFields(false);
        clearDeliveryAddressFields();
    }
});

// Fetch Head Office Data (from delivery address fields in your case)
function fetchHeadOfficeData() {
    return {
        postcode: document.getElementById('postcode')?.value || '',
        address1: document.getElementById('address1')?.value || '',
        address2: document.getElementById('address2')?.value || '',
        address3: document.getElementById('address3')?.value || '',
        town: document.getElementById('town')?.value || '',
        city: document.getElementById('city')?.value || '',
        county: document.getElementById('county')?.value || '',
        country: document.getElementById('country')?.value || '',
    };
}

// Populate Registered Office Fields with fetched data
function populateRegisteredOfficeFields(data) {
    document.getElementById('registerPostcode').value = data.postcode;
    document.getElementById('registerAddress1').value = data.address1;
    document.getElementById('registerAddress2').value = data.address2;
    document.getElementById('registerAddress3').value = data.address3;
    document.getElementById('registerTown').value = data.town;
    document.getElementById('registerCity').value = data.city;
    document.getElementById('registerCounty').value = data.county;
    document.getElementById('registerCountry').value = data.country;
}

// Clear Registered Office Fields (Reset the form)
function clearRegisteredOfficeFields() {
    document.getElementById('registerPostcode').value = '';
    document.getElementById('registerAddress1').value = '';
    document.getElementById('registerAddress2').value = '';
    document.getElementById('registerAddress3').value = '';
    document.getElementById('registerTown').value = '';
    document.getElementById('registerCity').value = '';
    document.getElementById('registerCounty').value = '';
    document.getElementById('registerCountry').value = '';
}

// Toggle Registered Office Fields Enable/Disable State
function toggleRegisteredOfficeFields(readOnly) {
    const fields = [
        'registerPostcode',
        'registerAddress1',
        'registerAddress2',
        'registerAddress3',
        'registerTown',
        'registerCity',
        'registerCounty',
        'registerCountry',
    ];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            element.readOnly = readOnly; // Set the readonly attribute based on checkbox state
        }
    });
}

// Populate Delivery Address Fields
function populateDeliveryAddressFields(data) {
    document.getElementById('deliveryPostcode').value = data.postcode;
    document.getElementById('deliveryAddress1').value = data.address1;
    document.getElementById('deliveryAddress2').value = data.address2;
    document.getElementById('deliveryAddress3').value = data.address3;
    document.getElementById('deliveryTown').value = data.town;
    document.getElementById('deliveryCity').value = data.city;
    document.getElementById('deliveryCounty').value = data.county;
    document.getElementById('deliveryCountry').value = data.country;
}

// Clear Delivery Address Fields
function clearDeliveryAddressFields() {
    document.getElementById('deliveryPostcode').value = '';
    document.getElementById('deliveryAddress1').value = '';
    document.getElementById('deliveryAddress2').value = '';
    document.getElementById('deliveryAddress3').value = '';
    document.getElementById('deliveryTown').value = '';
    document.getElementById('deliveryCity').value = '';
    document.getElementById('deliveryCounty').value = '';
    document.getElementById('deliveryCountry').value = '';
}

// Toggle Delivery Address Fields Enable/Disable State
function toggleDeliveryAddressFields(readOnly) {
    const fields = [
        'postcode',
        'address1',
        'address2',
        'address3',
        'town',
        'city',
        'county',
        'country',
    ];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            element.readOnly = readOnly; // Set the readonly attribute based on checkbox state
        }
    });
}




// Checkbox change listener
// document.getElementById('sameAsHeadOffice').addEventListener('change', function () {
//     const isChecked = this.checked;

//     // Head Office form data fetch
//     const postcode = document.getElementById('headOfficePostcode').value;
//     const address1 = document.getElementById('headOfficeAddress1').value;
//     const address2 = document.getElementById('headOfficeAddress2').value;
//     const address3 = document.getElementById('headOfficeAddress3').value;
//     const town = document.getElementById('headOfficeTown').value;
//     const city = document.getElementById('headOfficeCity').value;
//     const county = document.getElementById('headOfficeCounty').value;
//     const country = document.getElementById('headOfficeCountry').value;
//     console.log(postcode);
    

//     if (isChecked) {
//         // Populate Registered Office fields
//         document.getElementById('registerPostcode').value = postcode;
//         document.getElementById('registerAddress1').value = address1;
//         document.getElementById('registerAddress2').value = address2;
//         document.getElementById('registerAddress3').value = address3;
//         document.getElementById('registerTown').value = town;
//         document.getElementById('registerCity').value = city;
//         document.getElementById('registerCounty').value = county;
//         document.getElementById('registerCountry').value = country;

//         // Disable inputs
//         document.getElementById('registerPostcode').disabled = true;
//         document.getElementById('registerAddress1').disabled = true;
//         document.getElementById('registerAddress2').disabled = true;
//         document.getElementById('registerAddress3').disabled = true;
//         document.getElementById('registerTown').disabled = true;
//         document.getElementById('registerCity').disabled = true;
//         document.getElementById('registerCounty').disabled = true;
//         document.getElementById('registerCountry').disabled = true;
//     } else {
//         // Enable inputs for manual data entry
//         document.getElementById('registerPostcode').disabled = false;
//         document.getElementById('registerAddress1').disabled = false;
//         document.getElementById('registerAddress2').disabled = false;
//         document.getElementById('registerAddress3').disabled = false;
//         document.getElementById('registerTown').disabled = false;
//         document.getElementById('registerCity').disabled = false;
//         document.getElementById('registerCounty').disabled = false;
//         document.getElementById('registerCountry').disabled = false;

//         // Clear inputs if unchecked
//         document.getElementById('registerPostcode').value = '';
//         document.getElementById('registerAddress1').value = '';
//         document.getElementById('registerAddress2').value = '';
//         document.getElementById('registerAddress3').value = '';
//         document.getElementById('registerTown').value = '';
//         document.getElementById('registerCity').value = '';
//         document.getElementById('registerCounty').value = '';
//         document.getElementById('registerCountry').value = '';
//     }
// });




</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

