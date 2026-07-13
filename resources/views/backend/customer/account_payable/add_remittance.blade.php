@extends('backend.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <div class="container ">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Add Remittance</h5>
            </div>
            <div class="modal-body">
                <form action="" id="remittanceButtonForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-4">
                                <label for="customerName" class="form-label">Customer Name</label>
                                <select name="customer_id" class="form-control aiz-selectpicker pos-customer"
                                    id="customerSelect" data-live-search="true">
                                    <option value="">Select Customers</option>
                                    @foreach ($customer as $key => $customer)
                                        {{-- @if (isset($customer->contactInformation)) --}}
                                            <option value="{{ $customer->id }}">
                                                  {{ isset($customer->name) && isset($customer->last_name) ? $customer->name . ' ' .
                    $customer->last_name : 'N/A' }}
                                               
                                            </option>
                                        {{-- @endif --}}
                                    @endforeach
                                        <span id="customerSelect_error" class="text-danger error-message" style="display: none;">Please select a customer.</span>

                                </select>
                            </div>
                            <div class="col-4">

                                <label for="paymentDate" class="form-label">Payment Date</label>
                                <input type="date" id="paymentDate" name="payment_date" class="form-control">
                                    <span id="paymentDate_error" class="text-danger error-message" style="display: none;">Please enter a payment date.</span>


                            </div>
                            <div class="col-4">
                                <label for="paymentRef1" class="form-label">Payment Ref</label>
                                <input type="number" id="paymentRef1" name="remmittan_payment_number" class="form-control">
                                    <span id="paymentRef1_error" class="text-danger error-message" style="display: none;">Please enter a valid payment reference.</span>

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="dueAmount" class="form-label">Total Remittance Value</label>
                                <input type="text" id="dueAmount" name="due_amount" class="form-control" >
                                    <span id="dueAmount_error" class="text-danger error-message" style="display: none;">Please enter the total remittance value.</span>

                            </div>
                            <div class="col-3">
                                <label for="totalInvoice" class="form-label">Total Invoice</label>
                                
                                <input type="text" id="totalInvoice" name="total_invoice" class="form-control" readonly>

                            </div>
                            <div class="col-3">
                                <label for="Remaininginvoice" class="form-label">Remaining Amount</label>
                                <input type="text" id="Remaininginvoice" name="remaining_invoice" class="form-control" readonly>
                            </div>
                          <script>
                            document.getElementById('dueAmount').addEventListener('input', function () {
                                // const dueAmount = parseFloat(this.value) || 0; // Get the dueAmount value or default to 0 if empty
                                const dueAmount = parseFloat(document.getElementById('dueAmount').value) || 0;
                                const totalInvoice = parseFloat(document.getElementById('totalInvoice').value) || 0; // Get totalInvoice value
                                const remainingAmount =  dueAmount - totalInvoice;
                                document.getElementById('Remaininginvoice').value = "-".concat(remainingAmount.toFixed(2));
                                // const formatted = (remainingAmount < 0 ? '-' : '') + Math.abs(remainingAmount).toFixed(2);
                                // console.log("Formatted Remaining:", "-".concat(remainingAmount) );
                            });
                          </script>
                       
                        
                            <div class="col-3">
                                <label for="uploadPdf" class="form-label">Upload a PDF</label>
                                <input type="file" id="uploadPdf" name="payment_pdf" class="form-control" accept=".pdf">
                            </div>
                        </div>
                        <div>
                            <span id="message2" style="display:none; font-weight: bold;"></span>
                            <input type="hidden" name="status" value="" id="status" >
                        </div>
                    </div>
                    <div class="container col-12 ">

                        <div class="form-group">
                            <div class="row">

                                <div class="col-5 mt-4">
                                    <div class="row position-relative">
                                        <div class="col-10">
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control" id="searchInvoice" name="searchInvoice"
                                                    placeholder="{{ translate('Enter Invoice') }}">
                                                <!-- Search Icon -->
                                                <i class="fas fa-search search-icon" onclick="performSearch()" style="position:absolute; right: 20px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #555;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                        </div>
                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>PO Number</th>
                                    <th>Invoice Number</th>
                                    <th>Tracking Number</th>
                                    <th>Payment Due Date</th>
                                    <th>Total Payment Due</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="dataTableBody">
                            </tbody>
                        </table>

                    </div>


                    <button type="button" id="remittanceButton" class="btn btn-primary">Add Remittance</button>
                </form>

            </div>
        </div>
        <div class="container col-9  ">
            <div id="remittanceModal" class="modal" style="display:none; margin-top:10%; width:60%;  margin-left:20%; ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Payment Details Form</h5>
                        <button type="button" class="btn-close" onclick="closeModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="" id="remittanceButtonForm2" method="post" enctype="multipart/form-data">
                            @csrf
                            <!-- Laravel's CSRF Token -->
                            <div class="form-group">
                                <div class="row">
                                    <input type="hidden" id="account_id" name="account_id">
                                    <div class="col-6">
                                        <label for="customerName" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control" id="customerName" name="customer_name"
                                            readonly>

                                    </div>
                                    <div class="col-6">
                                        <label for="customerName" class="form-label">Invoice Number</label>
                                        <input type="text" class="form-control" id="invoiceNumber"
                                            name="invoice_number" readonly>

                                    </div>
                                    <div class="col-6">
                                        <label for="customerName" class="form-label">Po Number</label>
                                        <input type="text" class="form-control" id="po_number" name="po_number"
                                            readonly>
                                    </div>
                                    <div class="col-6">
                                        <label for="customerName" class="form-label">Tracking Number</label>
                                        <input type="text" class="form-control" id="tracking_number"
                                            name="tracking_number" readonly>
                                    </div>

                                </div>

                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="paymentDate1" class="form-label">Payment Date</label>
                                        <input type="date" id="paymentDate1" name="payment_date" class="form-control"
                                            required readonly>
                                                <span id="paymentDate1_error" class="text-danger error-message" style="display: none;">Please enter a payment date.</span>

                                    </div>
                                    <div class="col-6">
                                        <label for="paymentRef" class="form-label">Payment Ref</label>
                                        <input type="number" id="remmittanNumber" name="remmittan_payment_number" class="form-control"
                                            required readonly>
                                                <span id="remmittanNumber_error" class="text-danger error-message" style="display: none;">Please enter a valid payment reference.</span>

                                       
                                    </div>


                                </div>
                            </div>



                            <div class="form-group">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="dueAmount" class="form-label">Due Amount</label>
                                        <input type="text" id="dueeeAmount" name="due_amount" class="form-control"
                                            value="" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label for="paymentAmount" class="form-label">Payment Amount</label>
                                        <input type="number" id="payyymentAmount" name="payment_amount"
                                            class="form-control" required>
                                            <span id="paymentAmount_error" class="text-danger error-message"
                                            style="display: none;">Please enter a Amount.</span>
                                        <span id="message" style="display:none; font-weight: bold;"></span>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="remainingAmount" class="form-label">Remaining Amount</label>
                                        <input type="number" id="remainingggAmount" name="remaining_amount"
                                            class="form-control" readonly>

                                    </div>
                                    <div class="col-6">
                                        <label for="uploadPdf" class="form-label">Upload a PDF</label>
                                        <input type="file" id="uploadPdf" name="payment_pdf" class="form-control"
                                            accept=".pdf">
                                    </div>
                                </div>

                            </div>
                            <script>
                                document.getElementById('payyymentAmount').addEventListener('input', function() {

                                    var dueAmount = parseFloat(document.getElementById('dueeeAmount').value.replace(/[^0-9.]/g, ''));
                                    var paymentAmount = parseFloat(this.value);
                                    var remainingAmountInput = document.getElementById('remainingggAmount');
                                    var messageSpan = document.getElementById('message');

                                    if (isNaN(paymentAmount) || paymentAmount < 0) {
                                        remainingAmountInput.value = dueAmount;
                                        messageSpan.style.display = "none";
                                        return;
                                    }

                                    var remainingAmount = dueAmount - paymentAmount;

                                    if (remainingAmount < 0) {
                                        remainingAmount = remainingAmount;
                                    }

                                    remainingAmountInput.value = remainingAmount.toFixed(2);;

                                    if (paymentAmount === dueAmount) {
                                        messageSpan.textContent = "Payment Confirm";
                                        messageSpan.style.color = "green";
                                    } else if (paymentAmount < dueAmount) {
                                        messageSpan.textContent = "Your order is in exception payment";
                                        messageSpan.style.color = "orange";
                                    } else {
                                        messageSpan.textContent = "Payment amount exceeded";
                                        messageSpan.style.color = "red";
                                    }

                                    messageSpan.style.display = "inline";
                                });
                            </script>
 <div class="d-flex justify-content-around">
    <div class="col-4">
                            <button type="button" id="remittanceButton2" class="btn btn-primary">Add Payment
                                Details</button>
                                  
                                 
                                  </div>
                                  <div class="col-4">
                                    <div class=" bg-primary">
                                        <a href="" id="delivery_note_btn" type="button" class="btn  text-light"><i
                                                class="las la-print"></i>  View delivery Note</a>
                                    </div>
                                    </div>
                                  
                                    <div class="col-4">
                                    <div class="  bg-primary">
                                        <a href="" id="delivery_invoice" type="button" class="btn  text-light"><i
                                                class="las la-print"></i>  View Invoice</a>
                                   
                            
                                </div>
                                </div>
                                </div>
                                                               
                        </form>
                    </div>
                </div>
            </div>
        </div>
        

        {{--  --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Due Date</th>
                    <th>Purchase Order Number</th>
                    <th>Remaining Amount</th>
                    <th>Net Amount</th>
                    <th>Order Status</th>
                </tr>
            </thead>
            <tbody id="dataTableBody11">
            </tbody>
            <tbody>
            </tbody>
        </table>

    </div>
    <script>
        document.getElementById('remittanceButton2').addEventListener('click', function() {
       const paymentDate = document.getElementById('paymentDate1');
    const remmittanNumber = document.getElementById('remmittanNumber');
    const paymentAmount = document.getElementById('payyymentAmount');


    const paymentDateError = document.getElementById('paymentDate1_error');
    const remmittanNumberError = document.getElementById('remmittanNumber_error');
    const paymentAmount_error = document.getElementById('paymentAmount_error');


    let isValid = true;

    // Validate Payment Date
    if (paymentDate.value.trim() === '') {
        paymentDateError.style.display = 'block';
        isValid = false;
    } else {
        paymentDateError.style.display = 'none';
    }

    if (paymentAmount.value.trim() === '') {
        paymentAmount_error.style.display = 'block';
        isValid = false;
    } else {
        paymentAmount_error.style.display = 'none';
    }

    // Validate Payment Reference
    if (remmittanNumber.value.trim() === '') {
        remmittanNumberError.style.display = 'block';
        isValid = false;
    } else {
        remmittanNumberError.style.display = 'none';
    }
        if (!isValid) return;

            const formData = new FormData(document.getElementById('remittanceButtonForm2'));

            fetch('{{ route('payment.reUpdate') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data) {

                        

                        let orderStatus = data.remaining_amount == 0 ? 'Payment Confirmed' : 'Exception Order';
                         let statusColor = data.remaining_amount == 0 ? 'green' : 'red';
                        const newRow = `
                <tr>
                    <td>${data.customer_name}</td>
                    <td>${data.invoice_number}</td>
                    <td>${data.invoice_date}</td>
                    <td>${data.due_date}</td>
                    <td>${data.purchase_order_number}</td>
                    <td>${data.remaining_amount}</td>
                    <td>${data.net_amount}</td>
                     <td><span style="color: ${statusColor}">${orderStatus}</span></td>
                </tr>
            `;

                        document.getElementById('dataTableBody11').insertAdjacentHTML('beforeend', newRow);
                        // document.getElementById('Remaininginvoice').value = data.remaining_amount;
                        const searchInvoice = document.getElementById('searchInvoice');
                        const dataTableBody = document.getElementById('dataTableBody');
                        searchInvoice.value = '';
                        dataTableBody.style.display = 'none'
                        updateTotalInvoice();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
                closeModal();
        });

        function updateTotalInvoice() {
            let total = 0;
            const rows = document.querySelectorAll('#dataTableBody11 tr');

            rows.forEach(row => {
                const netAmountCell = row.querySelector('td:nth-child(7)');
                const netAmount = parseFloat(netAmountCell.textContent) || 0;
                total += netAmount;
            });


            document.getElementById('totalInvoice').value = total.toFixed(2);

            var dueAmount = parseFloat(document.getElementById('dueAmount').value.replace(/[^0-9.]/g, ''));
            var paymentAmount = total.toFixed(2);
            var remainingAmountInput = document.getElementById('Remaininginvoice');
            console.log("dueAmount",dueAmount);
            console.log("paymentAmount",paymentAmount);
            var remainingAmount = dueAmount - paymentAmount;
            remainingAmountInput.value = remainingAmount;
            console.log("remainingAmount",remainingAmount);

             var messageSpan = document.getElementById('message2');

                if (isNaN(paymentAmount) || paymentAmount < 0) {
                    remainingAmountInput.value = dueAmount;
                    messageSpan.style.display = "none";
                    return;
                }


                if (remainingAmount < 0) {
                    remainingAmount = 0;
                }

                remainingAmountInput.value = remainingAmount;

                if (paymentAmount == dueAmount) {
                    messageSpan.textContent = "Payment Confirm";
                    messageSpan.style.color = "green";
                    document.getElementById('status').value = 1;
                } else if (paymentAmount < dueAmount) {
                    messageSpan.textContent = "Your order is in exception payment";
                    messageSpan.style.color = "orange";
                    document.getElementById('status').value = 2;

                } else if (paymentAmount > dueAmount) {
                    messageSpan.textContent = "Payment amount exceeded";
                    messageSpan.style.color = "red";
                    document.getElementById('status').value = 3;

                }

                messageSpan.style.display = "inline";
            
        }

        document.getElementById('customerSelect').addEventListener('change', function() {
            var customerId = this.value;
            // console.log(customerId);
            if (customerId) {
                fetch('{{ route('search-customer') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                        },
                        body: JSON.stringify({
                            customer_id: customerId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // document.getElementById('dueAmount').value = data.due_amount || '';

                    })
                    .catch(error => console.error('Error:', error));
            }
        });
//  remittance
    //     document.getElementById('remittanceButton').addEventListener('click', function() {
    //         let isValid = true;

    // // Get input values
    // let customerSelect = document.getElementById('customerSelect');
    // let paymentDate = document.getElementById('paymentDate');
    // let paymentRef = document.getElementById('paymentRef1');
    // let dueAmount = document.getElementById('dueAmount');
    // let totalInvoice = document.getElementById('totalInvoice');

    // // Clear previous error messages
    // document.querySelectorAll('.error-message').forEach(el => el.remove());

    // // Validation function
    // function showError(inputElement, message) {
    //     let errorSpan = document.createElement('span');
    //     errorSpan.classList.add('error-message');
    //     errorSpan.style.color = 'red';
    //     errorSpan.innerText = message;
    //     inputElement.parentNode.appendChild(errorSpan);
    //     isValid = false;
    // }

    // // Validate Customer Name
    // if (customerSelect.value.trim() === '') {
    //     showError(customerSelect, 'Please select a customer.');
    // }

    // // Validate Payment Date
    // if (paymentDate.value.trim() === '') {
    //     showError(paymentDate, 'Please enter a payment date.');
    // }

    // // Validate Payment Ref (must be a number)
    // if (paymentRef.value.trim() === '') {
    //     showError(paymentRef, 'Please enter a payment reference number.');
    // } else if (isNaN(paymentRef.value)) {
    //     showError(paymentRef, 'Payment reference must be a valid number.');
    // }

    // // Validate Due Amount (must be a number)
    // if (dueAmount.value.trim() === '') {
    //     showError(dueAmount, 'Please enter the total remittance value.');
    // } else if (isNaN(dueAmount.value)) {
    //     showError(dueAmount, 'Remittance value must be a valid number.');
    // }
    // let totalInvoiceValue = parseFloat(totalInvoice.value); // Convert to number

    // if (isNaN(totalInvoiceValue) || totalInvoiceValue <= 0) {
    //     Swal.fire({
    //         icon: 'error',
    //         title: 'Error!',
    //         text: 'Add Invoice Amount greater than 0',
    //         confirmButtonColor: '#d33'
    //     });
    //     return; // Stop execution here if the condition fails
    // }
    // }
    // // Stop submission if validation fails
    // if (!isValid) return;
    //         const formData = new FormData(document.getElementById('remittanceButtonForm'));

    //         fetch('{{ route('remittance.store') }}', {
    //                 method: 'POST',
    //                 headers: {
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
    //                         'content'),
    //                 },
    //                 body: formData
    //             })
    //             .then(response => response.json())
    //             .then(data => {
    //                     // SweetAlert for success
    //                     Swal.fire({
    //                     title: 'Success!',
    //                     text: data.message || 'Data saved successfully!',
    //                     icon: 'success',
    //                     confirmButtonText: 'Ok'
    //                 }).then(() => {
    //                     window.location.href = "{{ url('admin/remittance') }}";
    //                 });
    //             })
    //             .catch(error => {
    //                 console.error('Error:', error);
    //             });
    //     });
    document.getElementById('remittanceButton').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent form submission if validation fails

    let isValid = true;

    // Get input values
    let customerSelect = document.getElementById('customerSelect');
    let paymentDate = document.getElementById('paymentDate');
    let paymentRef = document.getElementById('paymentRef1');
    let dueAmount = document.getElementById('dueAmount');
    let totalInvoice = document.getElementById('totalInvoice');

    // Clear previous error messages
    document.querySelectorAll('.error-message').forEach(el => el.remove());

    // Validation function
    function showError(inputElement, message) {
        let errorSpan = document.createElement('span');
        errorSpan.classList.add('error-message');
        errorSpan.style.color = 'red';
        errorSpan.innerText = message;
        inputElement.parentNode.appendChild(errorSpan);
        isValid = false;
    }

    // Validate Customer Name
    if (customerSelect.value.trim() === '') {
        showError(customerSelect, 'Please select a customer.');
    }

    // Validate Payment Date
    if (paymentDate.value.trim() === '') {
        showError(paymentDate, 'Please enter a payment date.');
    }

    // Validate Payment Ref (must be a number)
    if (paymentRef.value.trim() === '') {
        showError(paymentRef, 'Please enter a payment reference number.');
    } else if (isNaN(paymentRef.value)) {
        showError(paymentRef, 'Payment reference must be a valid number.');
    }

    // Validate Due Amount (must be a number)
    if (dueAmount.value.trim() === '') {
        showError(dueAmount, 'Please enter the total remittance value.');
    } else if (isNaN(dueAmount.value)) {
        showError(dueAmount, 'Remittance value must be a valid number.');
    }

    // Validate Total Invoice Amount (must be greater than 0)
    let totalInvoiceValue = parseFloat(totalInvoice.value); // Convert to number

    if (isNaN(totalInvoiceValue) || totalInvoiceValue <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Add Invoice Amount ',
            confirmButtonColor: '#d33'
        });
        return; // Stop execution here if the condition fails
    }

    // Stop submission if validation fails
    if (!isValid) return;

    // If all conditions are valid, proceed with form submission
    const formData = new FormData(document.getElementById('remittanceButtonForm'));

    fetch('{{ route('remittance.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // SweetAlert for success
            Swal.fire({
                title: 'Success!',
                text: data.message || 'Data saved successfully!',
                icon: 'success',
                confirmButtonText: 'Ok'
            }).then(() => {
                window.location.href = "{{ url('admin/remittance') }}";
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
});


    //     document.getElementById('searchInvoice').addEventListener('change', function() {
    //         var invoiceNumber = this.value;

    //         fetch('{{ route('search-invoice') }}', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
    //                         'content'),
    //                 },
    //                 body: JSON.stringify({
    //                     invoice: invoiceNumber
    //                 })
    //             })
    //             .then(response => response.json())
    //             .then(data => {
    //                 document.getElementById('dataTableBody').innerHTML = '';

    //                 if (data.orders && data.orders.length > 0) {
    //                     const dataTableBody = document.getElementById('dataTableBody');
    //                     searchInvoice.value = '';
    //                     dataTableBody.style.display = ''
    //                     document.getElementById('dataTableBody').innerHTML = '';

    //                     data.orders.forEach(order => {
    //                         const row = `
    //         <tr>
    //             <td>${order.customer_name || 'N/A'}</td>
    //             <td>${order.po_number || 'N/A'}</td>
    //             <td>${order.invoice_number || 'N/A'}</td>
    //             <td>${order.tracking_number || 'N/A'}</td>
    //             <td>${order.payment_due_date || 'N/A'}</td>
    //             <td>${order.due_payment || 'N/A'}</td>
    //             <td>
    //                 <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm" 
    //                    cust-id="${order.id}" 
    //                    val="${order.customer_name}" 
    //                    invo="${order.invoice_number}" 
    //                    po="${order.po_number}" 
    //                    track="${order.tracking_number}" 
    //                    da="${order.due_payment }" 
    //                     onclick="openModal(this)" title="Add Remittance">
    //                     <i class="las la-edit"></i>
    //                 </a>
    //             </td>
    //         </tr>
    //     `;
                            
    //                         document.getElementById('dataTableBody').insertAdjacentHTML('beforeend',
    //                             row);
    //                     });
    //                 } else {
                        
    //                     document.getElementById('dataTableBody').innerHTML = `
    //     <tr>
    //         <td colspan="7" class="text-center">No Data Found</td>
    //     </tr>
    // `;
    //                 }

    //             })
    //             .catch(error => console.error('Error:', error));
    //     });
    
function performSearch() {
    var invoiceNumber = document.getElementById('searchInvoice').value;

    fetch('{{ route('search-invoice') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            invoice: invoiceNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        const dataTableBody = document.getElementById('dataTableBody');
        dataTableBody.innerHTML = '';

        if (data.orders && data.orders.length > 0) {
            data.orders.forEach(order => {
                const row = `
                    <tr>
                        <td>${order.customer_name || 'N/A'}</td>
                        <td>${order.po_number || 'N/A'}</td>
                        <td>${order.invoice_number || 'N/A'}</td>
                        <td>${order.tracking_number || 'N/A'}</td>
                        <td>${order.payment_due_date || 'N/A'}</td>
                        <td>${order.due_payment || 'N/A'}</td>
                        <td>
                            <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm" 
                               cust-id="${order.id}" 
                               val="${order.customer_name}" 
                               invo="${order.invoice_number}" 
                               po="${order.po_number}" 
                               track="${order.tracking_number}" 
                               da="${order.due_payment}" 
                               onclick="openModal(this)" title="Add Remittance">
                                <i class="las la-edit"></i>
                            </a>
                             <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" 
                               cust-id="${order.id}" 
                               onclick="removeModal(this)" title="Remove Remittance">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                 
                `;
                dataTableBody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            dataTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">No Data Found</td>
                </tr>
            `;
        }
    })
    .catch(error => console.error('Error:', error));
}

// Event listeners
document.getElementById('searchInvoice').addEventListener('change', performSearch);

        // 
        function openModal(element) {
            const nameValue = element.getAttribute('val');
            const invoValue = element.getAttribute('invo');
            const poValue = element.getAttribute('po');
            const trackValue = element.getAttribute('track');
            const daValue = element.getAttribute('da');
            const custId = element.getAttribute('cust-id');
            const rNm = document.getElementById("paymentRef1").value;
            const Pdate = document.getElementById("paymentDate").value;
           
                                  document.getElementById("message").innerText = '';
                        document.getElementById("payyymentAmount").value = '';
                        var url = "{{ url('delivery') }}" + "/" + custId;

var deliveryNoteBtn = document.getElementById('delivery_note_btn');

deliveryNoteBtn.setAttribute('href', url);
                        var url = "{{ url('invoice') }}" + "/" + custId;

var deliveryNoteBtn = document.getElementById('delivery_invoice');

deliveryNoteBtn.setAttribute('href', url);

            document.getElementById('customerName').value = nameValue;
            document.getElementById('invoiceNumber').value = invoValue;
            document.getElementById('po_number').value = poValue;
            document.getElementById('tracking_number').value = trackValue;
            document.getElementById('dueeeAmount').value = daValue;
            document.getElementById('account_id').value = custId;
            document.getElementById('remmittanNumber').value = rNm;
            document.getElementById('paymentDate1').value = Pdate;
            document.getElementById('remittanceModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('remittanceModal').style.display = 'none';
        }
        function removeModal(el) {
    // if (confirm("Remove remittance from screen only?")) {
        // Remove the row or element visually (no DB update)
        el.closest('tr').remove(); // if inside <tr>, or use .parentElement
    // }
}
    </script>
@endsection
