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
    <h2>Register Account Detail</h2>

    <!-- First Row: Two Cards -->
    <div class="row">
        <!-- Customer Information Card -->
        <div class="card">
            <table>
              <tr>
                    <th colspan="2" class="section-title">Personal Detail</th>
                </tr>
                
              
                <tr>
                    <td class="text-primary">First Name:</td>
                    <td>
                       {{$User->name}}
                    </td>
                   </tr>
                   @if(!empty($User?->last_name))
                   <tr>
                    <td class="text-primary">Last Name:</td>
                    <td>
                       {{$User->last_name}}
                    </td>
                   </tr>
                   @endif
                   <tr>
                    <td class="text-primary">Email:</td>
                    <td>
                       {{$User->email}}
                    </td>
                   </tr>
                    @if(!empty($User?->registerCredit->company_name))
                   <tr>
                    <td class="text-primary">Business Name:</td>
                    <td>
                       {{$User->registerCredit->company_name}}
                    </td>
                   </tr>
                    @endif
                   <tr>
                    <td class="text-primary">Organization Type</td>
                    <td>
                        @if(isset($User) && $User?->registerCredit->organization_type == '1') 
                        GP Surgery
                           @elseif (isset($User) && $User?->registerCredit->organization_type == '2')
                           NHS Hospitals
                            @elseif (isset($User) && $User?->registerCredit->organization_type == '3')
                            Private Clinic
                            @elseif (isset($User) && $User?->registerCredit->organization_type == '4')
                            Private Hospitals
                            @elseif (isset($User) && $User?->registerCredit->organization_type == '5')
                            Dental Practice
                            @elseif (isset($User) && $User?->registerCredit->organization_type == '6')
                            Optometry Clinic
                            @elseif (isset($User) && $User?->registerCredit->organization_type == '7')
                            Pharmacy
                            @elseif (isset($User) && $User?->registerCredit->organization_type == '8')
                            Podiatry Clinic
                            @elseif (isset($User) && $User?->registerCredit->organization_type == '9')
                            Other
                        @else
                            Cannot find any company type
                        @endif
                    </td>
                </tr>
                   <!--<tr>-->
                   <!-- <td class="text-primary">Company Name:</td>-->
                   <!-- <td>-->
                   <!--    {{$User?->registerCredit->first()->company_name}}-->
                   <!-- </td>-->
                   <!--</tr>-->
                   <!--<tr>-->
                   <!-- <td class="text-primary">Organization Name	:</td>-->
                   <!-- <td>-->
                   <!--    {{$User?->registerCredit->first()->organization_name	}}-->
                   <!-- </td>-->
                   <!--</tr>-->
                   <tr>
                    <td class="text-primary">Account Status:</td>
                    <td>
<span class="badge w-50 {{ $User->is_approved ==1  ? 'bg-danger' : 'bg-success' }}">
    @if($User->is_approved == 1)
        Reject
    @else
        Approved
    @endif
</span>
                    </td>
                   </tr>

                
@if(!empty($User?->registerCredit?->phone_number))
                   <tr>
                    <td class="text-primary">Phone Number:</td>
                    <td>
                       {{$User?->registerCredit->phone_number}}
                    </td>
                   </tr>
                   @endif
                   @if(!empty($User?->registerCredit?->phone_number))
                   <tr>
                    <td class="text-primary">Mobile Number:</td>
                    <td>
                       {{$User?->registerCredit->mobile_number}}
                    </td>
                   </tr>
                   @endif
                  
             
            </table>
        </div>

        <!-- Head Office Card -->
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
              @foreach ($creditDelivery as $customer)
    <tr>
        <td>{{ $customer->address1 }}</td>
        <td>{{ $customer->post_code }}</td>
        <td>{{ $customer->town }}</td>
        <td>{{ $customer->city }}</td>
        <td>{{ $customer->county }}</td>
        <td>{{ $customer->country ?? 'Not Available' }}</td>
    </tr>
@endforeach

               
            </table>
        </div>
    </div>

    <!-- Second Row: Two Cards -->
 
</div>

@endsection
