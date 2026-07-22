<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>New Order Received</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff;">
        <!-- ===== Header Logo ===== -->
        <tr>
            <td align="center" style="padding: 25px 0; background:#1e1e1e;">
                <img src="https://rnrgsv2.tech9et.com/public/uploads/all/stshTDbOHCqb7vBUj6hkHqxYCG2dN7x8hrBYBdHH.png" alt="Raw NRG Supps" style="max-height:105px;">
            </td>
        </tr>

        <!-- ===== Blue Side Background Section ===== -->
        <!-- ===== Blue Side Background Section ===== -->
        @php
            $sa = $order->shipping_address;
            $shippingObj = $sa && !is_array(json_decode($sa)) ? json_decode($sa) : (object) [];
            $shipping = is_array($sa) ? $sa : json_decode($sa, true) ?? [];
            $customerName =
                $shippingObj->name ??
                ($shipping['name'] ??
                    null ??
                    (optional($order->user)->name
                        ? trim(optional($order->user)->name . ' ' . optional($order->user)->last_name)
                        : 'Customer'));
        @endphp
        <tr>
            <td style="background: #1e1e1e; padding: 0; position: relative; height: 100px; margin: 20px 0px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" style="padding: 0 20px">
                            <!-- White Card Box -->
                            <table width="1000" height="100" cellpadding="0" cellspacing="0" style=" background: #f3efd9; border-radius: 10px; /* margin: 20px auto; */ text-align: center; position: absolute; top: 10; left: 50%; transform: translate(-50%, -50%); border:3px dashed #cf9804; ">
                                <tr>
                                    <td style="padding: 50px 10px">
                                        <div
                                            style="font-size: 22px; font-weight: bold; color: #cf9804; margin-bottom: 10px;">
                                            New Order Received – {{ $order->code }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:70px 0 40px 0;background:#ffffff">
                <table width="1000" cellpadding="15" cellspacing="0" style="background:#ffffff; border-radius:6px; text-align:left;">
                    <td align="left" style="padding:10px 20px 40px 20px; font-size:14px; color:#1e1e1e; border:1px solid #ddd;">
                        <table width="100%" cellpadding="6" cellspacing="0">
                            <tr>
                                <td style="color:#1e1e1e; padding-bottom:10px;">A new order has been placed.</td>
                            </tr>
                            <tr>
                                <td style="color:#1e1e1e; padding-bottom:10px;">
                                    <span style="font-weight:bold;">Order Number:</span>
                                    {{ $order->code }}
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#1e1e1e; padding-bottom:10px;">
                                    <span style="font-weight:bold;">Customer Name:</span>
                                    {{ $customerName }}</td>
                            </tr>
                            <tr>
                                <td style="color:#1e1e1e; padding-bottom:10px;">
                                    <span style="font-weight:bold;">Order Total:</span>
                                    {{ single_price($order->grand_total) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#1e1e1e;" style="font-style: normal;">Please log into the admin panel to process the order</td>
                            </tr>
                        </table>
                    </td>
                </table>
            </td>
        </tr>
        <tr>
            <td style="background-color: #1e1e1e; color: #ffffff; padding: 30px 50px; font-size: 12px;">
                <table width="100%" cellpadding="0" cellspacing="0" align="center" style="color: #ffffff; font-size: 12px; text-align:left;">
                    <tr>
                        <!-- Find Us Column -->
                        <td valign="top" style="width: 33%; padding-right: 10px;">
                            <img src="https://rnrgsv2.tech9et.com/public/uploads/all/stshTDbOHCqb7vBUj6hkHqxYCG2dN7x8hrBYBdHH.png" alt="Raw NRG Supps" style="max-height:105px;">
                        </td>
                        <!-- Company Details Column -->
                        <td valign="top" style="width: 33%; padding-right: 10px;">
                            <span style="display: block; margin-bottom: 5px;">
                                <strong>Find Us:</strong>
                            </span>
                            <span style="display: block; line-height: 1.6;">
                                <!-- <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i> -->
                                <img src="https://mds.tech9et.com/public/uploads/all/location_on.png" alt="Raw NRG Supps" style="height:10px;margin-right: 5px;">
                                Raw Nrg Supps<br>
                                Canal Mills,<br>
                                Hillhouse Lane,<br>
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
                                    style="color: #ffffff; text-decoration: none;">hello@rawnrgsupps.com</a>
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
                    Raw NRG Supps Ltd. While this email has been scanned for viruses, recipients are responsible for
                    ensuring it is virus-free. Raw NRG Supps Ltd accepts no liability for any damage caused by viruses
                    transmitted via email.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
