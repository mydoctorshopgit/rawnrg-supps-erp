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
    <h2>Guest Account Detail</h2>

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
                   <tr>
                    <td class="text-primary">Last Name:</td>
                    <td>
                       {{$User->last_name}}
                    </td>
                   </tr>
                   <tr>
                    <td class="text-primary">Email:</td>
                    <td>
                       {{$User->email}}
                    </td>
                   </tr>
                   <tr>
                    <td class="text-primary">Mobile Number:</td>
                    <td>
                       {{$User?->mobile_number}}
                    </td>
                   </tr> 
                  
             
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
