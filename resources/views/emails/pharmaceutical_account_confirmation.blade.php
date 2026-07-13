<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;"/>
    
    <title>Pharmaceutical Account Application</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  </head>
  <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #e6f0f8;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #e6f0f8;">
      <tr>
        <td align="center">
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; margin: 0 auto;">
            <!-- Header Logo -->
            @php
              $logo = get_setting('header_logo');
            @endphp
            <tr>
              <td align="center" style="padding: 20px 0;">
                @if($logo != null)
                  <img src="{{ uploaded_asset($logo) }}" height="30" style="display:inline-block;">
                @else
                  <img src="{{ static_asset('assets/img/logo.png') }}" height="30" style="display:inline-block;">
                @endif

              </td>
            </tr>

            <!-- Blue Heading -->
            <tr>
              <td style="background-color: #005eb8; padding: 20px; text-align: center; color: #fff; font-size: 22px; font-weight: bold;">
            Pharmaceutical Account Confirmation
              </td>
            </tr>

            <!-- Main Content -->
<tr>
  <td style="padding: 30px; color: #000; font-size: 14px;">
    <span style="display: block; font-weight: bold; margin-bottom: 10px;">{{$name}},</span>

    <span style="display: block; line-height: 1.6; margin-bottom: 10px;">
  We’re pleased to confirm that your pharmaceutical account with My Doctor Shop has been successfully set up and is now active. You are now eligible to make purchases and  
access our range of pharmaceutical products and services.
    </span>

<span style="display: block; color: #005eb8; font-weight: bold; margin-bottom: 10px;"> Account Details:</span>

<table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 10px;">
  <tr>
    <td style="font-weight: bold; padding: 5px 10px 5px 0; width: 120px;"> Account Name</td>
    <td style="padding: 5px 0;">{{$companyName}}</td>
  </tr>
  <tr>
    <td style="font-weight: bold; padding: 5px 5px 5px 0; width:200px;"> Account Number:</td>
    <td style="padding: 5px 0;">{{$accountNumber}}</td>
  </tr>
</table>

<span style="display: block; color: #005eb8; font-weight: bold; margin-bottom: 2px;">  What You Can Do with Your Account:</span>

    <!--<span style="display: block; line-height: 1.6; margin-bottom: 10px;">-->
    <!--  You can log in to your account anytime to view your orders, update your information, or track your deliveries here:-->
    <!--  <a href="#" style="color: #005eb8;">[LoginURL]</a>-->
    <!--</span>-->

    <!--<span style="display: block; color: #005eb8; font-weight: bold; margin-bottom: 5px;">Terms and Conditions::</span>-->
    <!--<span style="display: block; line-height: 1.6; margin-bottom: 2px;">-->
    <!-- Please note that all purchases made under this credit account are subject to the following terms:-->
    <!--</span>-->
    <!--<span style="display: block; line-height: 1.4; margin-bottom: 10px;">-->
    <!--  - Payment is due 30 days from the date of the invoice.<br>-->
    <!--  -Late payments may result in additional charges or account suspension. -->
    
    <!--</span>-->

    <span style="display: block; line-height: 1.6; margin-bottom: 2px;">
     You can log in to your <a href="https://mydoctorshop.com/login/">account login</a> . Our team is dedicated to providing you with excellent service and support to meet your pharmaceutical needs. 
    </span>
    <span style="display: block;  margin-bottom: 2px;">
    For any questions or additional assistance, feel free to  <a href="https://mydoctorshop.com/contact/"> contact us</a>.
    </span>
    <span style="display: block;  margin-bottom: 10px;">
    We look forward to working with you and providing you with high-quality pharmaceutical products. 
    </span>

    <span style="display: block; line-height: 1.6;">
      Kind regards,<br>
      My Doctor Shop Team<br>
      <a href="https://mydoctorshop.com/" style="color: #005eb8; text-decoration: none;">https://mydoctorshop.com/</a>
    </span>
  </td>
</tr>

            <!-- Footer -->
            <tr>
              <td style="background-color: #005eb8; color: #ffffff; padding: 30px; font-size: 12px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="color: #ffffff; font-size: 12px;">
                  <tr>
                    <!-- Find Us Column -->
                    <td valign="top" style="width: 33%; padding-right: 10px;">
                      <span style="display: block; margin-bottom: 5px;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                        <strong>Find Us:</strong>
                      </span>
                      <span style="display: block; line-height: 1.6;">
                       My Doctor Shop Ltd,<br>
                   Canal Mills,Hillhouse Lane,<br>
                   Huddersfield, HD1 1ED United Kingdom
                    </span>
                    </td>

                    <!-- Company Details Column -->
                    <td valign="top" style="width: 33%; padding-right: 10px;">
                      <span style="display: block; margin-bottom: 5px;">
                        <i class="fas fa-file-alt" style="margin-right: 5px;"></i>
                        <strong>Company Details:</strong>
                      </span>
                      <span style="display: block; line-height: 1.6;">
                        Company No.: 7326383
                        VAT No.: GB 998 1213 86
                      </span>
                    </td>

                    <!-- Contact Column -->
                    <td valign="top" style="width: 33%;">
                      <span style="display: block; margin-bottom: 5px;">
                        <i class="fas fa-phone" style="margin-right: 5px;"></i>
                        <strong>Telephone:</strong> 0330 133 1786
                      </span>
                      <span style="display: block; margin-top: 10px;">
                        <i class="fas fa-envelope" style="margin-right: 5px;"></i>
                        <strong>Email:</strong>
                        <a href="mailto:Acc@mydoctorshop.com" style="color: #ffffff; text-decoration: none;">Acc@mydoctorshop.com</a>
                      </span>
                    </td>
                  </tr>
                </table>

             

                <p style="font-size: 11px; color: #ffffff; line-height: 1.6;">
                  <strong>PRIVILEGED & CONFIDENTIAL</strong><br>
                  This email and any attachments are intended only for the named recipient and may contain confidential or legally privileged information. Any unauthorised use, disclosure, copying, or distribution is prohibited. If you received this message in error, please notify the sender immediately and delete it.
                  The views expressed are those of the author and may not reflect those of My Doctor Shop Ltd. While this email has been scanned for viruses, recipients are responsible for ensuring it is virus-free. My Doctor Shop Ltd accepts no liability for any damage caused by viruses transmitted via email.
                </p>
              </td>
            </tr>

          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
