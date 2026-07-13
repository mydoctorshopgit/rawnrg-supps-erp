<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Order Confirmation</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff;">
        <!-- ===== Header Logo ===== -->
        <tr>
            <td align="center" style="padding: 25px 0; background:#ffffff;">
                <img src="https://mds.tech9et.com/public/uploads/all/4YNSioX3IvRAMGUZmCBdam6GZmpxQCmvQj5DO7me.png"
                    alt="My Doctor Shop" style="max-height:60px;">
            </td>
        </tr>

        <!-- ===== Blue Side Background Section ===== -->
        <!-- ===== Blue Side Background Section ===== -->
        @php
            // Decode the shipping_address JSON stored at order-time — ground truth
            $sa = $order->shipping_address;
            $shippingObj = $sa && !is_array(json_decode($sa)) ? json_decode($sa) : (object) [];

            // Also keep as array for backward-compatible access
            $shipping = is_array($sa) ? $sa : json_decode($sa, true) ?? [];

            // Country from JSON — used for delivery charge logic
            $shippingCountry = $shippingObj->country ?? ($shipping['country'] ?? '');
        @endphp
        <tr>
            <td style="background: #1565b3; padding: 0; position: relative; height: 100px; margin: 20px 0px;">

                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" style="padding: 0 20px">
                            <!-- White Card Box -->
                            <table width="1000" height="100" cellpadding="0" cellspacing="0"
                                style="
                    background: #dbe7f3;
                    border-radius: 10px;
                    /* margin: 20px auto; */
                    text-align: center;
                    position: absolute;
                    top: 10;
                    left: 50%;
                    transform: translate(-50%, -50%);
                  ">
                                <tr>
                                    <td style="padding: 20px 10px">
                                        <div
                                            style="
                          font-size: 22px;
                          font-weight: bold;
                          color: #1565b3;
                          margin-bottom: 10px;
                        ">
                                            Order Confirmation
                                        </div>

                                        <div style="font-size: 14px; color: #333; line-height: 1.6">
                                            Hello {{ $shippingObj->name ?? ($shipping['name'] ?? 'N/A') }},<br />
                                            Thank you for your order with My Doctor Shop.<br />
                                            We have received your order and are now processing it.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ================= ORDER DETAILS SECTION ================= -->
        <tr>
            <td align="center" style="padding:40px 0;">
                <table width="1000" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:6px; text-align:left;">
                    <!-- BLUE HEADER BAR -->
                    <tr>
                        <td
                            style="background:#1565b3; color:#ffffff; padding:12px 10px; font-size:16px; font-weight:bold;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>Order Details</td>
                                    <td align="right">{{ $order->invoice_number }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ORDER INFO ROWS -->
                    <tr>
                        <td style="padding:15px 10px; font-size:14px; color:#333;">
                            <table width="100%" cellpadding="6" cellspacing="0">
                                <tr>
                                    <td style="color:#black; font-weight:bold;">Order Number:</td>
                                    <td align="right">{{ $order->code }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#black; font-weight:bold;">Order Date:</td>
                                    <td align="right">{{ date('d-m-Y', $order->date) }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#black; font-weight:bold;">Order Total:</td>
                                    <td align="right">{{ single_price($order->grand_total) }}</td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ================= ITEMS HEADING ================= -->
        <tr>
            <td align="center" style="padding:10px 0 5px 0;">
                <table width="1000" cellpadding="0" cellspacing="0" style="text-align:left;">
                    <tr>
                        <td
                            style="color:#1565b3; font-weight:bold; font-size:20px; padding-left:5px;padding-bottom:5px;border-bottom:1px solid #ddd;">
                            Items in Your Order:
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        @foreach ($order->orderDetails as $key => $item)
            @php
                $stock = App\Models\ProductStock::with('product:id,discount_type,discount')
                    ->where('sku', $item->sku)
                    ->first();
            @endphp
            <tr>
                <td align="center" style="padding-bottom:20px; ">
                    <table width="1000" cellpadding="10" cellspacing="0"
                        style="background:#ffffff; border-radius:6px; text-align:left; border-bottom:1px solid #ddd;">

                        <!-- ITEM 1 -->

                        <tr style="border-bottom:1px solid #ddd; padding-bottom:20px;  ">
                            <td width="100" style="padding:5px; background-color:;">
                                <img src="{{ asset('public/' . $item->product->thumbnail->file_name) }}"
                                    alt="Product Image"
                                    style="width:60px; height:auto; display:block; border-radius:4px;">
                            </td>
                            <td style="font-size:14px; color:#333; vertical-align:top;">
                                <div style="font-size:12px; color:#555; padding-bottom:5px;">{{ $item->sku ?? 'N/A' }}
                                </div>
                                <div style="font-weight:bold; color:#1565b3;padding-bottom:5px;">
                                    {{ $item->product->name ?? 'N/A' }}</div>

                                {{-- Unit price shown below name --}}
                                @php
                                    $unitPrice =
                                        $item->quantity > 0 ? round($item->price / $item->quantity, 2) : $item->price;
                                    $lineVat = (float) ($item->tax ?? 0);
                                @endphp
                                <div style="font-size:12px; color:#555; padding-bottom:5px;">
                                    <span style="font-weight:bold;">Unit Price:</span>
                                    {{ single_price($unitPrice) }}
                                </div>

                                <div style="font-size:12px; color:#555; padding-bottom:5px;">
                                    <div style="display:inline-block; width:60px; font-weight:bold;">Pack of:</div>
                                    <div style="display:inline-block;">{{ $stock->pack_qty ?? '' }}</div>
                                </div>
                                @if (!empty($stock->variant))
                                    <div style="font-size:12px; color:#555; padding-bottom:5px;">
                                        <div style="display:inline-block; width:60px; font-weight:bold;">Size:</div>
                                        <div style="display:inline-block;">{{ $stock->variant }}</div>
                                    </div>
                                @endif
                                @if (!empty($stock->color))
                                    <div style="font-size:12px; color:#555; padding-bottom:5px;">
                                        <div style="display:inline-block; width:60px; font-weight:bold;">Color:</div>
                                        <div style="display:inline-block;">{{ $stock->color }}</div>
                                    </div>
                                @endif
                                @if (!empty($stock->flavour))
                                    <div style="font-size:12px; color:#555; padding-bottom:5px;">
                                        <div style="display:inline-block; width:60px; font-weight:bold;">Flavor:</div>
                                        <div style="display:inline-block;">{{ $stock->flavour }}</div>
                                    </div>
                                @endif
                                @if (!empty($item->quantity))
                                    <div style="font-size:12px; color:#555; padding-bottom:5px;">
                                        <div style="display:inline-block; width:60px; font-weight:bold;">Quantity:</div>
                                        <div style="display:inline-block;">{{ $item->quantity }}</div>
                                    </div>
                                @endif
                            </td>
                            <td
                                style="font-size:14px; color:#1565b3; text-align:right; vertical-align:top; font-weight:bold;">
                                {{-- Line total (no division — $item->price is already quantity × unit) --}}
                                <div style="font-size:20px; margin-bottom:4px;">{{ single_price($item->price) }}</div>
                                {{-- VAT for this line --}}
                                {{-- <div style="font-size:12px; color:#555; font-weight:normal;">
                                    inc. VAT: {{ single_price($lineVat) }}
                                </div> --}}
                            </td>
                        </tr>





                    </table>
                </td>
            </tr>
        @endforeach
        <tr>
            <td align="center" style="padding:20px 0;">
                <table width="1000" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:6px; text-align:left;">
                    <!-- BLUE HEADER BAR -->


                    <!-- ORDER INFO ROWS -->
                    <tr>
                        <td style="padding:15px 10px; font-size:14px; color:#333;">
                            <table width="100%" cellpadding="6" cellspacing="0">
                                <tr>
                                    <td style="color:#333; font-weight:bold;padding-bottom:10px;">Subtotal:</td>
                                    <td align="right">{{ single_price($order->orderDetails->sum('price')) }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#333; font-weight:bold;padding-bottom:10px;">Discount:</td>
                                    <td align="right">{{ single_price($order->coupon_discount) }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#333; font-weight:bold;padding-bottom:10px;">Delivery Charge:</td>
                                    <td align="right">
                                        @if ($shippingCountry != 'United Kingdom')
                                            {{ single_price($order->orderDetails->sum('shipping_cost')) }}
                                        @else
                                            {{ single_price($order->orderDetails->sum('price') > 49.0 ? 0 : 7.99) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#333; font-weight:bold;padding-bottom:10px;">VAT:</td>
                                    <td align="right">{{ single_price($order->total_tax) }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="color:#1565b3;  background-color: rgba(228, 242, 255, 1); font-weight:bold;">
                                        Total:</td>
                                    <td align="right"
                                        style="color:#1565b3;  background-color: rgba(228, 242, 255, 1); font-weight:bold;">
                                        {{ single_price($order->grand_total) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        @if (isset($verificationUrl) && !empty($verificationUrl))
            <tr>
                <td align="center" style="padding:10px 0 30px 0;">
                    <table width="1000" cellpadding="15" cellspacing="0" style="text-align:left;">
                        <tr>
                            <td>
                                <p style="color:#1565b3; font-weight:bold; font-size:15px;">Please use the secure link
                                    below to complete your international payment:</p>
                                <br />
                                <p style="color:#1565b3; font-weight:bold; font-size:15px;">
                                    Click the here to pay securely
                                    <a href="{{ $verificationUrl }}"
                                        style="background-color: #4CAF50; color: white; padding: 5px 5px; text-decoration: none; border-radius: 3px; font-size: 15px;">
                                        Pay Now
                                    </a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif
        <tr>
            <td align="center" style="padding:10px 0 30px 0;">
                <table width="1000" cellpadding="15" cellspacing="0"
                    style="background:#e4f2ff; border-radius:6px; text-align:left;">
                    <tr>
                        <td>
                            <strong style="color:#1565b3;">Shipping Address:</strong><br><br>
                            @php
                                $addrLines = array_filter([
                                    $shippingObj->address ?? '',
                                    $shippingObj->address1 ?? '',
                                    $shippingObj->address2 ?? '',
                                    $shippingObj->address3 ?? '',
                                    $shippingObj->town ?? '',
                                    $shippingObj->city ?? '',
                                    $shippingObj->county ?? '',
                                    $shippingObj->post_code ?? '',
                                    $shippingCountry,
                                ]);
                            @endphp
                            {!! implode('<br>', $addrLines) ?: 'N/A' !!}
                        </td>
                        <td width="50" align="center">
                            <!-- <i class="fas fa-map-marker-alt" style="font-size:24px; color:#1565b3;"></i>
                              -->
                            <img src="https://mds.tech9et.com/public/uploads/all/location.png" alt="My Doctor Shop"
                                style="max-height:70px;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:10px 0 30px 0;background:#ffffff">
                <table width="1000" cellpadding="15" cellspacing="0"
                    style="background:#ffffff; border-radius:6px; text-align:left;">
                    <td align="left"
                        style="padding:10px 20px 40px 20px; font-size:14px; color:#333; border:1px solid #ddd;">
                        You will receive another email once your order has been dispatched.<br><br>
                        If you have any questions, please contact <a href="mailto:Acc@mydoctorshop.com"
                            style="color:rgba(30, 30, 30, 1); text-decoration: none;">Acc@mydoctorshop.com</a>and quote
                        your order number.<br><br>
                        Kind regards,<br>
                        My Doctor Shop Team
                    </td>
                </table>
            </td>
        </tr>

        <tr>
            <td style="background-color: #005eb8; color: #ffffff; padding: 30px 50px; font-size: 12px;">
                <table width="100%" cellpadding="0" cellspacing="0" align="center"
                    style="color: #ffffff; font-size: 12px; text-align:left;">
                    <tr>
                        <!-- Find Us Column -->
                        <td valign="top" style="width: 33%; padding-right: 10px;">
                            <img src="https://mds.tech9et.com/public/uploads/all/footer_image.png"
                                alt="My Doctor Shop" style="max-height:60px;">
                        </td>

                        <!-- Company Details Column -->
                        <td valign="top" style="width: 33%; padding-right: 10px;">
                            <span style="display: block; margin-bottom: 5px;">
                                <strong>Find Us:</strong>
                            </span>

                            <span style="display: block; line-height: 1.6;">
                                <!-- <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                                  -->
                                <img src="https://mds.tech9et.com/public/uploads/all/location_on.png"
                                    alt="My Doctor Shop" style="height:10px;margin-right: 5px;">

                                My Doctor Shop Ltd,Canal<br>
                                Mills,Hillhouse Lane,<br>
                                Huddersfield,<br>
                                HD1 1ED <br>
                                United Kingdom
                            </span>
                        </td>

                        <!-- Contact Column -->
                        <td valign="top" style="width: 33%;">
                            <span style="display: block; margin-bottom: 8px;">
                                <strong>Contact Us:</strong>
                            </span>
                            <span style="display: block; margin-bottom: 5px; line-height: 1.6;">
                                <!-- <i class="fas fa-phone" style="margin-right: 5px;"></i> -->
                                <img src="https://mds.tech9et.com/public/uploads/all/call.png" alt="My Doctor Shop"
                                    style="height:10px;margin-right: 5px;">
                                +44 (0)3301 331 786
                            </span>
                            <span style="display: block; margin-top: 10px;">
                                <!-- <i class="fas fa-envelope" style="margin-right: 5px;"></i>
                                  -->

                                <img src="https://mds.tech9et.com/public/uploads/all/mail.png" alt="My Doctor Shop"
                                    style="height:10px;margin-right: 5px;">
                                <a href="mailto:hello@mydoctorshop.co.uk"
                                    style="color: #ffffff; text-decoration: none;">hello@mydoctorshop.co.uk</a>
                            </span>
                        </td>
                    </tr>
                </table>



                <p style="font-size: 11px; color: #ffffff; line-height: 1.6;">
                    <strong>PRIVILEGED & CONFIDENTIAL</strong><br>
                    This email and any attachments are intended only for the named recipient and may contain
                    confidential or legally privileged information. Any unauthorised use, disclosure, copying, or
                    distribution is prohibited. If you received this message in error, please notify the sender
                    immediately and delete it. The views expressed are those of the author and may not reflect those of
                    My Doctor Shop Ltd. While this email has been scanned for viruses, recipients are responsible for
                    ensuring it is virus-free. My Doctor Shop Ltd accepts no liability for any damage caused by viruses
                    transmitted via email.
                </p>
            </td>
        </tr>



    </table>
</body>

</html>
