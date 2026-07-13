<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sales Receipt</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }
  .receipt-container {
    width: 900px;
    margin: 30px auto;
    border: 1px solid #ccc;
    background-color: #fff;
    padding: 30px 40px;
  }
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .header-left {
    width: 60%;
  }
  .header-right {
    width: 40%;
    text-align: right;
  }
  .receipt-title {
    color: #003087;
    font-size: 26px;
    margin: 0 0 6px 10px;
    text-align: left;
  }
  .contact-info p {
    margin: 4px 0;
    font-size: 14px;
  }
  .contact-info a {
    color: #000;
    text-decoration: none;
  }
  .info-table, .invoice-info-table, .product-table, .summary-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
  }
  .info-table td {
    padding: 3px;
  }
  .label {
    text-align: right;
    font-weight: bold;
    padding-right: 8px;
  }
  .highlight-box {
    background-color: #003087;
    color: #fff;
    padding: 6px 8px;
    border-radius: 4px;
    text-align: center;
    font-size: 10px;
    font-weight: normal;
  }
  .highlight-box div:first-child {
    font-size: 9px;
    margin-bottom: 2px;
  }
  .invoice-info {
    background-color: #e9f1ff;
    padding: 10px 40px;
  }
  .invoice-info-table td {
    font-size: 12px;
    border-right: 1px solid #ccc;
    vertical-align: top;
    padding: 10px;
  }
  .invoice-info-table .delivery-address {
    font-size: 11px;
    padding: 5px;
  }
  .product-table th {
    background-color: #003087;
    color: #fff;
    padding: 10px;
    text-align: left;
  }
  .product-table td {
    padding: 10px;
    border-bottom: 1px solid #ccc;
  }
  .bank-section, .footer-section {
    padding: 20px 30px;
    font-size: 13px;
  }
  .bank-details {
    background-color: #e9f1ff;
    border: 1px solid #ccc;
    padding: 10px;
  }
  .bank-details h4 {
    margin-bottom: 10px;
  }
  .footer-section {
    background-color: #e9f1ff;
     margin-top: 100px;
  }
  .summary-highlight {
    background-color: #003087;
    color: #fff;
    font-weight: bold;
  }
  .text-right {
    text-align: right;
  }
  .text-center {
    text-align: center;
  }
  .text-left {
    text-align: left;
  }
  .currency {
    font-family: monospace;
  }
</style>
</head>
<body>
 <div class="receipt-container">
 <div class="header">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding: 30px 40px;">
    <tr valign="top">
      <!-- Left Column -->
      <td width="50%">
           <img src="https://mds.tech9et.com/public/uploads/all/4YNSioX3IvRAMGUZmCBdam6GZmpxQCmvQj5DO7me.png" alt="My Doctor Shop Logo" style="max-height: 60px;">
        <p style="margin:4px 0; font-size:14px;">📞 03301 133 786</p>
        <p style="margin:4px 0; font-size:14px;">📧 <a href="mailto:ccs@mydoctorshop.com" style="color:#000; text-decoration:none;">ccs@mydoctorshop.com</a></p>
        <p style="margin:4px 0; font-size:14px;">🌐 <a href="http://www.mydoctorshop.com" target="_blank" style="color:#000; text-decoration:none;">www.mydoctorshop.com</a></p>
      </td>

      <!-- Right Column -->
      <td width="50%" style="text-align:right;">
        <h3 style="color:#003087; text-align:left; margin: 0 0 6px 10px; font-size:26px;margin-left:30px;">Sales Receipt</h3>

        <table cellpadding="0" cellspacing="0" style="font-size:14px; width:100%;font-weight: bold;">
          <tr>
            <td style="width:25%; text-align:right; padding: 1px;">Invoice No:</td>
            <td style="width:25%; word-break: break-word; padding: 3px;">
                
             
                </td>
            <td style="width:25%; text-align:right; padding: 1px;">Order Date:</td>
            <td style="width:25%; word-break: break-word; padding: 3px;"></td>
          </tr>
          <tr>
            <td style="width:25%;text-align:right; padding: 1px;">PO No:</td>
            <td style="width:25%;word-break: break-word; padding: 3px;">
             
                </td>
            <td style="width:25%;text-align:right; padding: 1px;">Tracking No:</td>
            <td style="width:25%;word-break: break-word; padding: 3px;">
            </td>
          </tr>
          <tr>
            <td style="width:30%;text-align:right; padding: 1px;">Order Ref No:</td>
            <td style="width:20%;word-break: break-word; padding: 3px;">--</td>
            <td style="width:30%;text-align:right; padding: 1px;">Shipping Term:</td>
            <td style="width:20%;word-break: break-word; padding: 3px;">
         
            </td>
          </tr>
          <tr>
            <td style="width:30%;text-align:right; padding: 1px;">Courier:</td>
            <td style="width:20%;word-break: break-word; padding: 3px;">--</td>

         
            </td>
          </tr>
        </table>

        <br>

        <!-- Highlight Boxes -->
        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:9px;">
          <tr>
            <td align="center">
              <table cellpadding="0" cellspacing="0" style="border-spacing:6px;">
                <tr>
                  <td style="background:#003087; color:#fff; text-align:center; padding:8px 8px; border-radius:4px; font-weight:normal; width:100px;">
                    <div style="font-size:9px; margin-bottom:2px;">Total Amount Due</div>
                    <div style="font-size:10px;">
                        

                    </div>
                  </td>
                  <td style="background:#003087; color:#fff; text-align:center; padding:8px 8px; border-radius:4px; font-weight:normal; width:100px;">
                    <div style="font-size:9px; margin-bottom:2px;">Inv Due Date</div>
                    <div style="font-size:10px;">12/12/23</div>
                  </td>
                  <td style="background:#003087; color:#fff; text-align:center; padding:8px 8px; border-radius:4px; font-weight:normal; width:100px;">
                    <div style="font-size:9px; margin-bottom:2px;">Invoice Date</div>
                    <div style="font-size:10px;">12/12/23</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>

  

    <div class="invoice-info" style="margin-top:20px;">
      <table class="invoice-info-table">
        <tr>
           <td style="border-right:1px solid #ccc; width:15%; word-wrap:break-word;">
              <strong>Invoice To:</strong><br>
            </td>
           <td style="border-right:1px solid #ccc; width:15%; word-wrap:break-word;">
              <strong>Customer Account No:</strong><br>
            </td>
           <td style="border-right:1px solid #ccc; width:15%; word-wrap:break-word;">
              <strong>Delivery Date:</strong><br>
            </td>
           <td style="border:none; width:15%; word-wrap:break-word;">
              <strong>Delivery Address:</strong><br>
            </td>

        </tr>
      </table>
    </div>

    <table class="product-table" style="margin-top:20px;">
      <thead>
        <tr>
          <th>Product Code</th>
          <th>Description</th>
          <th>Order Qty</th>
          <th>Pack Price</th>
          <th>Net Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Product A</td>
          <td>2</td>
          <td class="currency">$50.00</td>
          <td class="currency">$100.00</td>
        </tr>
        <tr>
          <td>Product B</td>
          <td>1</td>
          <td class="currency">$80.00</td>
          <td class="currency">$80.00</td>
        </tr>
        <tr>
          <td>Service C</td>
          <td>3</td>
          <td class="currency">$30.00</td>
          <td class="currency">$90.00</td>
        </tr>
      </tbody>
    </table>

<table width="100%" style="margin-top: 30px;">
  <tr>
    <!-- Left Column: Bank Details -->
            <td style="background:#e9f1ff; padding:10px; width:45%; border:1px solid #ccc;">
              <h4 style="margin-bottom:10px;">Bank Details</h4>
              <table width="100%" cellpadding="4" cellspacing="0" style="font-size:12px;">
                <tr><td>Account Name:</td><td>My Doctor Shop Ltd</td></tr>
                <tr><td>Bank Name:</td><td>Barclays Bank Plc</td></tr>
                <tr><td>Account Number:</td><td>23057054</td></tr>
                <tr><td>Sort Code:</td><td>20-98-98</td></tr>
                <tr><td>IBAN:</td><td>GB62BUKB20998223057054</td></tr>
                <tr><td>SWIFT/BIC:</td><td>BUKBGB22</td></tr>
              </table>
            </td>

 <td width="10%"></td>
    <td style="vertical-align: top; width: 50%; ">
      <table class="summary-table" style="width: 100%;">
        <tr>
          <td class="text-right" style="padding-right: 20px; margin-top:10px;"><strong>Net Amount:</strong></td>
          <td class="text-right currency">$270.00</td>
        </tr>
        <tr>
          <td class="text-right" style="padding-right: 20px;margin-top:20px;"><strong>Carriage Charge:</strong></td>
          <td class="text-right currency">$13.50</td>
        </tr>
        <tr>
          <td class="text-right" style="padding-right: 20px;margin-top:10px;"><strong>Discount</strong></td>
          <td class="text-right currency">$13.50</td>
        </tr>
        <tr>
          <td class="text-right" style="padding-right: 20px;margin-top:10px;"><strong>VAT@20%:</strong></td>
          <td class="text-right currency">$13.50</td>
        </tr>

        <tr class="summary-highlight">
          <td class="text-right" style="padding-right: 20px;margin-top:10px;"><strong>TOTAL AMOUNT DUE:</strong></td>
          <td class="text-right currency">$283.50</td>
        </tr>
      </table>
    </td>
          </tr>
        </table>
      </td>
  </tr>
</table>


    <div class="footer-section">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
          <tr>
            <td style="width:50%;">
              <strong>Company Reg Number:</strong> 9614874<br>
              <strong>VAT Number:</strong> 243929584 Genx Medicare Ltd.<br>
              <p style="margin-top:8px;">
                Terms and Conditions of Sale Apply. Title of goods remains with Genx Medicare Ltd until full payment. Claims must be made in writing within 24 hours. Contact <a href="mailto:cs@genxmedicare.com">cs@genxmedicare.com</a>.
              </p>
            </td>
            <td style="width:50%; text-align:right;">
              <strong>Address:</strong><br>
              Unit 5 Ray Street Enterprise Centre, Ray Street<br>
              Huddersfield, West Yorkshire HD1 6BL
            </td>
          </tr>
        </table>
    </div>
  </div>
</body>
</html>
