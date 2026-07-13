<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <title>Account Registration Confirmation</title>
  <!-- Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #e6f0f8;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #e6f0f8;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" border="0"
          style="background-color: #ffffff; margin: 0 auto;">
          <!-- Header Logo -->
          <tr>
            <td align="center" style="padding: 20px 0;">
              <img src="https://mds.tech9et.com/public/uploads/all/4YNSioX3IvRAMGUZmCBdam6GZmpxQCmvQj5DO7me.png"
                alt="My Doctor Shop Logo" style="max-height: 60px;">
            </td>
          </tr>

          <!-- Blue Heading -->
          <tr>
            <td
              style="background-color: #005eb8; padding: 20px; text-align: center; color: #fff; font-size: 22px; font-weight: bold;">
              Account Registration Confirmation
            </td>
          </tr>

          <!-- Main Content -->
          <tr>
            <td style="padding: 30px; color: #000; font-size: 14px;">
              <span style="display: block; font-weight: bold; margin-bottom: 10px;">{{$firstName}},</span>

              <span style="display: block; line-height: 1.6; margin-bottom: 10px;">
                Thank you for registering with <strong>My Doctor Shop </strong>! We're excited to have you on board.
                Your account is now active, and you can start shopping with us right away.
              </span>

              <span style="display: block; color: #005eb8; font-weight: bold; margin-bottom: 10px;">Account
                Details</span>

              <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 10px;">
                @if($user->is_approved != 1)

                <tr>
                  <td style="font-weight: bold; padding: 5px 10px 5px 0; width: 120px;">Username:</td>
                  <td style="padding: 5px 0;">{{$firstName}}{{$last_name}}</td>
                </tr>
                @endif

                <tr>
                  <td style="font-weight: bold; padding: 5px 10px 5px 0;">Email:</td>
                  <td style="padding: 5px 0;">{{$email}}</td>
                </tr>
                @if($user->is_approved == 1)
                <tr>
                  <td style="font-weight: bold; padding: 5px 10px 5px 0;">Password:</td>
                  <td style="padding: 5px 0;">{{$randomPassword}}</td>
                </tr>

                @endif
              </table>


              <span style="display: block; line-height: 1.6; margin-bottom: 10px;">
                You can log in to your account anytime to view your orders, update your information, or track your
                deliveries here:
                <a href="https://mydoctorshop.com/login/">account login</a>
              </span>

              <span style="display: block; color: #005eb8; font-weight: bold; margin-bottom: 5px;">What You Can Do with
                Your Account:</span>
              <span style="display: block; line-height: 1.1; margin-bottom: 10px;">
                - Track your order history and status<br>
                - Manage your personal information and addresses<br>
                - Save items to your wishlist for future purchases<br>
                - Access exclusive offers and promotions
              </span>

              <span style="display: block;  margin-bottom: 10px;">
                If you have any questions or need assistance, feel free to reach out to <a
                  href="https://mydoctorshop.com/contact/"> contact us</a>.<br>
                Welcome to the My Doctor Shop family!
              </span>

              <span style="display: block; line-height: 1.6;">
                Kind regards,<br>
                My Doctor Shop Team<br>
                <a href="https://mydoctorshop.com/"
                  style="color: #005eb8; text-decoration: none;">https://mydoctorshop.com/</a>
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
                      <a href="mailto:Acc@mydoctorshop.com"
                        style="color: #ffffff; text-decoration: none;">Acc@mydoctorshop.com</a>
                    </span>
                  </td>
                </tr>
              </table>



              <p style="font-size: 11px; color: #ffffff; line-height: 1.6;">
                <strong>PRIVILEGED & CONFIDENTIAL</strong><br>
                This email and any attachments are intended only for the named recipient and may contain confidential or
                legally privileged information. Any unauthorised use, disclosure, copying, or distribution is
                prohibited. If you received this message in error, please notify the sender immediately and delete it.
                The views expressed are those of the author and may not reflect those of My Doctor Shop Ltd. While this
                email has been scanned for viruses, recipients are responsible for ensuring it is virus-free. My Doctor Shop
                Ltd accepts no liability for any damage caused by viruses transmitted via email.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>

</html>