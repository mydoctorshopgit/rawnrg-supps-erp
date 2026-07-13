@extends('backend.layouts.app')
@section('content')

<style>
    .body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .card {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        flex: 0 0 48%; /* Each card takes up 48% of the row */
        margin: 1%;
        padding: 20px;
    }
    .card table {
        width: 100%;
        border-collapse: collapse;
    }
    .card th, .card td {
        padding: 10px;
        vertical-align: top;
    }
    .text-primary {
        font-weight: bold;
        color: #3390F3;
    }
</style>

<div class="body">
    <h2>Commercial Account Detail</h2>

    <!-- First Row: Two Cards -->
    <div class="row">
        <!-- Customer Information Card -->
        <div class="card">
            <table>
              <tr>
                    <th colspan="2" class="section-title">Customer Detail</th>
                </tr>
                   <tr>
                    <td class="text-primary">Company Name:</td>
                    <td>
                       {{$customerDetail->company_name}}
                    </td>
                <tr>
                    <td class="text-primary">Company Type:</td>
                    <td>
                        @if(isset($customerDetail) && $customerDetail->company_type == '1') 
                            International
                        @elseif (isset($customerDetail) && $customerDetail->company_type == '2')
                            Domestic
                        @else
                            Cannot find any company type
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-primary">Account Type:</td>
                    <td>
                        @if(isset($customerDetail) && $customerDetail->account_type == '1') 
                            Credit
                        @elseif (isset($customerDetail) && $customerDetail->account_type == '2')
                            Performa
                        @else
                            Cannot find any account type
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-primary">Business Structure:</td>
                    <td>
                        @if(isset($customerDetail) && $customerDetail->business_structure == '1') 
                        Sole Trade
                       @elseif (isset($customerDetail) && $customerDetail->business_structure == '2')
                       Partnership
                       @elseif(isset($customerDetail) && $customerDetail->business_structure == '3') 
                       Limited Company (LTD)
                      @elseif (isset($customerDetail) && $customerDetail->business_structure == '4')
                      Limited Liability Partnership(LLP)
                      @elseif (isset($customerDetail) && $customerDetail->business_structure == '5')
                      Non-Profit Organization
                       @else
                Cannot find any business structure type
                @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-primary">Currency:</td>
                    <td>
                        @if(isset($customerDetail) && $customerDetail->currency == '1') 
            USD
           @elseif (isset($customerDetail) && $customerDetail->currency == '2')
           EUR
           @elseif(isset($customerDetail) && $customerDetail->currency == '3') 
           GBP
        
           @else
    Cannot find any currency
    @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-primary">VAT Rate:</td>
                    <td>
                        @if(isset($customerDetail) && $customerDetail->vat_rate == '1') 
            Standard Rate 20%       
           @elseif(isset($customerDetail) && $customerDetail->vat_rate == '2') 
           Zero Rate 0%
        
           @else
    Cannot find any currency
    @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Head Office Card -->
        <div class="card">
            <table>
                <tr>
                    <th colspan="2" class="section-title">Head Office</th>
                </tr>
                <tr>
                    <td class="text-primary">Head Office Address:</td>
                    <td>{{ $customerDetail->headOffice->first()->address1 ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Post Code:</td>
                    <td>{{ $customerDetail->headOffice->first()->postcode ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Town:</td>
                    <td>{{ $customerDetail->headOffice->first()->town ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">City:</td>
                    <td>{{ $customerDetail->headOffice->first()->city ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">County:</td>
                    <td>{{ $customerDetail->headOffice->first()->county ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Country:</td>
                    <td>{{ $customerDetail->headOffice[0]->countries->name ?? 'Not Available' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Second Row: Two Cards -->
    <div class="row">
        <!-- Register Office Address Card -->
        <div class="card">
            <table>
                <tr>
                    <th colspan="2" class="section-title">Register Office Address</th>
                </tr>
              
                <tr>
                    <td class="text-primary">Register Office Address:</td>
                    <td>{{ $customerDetail->registerOffice->first()->address1 ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Post Code:</td>
                    <td>{{ $customerDetail->registerOffice->first()->postcode ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Town:</td>
                    <td>{{ $customerDetail->registerOffice->first()->town ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">City:</td>
                    <td>{{ $customerDetail->registerOffice->first()->city ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">County:</td>
                    <td>{{ $customerDetail->registerOffice->first()->county ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Country:</td>
                    <td>{{ $customerDetail->registerOffice[0]->countries->name ?? 'Not Available' }}</td>
                </tr>
            </table>
        </div>

        <!-- Contact Information Card -->
        <div class="card">

            <table>
                <tr>
                    <th colspan="2" class="section-title">Contact Information</th>
                </tr>
                <tr>
                    <td class="text-primary">First Name:</td>
                    <td>{{ $customerDetail->contactInformation->first_name ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Last Name:</td>
                    <td>{{ $customerDetail->contactInformation->last_name ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Email:</td>
                    <td>{{ $customerDetail->contactInformation->email ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Office Number:</td>
                    <td>{{ $customerDetail->contactInformation->office_number ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Mobile Number:</td>
                    <td>{{ $customerDetail->contactInformation->mobile_number ?? 'Not Available' }}</td>
                </tr>
            </table>
        </div>
        
        <div class="card">

            <table>
                <tr>
                    <th colspan="2" class="section-title">Shipping Term</th>
                </tr>
                <tr>
                    <td class="text-primary">Shipping:</td>
                    <td>
                        @if(isset($customerDetail) && $customerDetail->company_type == '1') 
                        International
                    @elseif (isset($customerDetail) && $customerDetail->company_type == '2')
                        Domestic
                    @else
                        Cannot find any company type
                    @endif
                                    </td>
                </tr>
                <tr>
                    <td class="text-primary">Order Value:</td>
                    <td>{{ $customerDetail->shippingTerms->first()->order_value ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Delivery Charges:</td>
                    <td>{{ $customerDetail->shippingTerms->first()->delivary_charges ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">International Shipping Term:</td>
                    <td>
                        @if(isset($customerDetail->shippingTerms->first()->international_shipping_term) && $customerDetail->shippingTerms->first()->international_shipping_term == '1') 
                        Ex-Works (EXW)
                        @elseif(isset($customerDetail->shippingTerms->first()->international_shipping_term) && $customerDetail->shippingTerms->first()->international_shipping_term == '2') 
                        Free Carrier (FCA)
                       @elseif(isset($customerDetail->shippingTerms->first()->international_shipping_term) && $customerDetail->shippingTerms->first()->international_shipping_term == '3') 
                       Free On Board (FOB)
                       @elseif(isset($customerDetail->shippingTerms->first()->international_shipping_term) && $customerDetail->shippingTerms->first()->international_shipping_term == '4') 
                       Cost And Freight (CNF)
                       @elseif(isset($customerDetail->shippingTerms->first()->international_shipping_term) && $customerDetail->shippingTerms->first()->international_shipping_term == '5') 
                       Cost, Insurance, And Freight (CIF)
                       @elseif(isset($customerDetail->shippingTerms->first()->international_shipping_term) && $customerDetail->shippingTerms->first()->international_shipping_term == '6') 
                       Delivered at Place Unloaded (DPU)
                       @elseif(isset($customerDetail->shippingTerms->first()->international_shipping_term) && $customerDetail->shippingTerms->first()->international_shipping_term == '7') 
                       Delivery Duty Unpaid (DDU)
                       @else
                Cannot find any International shipping term
                @endif
                    </td>
                </tr>
              
            </table>
        </div>
      
        <div class="card">

            <table>
                <tr>
                    <th colspan="2" class="section-title">Delivery Office Address</th>
                </tr>
                <tr>
                    <th class="text-primary">Delivery Office Address:</th>
                    <th class="text-primary">Post Code:</th>
                    <th class="text-primary">Town:</th>
                    <th class="text-primary">City:</th>
                    <th class="text-primary">County:</th>
                    <th class="text-primary">Country:</th>
                </tr>
              @foreach ($customerDelivaryDetail as $customer)
    <tr>
        <td>{{ $customer->address1 }}</td>
        <td>{{ $customer->postcode }}</td>
        <td>{{ $customer->town }}</td>
        <td>{{ $customer->city }}</td>
        <td>{{ $customer->county }}</td>
        <td>{{ $customer->countries->name ?? 'Not Available' }}</td>
    </tr>
@endforeach

               
            </table>
        </div>
        <div class="card">
            <table>
                <tr>
                    <th colspan="2" class="section-title">Account Payable</th>
                </tr>
                <tr>
                    <td class="text-primary">First Name:</td>
                    <td>{{ $customerDetail->accountPayable->first()->first_name ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Last Name:</td>
                    <td>{{ $customerDetail->accountPayable->first()->last_name ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Email:</td>
                    <td>{{ $customerDetail->accountPayable->first()->email ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Office Number:</td>
                    <td>{{ $customerDetail->accountPayable->first()->office_number ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Mobile Number:</td>
                    <td>{{ $customerDetail->accountPayable->first()->mobile_number ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Confirmation Email:</td>
                    <td>{{ $customerDetail->accountPayable->first()->confirmation_email ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Statement Email:</td>
                    <td>{{ $customerDetail->accountPayable->first()->statement_email ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Register Office Address:</td>
                    <td>{{ $customerDetail->accountPayable->first()->address1 ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Post Code:</td>
                    <td>{{ $customerDetail->accountPayable->first()->post_code ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Town:</td>
                    <td>{{ $customerDetail->accountPayable->first()->town ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">City:</td>
                    <td>{{ $customerDetail->accountPayable->first()->city ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Country:</td>
                    <td>{{ $customerDetail->accountPayable[0]->countries->name ?? 'Not Available' }}</td>
                    
                </tr>
                <tr>
                    <td class="text-primary">Account Name:</td>
                    <td>{{ $customerDetail->accountPayable->first()->account_name ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Bank Name:</td>
                    <td>{{ $customerDetail->accountPayable->first()->bank_name ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Short Code:</td>
                    <td>{{ $customerDetail->accountPayable->first()->short_code ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">IBAN:</td>
                    <td>{{ $customerDetail->accountPayable->first()->iban ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Swift Code:</td>
                    <td>{{ $customerDetail->accountPayable->first()->swift_code ?? 'Not Available' }}</td>
                </tr>
                <tr>
                    <td class="text-primary">Account Number:</td>
                    <td>{{ $customerDetail->accountPayable->first()->account_number ?? 'Not Available' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection
