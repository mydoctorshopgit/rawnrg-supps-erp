<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Order Dispatched</title>
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
    $shipping = json_decode($order->shipping_address, true);
        $address =$order->customer->creditDelivery->first();

@endphp
     <tr >
        <td style="background: #1565b3; padding: 0; position: relative; height: 100px; margin: 20px 0px;">
            
            <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center" style="padding: 0 20px">
                <!-- White Card Box -->
                <table
                  width="1000"
                  height="100"
                  cellpadding="0"
                  cellspacing="0"
                  style="
                    background: #dbe7f3;
                    border-radius: 10px;
                    /* margin: 20px auto; */
                    text-align: center;
                    position: absolute;
                    top: 10;
                    left: 50%;
                    transform: translate(-50%, -50%);
                  "
                >
                  <tr>
                    <td style="padding: 20px 10px">
                      <div
                        style="
                          font-size: 22px;
                          font-weight: bold;
                          color: #1565b3;
                          margin-bottom: 10px;
                        "
                      >
                        Order Dispatched
                      </div>

                      <div
                        style="font-size: 14px; color: #333; line-height: 1.6"
                      >
                       Your Order {{ $order->code ?? 'N/A' }} Has Been Dispatched<br>
Hello {{ $shipping['name'] ?? 'N/A' }},
Good news – your order has been dispatched.
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
                                    <td>Shipping Detail:</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ORDER INFO ROWS -->
                    <tr>
                        <td style="padding:15px 10px; font-size:14px; color:#333;">
                            <table width="100%" cellpadding="6" cellspacing="0">
                                <tr>
                                    <td style="color:#black; font-weight:bold;">Tracking Number:</td>
                                    <td align="right">{{ $order->tracking_code }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#black; font-weight:bold;">Carrier:</td>
                                    <td align="right">{{ $order->carrier_name ?? '' }}</td>
                                </tr>
                                <!-- <tr>
                                    <td style="color:#black;">You can track your package here::</td>
                                    <td align="right">$250.00</td>
                                </tr> -->

                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

      
         <tr>
    <td align="center" style="background:#ffffff; padding:40px 0;">
        <table width="1000" cellpadding="0" cellspacing="0"
            style="background:#ffffff; border-radius:6px; text-align:left;">
            <tr>
                <td align="left"
                    style="padding:30px; font-size:14px; color:#333; border:1px solid #ddd;">
                    You will receive another email once your order has been dispatched.<br><br>
                    If you have any questions, please contact 
                    <a href="mailto:Acc@mydoctorshop.com" style="color:rgba(30, 30, 30, 1); text-decoration: none;">
                        Acc@mydoctorshop.com
                    </a>
                    and quote your order number.<br><br>
                    Kind regards,<br>
                    My Doctor Shop Team
                </td>
            </tr>
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
                                   <img src="https://mds.tech9et.com/public/uploads/all/call.png"
                    alt="My Doctor Shop" style="height:10px;margin-right: 5px;">
                                 +44 (0)3301 331 786
                            </span>
                            <span style="display: block; margin-top: 10px;">
                                <!-- <i class="fas fa-envelope" style="margin-right: 5px;"></i>
                                  -->

                                 <img src="https://mds.tech9et.com/public/uploads/all/mail.png"
                    alt="My Doctor Shop" style="height:10px;margin-right: 5px;">
                                <a href="mailto:hello@mydoctorshop.co.uk"
                                    style="color: #ffffff; text-decoration: none;">hello@mydoctorshop.co.uk</a>
                            </span>
                        </td>
                    </tr>
                </table>



                <p style="font-size: 11px; color: #ffffff; line-height: 1.6;">
                    <strong>PRIVILEGED & CONFIDENTIAL</strong><br>
                    This email and any attachments are intended only for the named recipient and may contain confidential or legally privileged information. Any unauthorised use, disclosure, copying, or distribution is prohibited. If you received this message in error, please notify the sender immediately and delete it. The views expressed are those of the author and may not reflect those of My Doctor Shop Ltd. While this email has been scanned for viruses, recipients are responsible for ensuring it is virus-free. My Doctor Shop Ltd accepts no liability for any damage caused by viruses transmitted via email.
                </p>
            </td>
        </tr>


    </table>
</body>

</html>