{{--
    Shared address partial for all order show views.
    Expects: $order (Order model)
--}}
@php
    $sa = $order->shipping_address;
    $shipping_address = ($sa && !is_array(json_decode($sa))) ? json_decode($sa) : (object)[];
@endphp

@if($order->user_id)
    {{-- ── Registered user ── --}}
    <address>
        <strong class="text-main">
            {{ json_decode($order->customer_details)->company_name ?? '' }}
        </strong><br>
        @php
            $full_name = '';
            if (isset($order->user->name) && isset($order->user->last_name)) {
                $full_name = trim($order->user->name . ' ' . $order->user->last_name);
            }
            if (!$full_name) {
                $full_name = $shipping_address->name ?? ($order->user->name ?? 'N/A');
            }

            $addressParts = [
                'Contact Name'  => $full_name ?: 'N/A',
                'Business Name' => optional($order->user)->registerCredit->company_name ?? 'N/A',
                'Phone'         => $shipping_address->phone
                                   ?? (optional($order->user->registerCredit)->mobile_number ?? 'N/A'),
                'Address'       => implode('<br>', array_filter([
                                       $shipping_address->address  ?? '',
                                       $shipping_address->address1  ?? '',
                                       $shipping_address->address2  ?? '',
                                       $shipping_address->address3  ?? '',
                                       $shipping_address->town      ?? '',
                                       $shipping_address->city      ?? '',
                                       $shipping_address->county    ?? '',
                                       $shipping_address->post_code ?? '',
                                       $shipping_address->country   ?? '',
                                   ])) ?: 'N/A',
            ];
        @endphp

        <p>
            <table>
                @foreach($addressParts as $key => $value)
                <tr style="{{ $key == 'Address' ? 'vertical-align: top;' : '' }}">
                    <td style="padding-bottom: 5px;"><strong>{{ $key }}</strong>:</td>
                    <td style="padding-bottom: 5px;">{!! $value !!}</td>
                </tr>
                @endforeach
            </table>
        </p>
    </address>

@else
    {{-- ── Guest ── --}}
    <address>
        <strong class="text-main">Contact Name</strong>: {{ $shipping_address->name ?? $shipping_address->email ?? 'Guest' }}
        <span class="badge-secondary">Guest</span><br>
        <strong>Email</strong>: {{ $shipping_address->email ?? 'N/A' }}<br>
        <strong>Phone</strong>: {{ $shipping_address->phone ?? 'N/A' }}<br>
        <strong>Address</strong>:<br>
        @php
            $guestParts = array_filter([
                $shipping_address->address1  ?? '',
                $shipping_address->address2  ?? '',
                $shipping_address->address3  ?? '',
                $shipping_address->town      ?? '',
                $shipping_address->city      ?? '',
                $shipping_address->county    ?? '',
                $shipping_address->post_code ?? '',
                $shipping_address->country   ?? '',
            ]);
        @endphp
        {!! implode('<br>', $guestParts) ?: 'N/A' !!}
    </address>
@endif
