<h5 class="mb-4">Delivery Address</h5>

<form id="deliveryAddressForm">
    @csrf
    <input type="hidden" name="delivery_id" id="deliveryId"> <!-- Hidden field for editing -->
  <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="sameAsHeadOffice1" />
        <label class="form-check-label" for="sameAsHeadOffice1">Select if address is the same as 'Head Office'.</label>
    </div>

    <div class="row mb-3">
            <div class="col-md-6">
            <label for="deliveryName" class="form-label">Delivery Name</label>
            <input type="text" class="form-control" id="deliveryName" name="delivery_name" placeholder="Type here" required>
        </div>

        <div class="col-md-6">
            <label for="postcode" class="form-label">Post Code</label>
            <input type="text" class="form-control" id="deliveryPostcode" name="postcode" placeholder="Type here" required>
        </div>
      
    </div>
    <div class="row mb-3">

  <div class="col-md-12">
            <label for="address1" class="form-label">Address 1</label>
            <input type="text" class="form-control" id="deliveryAddress1" name="address1" placeholder="Type here" required>
        </div>
        </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="address2" class="form-label">Address 2</label>
            <input type="text" class="form-control" id="deliveryAddress2" name="address2" placeholder="Type here">
        </div>
        <div class="col-md-6">
            <label for="address3" class="form-label">Address 3</label>
            <input type="text" class="form-control" id="deliveryAddress3" name="address3" placeholder="Type here">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="town" class="form-label">Town</label>
            <input type="text" class="form-control" id="deliveryTown" name="town" placeholder="Type here" required>
        </div>
        <div class="col-md-6">
            <label for="city" class="form-label">City</label>
            <input type="text" class="form-control" id="deliveryCity" name="city" placeholder="Type here" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="county" class="form-label">County</label>
            <input type="text" class="form-control" id="deliveryCounty" name="county" placeholder="Type here" required>
        </div>
        <div class="col-md-6">
            <label for="country" class="form-label">Country*</label>
            <select class="form-select" id="deliveryCountry" name="country" >
                <option value="" disabled selected>Select</option>
                @foreach ($country as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <button type="button" id="submitdeliveryButton" val="" class="btn btn-primary">Save</button>
    <button type="button" id="updatedeliveryButton" val="" class="btn btn-primary d-none">Update</button>

    <div class="d-flex justify-content-end mb-3">
        <button type="button" id="deliveryBackButton" class="btn btn-outline-secondary me-3">Back</button>
        <button type="button" id="nextDeliveryButton" class="btn btn-primary">Next</button>
    </div>
</form>

<div class="card-body">
    <table class="table">
        <thead>
            <tr>
                <th>DeliveryName</th>
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
            @foreach ($customerDetail->deliveryAddress ?? [] as $delivery)
                <tr>
                    <td>{{ $delivery->delivery_name }}</td>
                    <td>{{ $delivery->postcode }}</td>
                    <td>{{ $delivery->address1 }}</td>
                    <td>{{ $delivery->town }}</td>
                    <td>{{ $delivery->city }}</td>
                    <td>{{ $delivery->county }}</td>
                    <td>{{ optional($country->firstWhere('id', $delivery->country))->name }}</</td>
                    <td>
                        <button type="button" class="btn btn-soft-primary btn-icon btn-circle btn-sm editDeliveryAddress" data-id="{{ $delivery->id }}">
                            <i class="las la-edit"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".editDeliveryAddress").forEach(button => {
        button.addEventListener("click", function () {
            let deliveryId = this.dataset.id; // Get the deliveryId from the data attribute
            console.log(deliveryId);
            let method = "POST";
            let url = `{{ url('/admin/delivery-address') }}/${deliveryId}/edit`;

            fetch(url, {
                    method: method,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let delivery = data.data;
                        
                        // Populate form fields with fetched delivery data
                        document.getElementById("deliveryId").value = delivery.id;
                        document.getElementById("deliveryName").value = delivery.delivery_name;
                        document.getElementById("deliveryPostcode").value = delivery.postcode;
                        document.getElementById("deliveryAddress1").value = delivery.address1;
                        document.getElementById("deliveryAddress2").value = delivery.address2;
                        document.getElementById("deliveryAddress3").value = delivery.address3;
                        document.getElementById("deliveryTown").value = delivery.town;
                        document.getElementById("deliveryCity").value = delivery.city;
                        document.getElementById("deliveryCounty").value = delivery.county;
                        document.getElementById("deliveryCountry").value = delivery.country;
                        
                        // Update the button text
                        document.getElementById("submitdeliveryButton").classList.add("d-none"); // Hide Save button
                        document.getElementById("updatedeliveryButton").classList.remove("d-none"); // Hide Save button

                    
                    }
                })
                .catch(error => console.error("Error fetching data:", error));
        });
    });


   
    let updateButton = document.getElementById("updatedeliveryButton");

// Ensure button exists before adding event listener
if (updateButton) {
    updateButton.addEventListener("click", function () {
        let deliveryId = document.getElementById("deliveryId").value; // Get the delivery ID from the hidden input field

if (!deliveryId) {
    console.error("Delivery ID not found");
    return;
}

        let url = `/admin/delivery-update/${deliveryId}`;
        let form = document.getElementById("deliveryAddressForm");

        if (!form) {
            console.error("Form not found");
            return;
        }

        let formData = new FormData(form);
        formData.append("_method", "POST");

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content")
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Update failed. Please try again.");
            }
        })
        .catch(error => console.error("Error updating data:", error));
    });
  
} else {
    console.error("Update button not found in DOM");
}
});
</script>
