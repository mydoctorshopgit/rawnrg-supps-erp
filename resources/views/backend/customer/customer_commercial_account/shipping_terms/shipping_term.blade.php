<h5 class="mb-4">Shipping Terms</h5>
<form id="shippingTermForm">
    <input type="hidden" name="customer_id" id="customerId" value="{{ $customerDetail->id ?? '' }}">
    
    <!-- International Shipping Terms -->
    <div  class="row mb-3">
        <div class="col-md-6">
            <label for="order_value" class="form-label">Minimum Order Value</label>
            <input type="text" class="form-control" id="order_value" name="order_value" placeholder="E0" required value="{{ $customerDetail?->shippingTerms?->first()?->order_value ?? '' }}">
            <span class="error-message" id="order_value-error" style="color:red; display:none;">This field is required.</span>
        </div>
        
        <div class="col-md-6" id="internationalShipping"  style="display: none;">
            <label for="shippingTerms" class="form-label">Shipping Terms</label>
            <select class="form-select" id="shippingTerms" name="internationalShipping" required>
                <option value="" disabled selected>Select</option>
                <option value="1" {{isset($customerDetail->shippingTerms) && isset($customerDetail->shippingTerms->first()->international_shipping_term)  && $customerDetail->shippingTerms->first()->international_shipping_term == '1' ? 'selected' : '' }}>
                    Ex-Works (EXW)
                </option>
                <option value="2" {{ isset($customerDetail->shippingTerms) && isset($customerDetail->shippingTerms->first()->international_shipping_term)  && $customerDetail->shippingTerms->first()->international_shipping_term == '2' ? 'selected' : '' }}>
                    Free Carrier (FCA)
                </option>
                <option value="3" {{isset($customerDetail->shippingTerms) && isset($customerDetail->shippingTerms->first()->international_shipping_term)  && $customerDetail->shippingTerms->first()->international_shipping_term == '3' ? 'selected' : '' }}>
                    Free On Board (FOB)
                </option>
                <option value="4" {{isset($customerDetail->shippingTerms) && isset($customerDetail->shippingTerms->first()->international_shipping_term)  && $customerDetail->shippingTerms->first()->international_shipping_term == '4' ? 'selected' : '' }}>
                    Cost And Freight (CNF)
                </option>
                <option value="5" {{ isset($customerDetail->shippingTerms) && isset($customerDetail->shippingTerms->first()->international_shipping_term)  && $customerDetail->shippingTerms->first()->international_shipping_term == '5' ? 'selected' : '' }}>
                    Cost, Insurance, And Freight (CIF)
                </option>
                <option value="6" {{ isset($customerDetail->shippingTerms) && isset($customerDetail->shippingTerms->first()->international_shipping_term)  && $customerDetail->shippingTerms->first()->international_shipping_term == '6' ? 'selected' : '' }}>
                    Delivered at Place Unloaded (DPU)
                </option>
                <option value="7" {{ isset($customerDetail->shippingTerms) && isset($customerDetail->shippingTerms->first()->international_shipping_term)  && $customerDetail->shippingTerms->first()->international_shipping_term == '7' ? 'selected' : '' }}>
                    Delivery Duty Unpaid (DDU)
                </option> 
         
            
            </select>
            <span class="error-message" id="shippingTerms-error" style="color:red; display:none;">This field is required.</span>
        </div>
   
      
        <div class="col-md-6" id="domesticShipping"  style="display: none;">
            <label for="delivary_charges" class="form-label">Delivery Charges</label>
            <input type="text" class="form-control" id="delivary_charges" name="delivary_charges" placeholder="E0" required value="{{ $customerDetail?->shippingTerms?->first()?->delivary_charges ?? '' }}">
            <span class="error-message" id="delivary_charges-error" style="color:red; display:none;">This field is required.</span>
        </div>
    </div>
    
    <div class="d-flex justify-content-end">
        <button type="button" id="shippingBackButton" class="btn btn-secondary me-2">Back</button>
        <button type="button" id="shippingButton" class="btn btn-primary">Next</button>
    </div>
</form>
