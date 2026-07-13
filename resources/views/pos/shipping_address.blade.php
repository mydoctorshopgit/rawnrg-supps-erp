<div class="">
    <input type="text" id="addressSearch" class="form-control mb-3" placeholder="Search by Name...">
 
    <div id="addressList">
     
        @foreach (\App\Models\CreditDelivery::where('credit_id',"=",$user_id)->get() as $key => $address)
            <label class="aiz-megabox d-block bg-white" style="display:block">
                <input type="radio" name="address_id" value="{{ $address->id }}" required>
                <span class="d-flex p-3 pad-all aiz-megabox-elem">
                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                    <span class="flex-grow-1 pl-3 pad-lft">
                        <div>
                            <span class="alpha-6">{{ translate('Name') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->delivery_name ?? '' }}</span>
                        </div>
                      <div>
                            <span class="alpha-6">{{ translate('Address') }}:</span>
                            <span class="strong-600 ml-2 ">{{ $address->address1 }}</span><br>
                            <span class="strong-600 ml-2">{{ $address->address2 }}</span><br>
                            <span class="strong-600 ml-2">{{ $address->address1 }}</span>
                    </div>
                        <div>
                            <span class="alpha-6">{{ translate('Postal Code') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->post_code }}</span>
                        </div>
                        <div>
                            <span class="alpha-6">{{ translate('City') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->town }} - {{ $address->city }}</span>
                        </div>
                          <div>
                            <span class="alpha-6">{{ translate('County') }}:</span>
                            <span class="strong-600 ml-2">{{ $address->county }}</span>
                        </div>
                          <div>
                            <span class="alpha-6">{{ translate('Country') }}:</span>
<span class="strong-600 ml-2">  {{ $address->countries->name ?? $address->country }} </span>
                        </div>
                    </span>
                </span>
            </label>
        @endforeach
    </div>

    <input type="hidden" id="customer_id" value="{{$user_id}}">

    <div class="" onclick="add_new_address()">
        <div class="border p-3 rounded mb-3 bord-all pad-all c-pointer text-center bg-white">
            <i class="fa fa-plus fa-2x"></i>
            <div class="alpha-7">{{ translate('Add New Address') }}</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $("#addressSearch").on("keyup", function () {
        var query = $(this).val();
        var customer_id = $("#customer_id").val();

        $.ajax({
            url: "{{ route('search.delivery.address') }}", 
            type: "GET",
            data: { query: query, customer_id: customer_id },
            success: function (response) {
                let addressList = $("#addressList");
                addressList.empty(); // Clear previous results

                if (response.length > 0) {
                    response.forEach(function (address) {
                        let addressHtml = `
                            <label class="aiz-megabox d-block bg-white" style="display:block">
                                <input type="radio" name="address_id" value="${address.id}" required>
                                <span class="d-flex p-3 pad-all aiz-megabox-elem">
                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                    <span class="flex-grow-1 pl-3 pad-lft">
                                        <div><span class="alpha-6">Name:</span> <span class="strong-600 ml-2">${address.delivery_name ?? ''}</span></div>
                                        <div><span class="alpha-6">Address:</span> <span class="strong-600 ml-2">${address.address1}</span> <br><span class="strong-600 ml-2">${address.address2}</span><br><span class="strong-600 ml-2">${address.address3}</span></div>
                                        <div><span class="alpha-6">Postal Code:</span> <span class="strong-600 ml-2">${address.postcode}</span></div>
                                        <div><span class="alpha-6">City:</span> <span class="strong-600 ml-2">${address.town} - ${address.city}</span></div>
                             <div><span class="alpha-6">County:</span> <span class="strong-600 ml-2">${address.county}</span></div>
                             <div><span class="alpha-6">Country:</span> <span class="strong-600 ml-2">${address.country_name}</span></div>
                                    </span>
                                </span>
                            </label>
                        `;
                        addressList.append(addressHtml);
                    });
                } else {
                    addressList.html('<p class="text-center text-muted">No results found</p>');
                }
            }
        });
    });
});

</script>
