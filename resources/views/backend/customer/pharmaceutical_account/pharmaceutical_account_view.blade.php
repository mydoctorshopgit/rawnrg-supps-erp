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
    <h2>Pharmaceutical Account Detail</h2>

    <!-- First Row: Two Cards -->
    <div class="row">
        <!-- Customer Information Card -->
        <div class="card">
            <table>
              <tr>
                    <th colspan="2" class="section-title">Account Detail</th>
                </tr>
                
              <tr>
    <td class="text-primary">First Name:</td>
    <td>{{ $User->pharmaceuticalAccount->holder_first_name }}</td>
</tr>
<tr>
    <td class="text-primary">Last Name:</td>
    <td>{{ $User->pharmaceuticalAccount->holder_last_name }}</td>
</tr>
<tr>
    <td class="text-primary">Email:</td>
    <td>{{ $User->pharmaceuticalAccount->holder_email }}</td>
</tr>

@if($User?->pharmaceuticalAccount?->company_name)
<tr>
    <td class="text-primary">Bussiness Name:</td>
    <td>{{ $User->pharmaceuticalAccount->company_name }}</td>
</tr>
@endif

@if($User?->pharmaceuticalAccount?->account_number)
<tr>
    <td class="text-primary">Account Number:</td>
    <td>{{ $User->pharmaceuticalAccount->account_number }}</td>
</tr>
@endif

@if($User?->pharmaceuticalAccount?->registration_date)
<tr>
    <td class="text-primary">Registration Date:</td>
    <td>{{ $User->pharmaceuticalAccount->registration_date }}</td>
</tr>
@endif

@if($User?->pharmaceuticalAccount?->Signature)
<tr>
    <td class="text-primary">Signature:</td>
    <td>{{ $User->pharmaceuticalAccount->Signature }}</td>
</tr>
@endif

@php
    $licenseType = optional($User->pharmaceuticalAccount)->license_type;
    $types = [
        '1' => 'GMC',
        '2' => 'GPC',
        '3' => 'HPC',
        '4' => 'JDC',
        '5' => 'NMC',
        '6' => 'WDL',
        '7' => 'GPSC',
    ];
@endphp

@if($licenseType)
<tr>
    <td class="text-primary">License Type:</td>
    <td>{{ $types[$licenseType] ?? 'Cannot find any type' }}</td>
</tr>
@endif

<tr>
    <td class="text-primary">Account Status:</td>
    <td>
        <span class="badge w-25
            {{ $User->is_pharma_approved == 1 ? 'bg-success' : ($User->is_pharma_approved == 0 ? 'bg-warning' : 'bg-danger') }}">
            {{ $User->is_pharma_approved == 1 ? "Approved" : ($User->is_pharma_approved == 0 ? "Pending" : "Rejected") }}
        </span>
    </td>
</tr>

@if($User?->pharmaceuticalAccount?->url)
<tr>
    <td class="text-primary">Url:</td>
    <td>
        <a href="{{ $User->pharmaceuticalAccount->url }}" target="_blank" rel="noopener noreferrer">
            {{ $User->pharmaceuticalAccount->url }}
        </a>
    </td>
</tr>
@endif

@if($User?->pharmaceuticalAccount?->license_name)
<tr>
    <td class="text-primary">License Name:</td>
    <td>{{ $User->pharmaceuticalAccount->license_name }}</td>
</tr>
@endif

@if($User?->pharmaceuticalAccount?->license_number)
<tr>
    <td class="text-primary">License Number:</td>
    <td>{{ $User->pharmaceuticalAccount->license_number }}</td>
</tr>
@endif

                   {{--<tr>
                    <td class="text-primary">Company Name:</td>
                    <td>
                       {{$User?->registerCredit->company_name}}
                    </td>
                   </tr>
                   <tr>
                    <td class="text-primary">Organization Name	:</td>
                    <td>
                       {{$User?->registerCredit->organization_name	}}
                    </td>
                   </tr>
                   <tr>
                    <td class="text-primary">Department Name:</td>
                    <td>
                       {{$User?->registerCredit->department_name}}
                    </td>
                   </tr>
                   <tr>
                    <td class="text-primary">Statement Email:</td>
                    <td>
                       {{$User?->registerCredit->statement_email}}
                    </td>
                   </tr>
                   <tr>
                    <td class="text-primary">Phone Number:</td>
                    <td>
                       {{$User?->registerCredit->phone_number}}
                    </td>
                   </tr>
                   <tr>
                    <td class="text-primary">Mobile Number:</td>
                    <td>
                       {{$User?->registerCredit->mobile_number}}
                    </td>
                   </tr> --}}
                  
             
            </table>
        </div>

        <!-- Head Office Card -->
     
    </div>

    <!-- Second Row: Two Cards -->
 
</div>

@endsection
