<?php

namespace App\Http\Controllers;

use Mail;
use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Accounts;
use App\Models\Remittance;
use Illuminate\Http\Request;
use App\Models\CustomerDetail;
use App\Models\User;
use App\Mail\InvoiceEmailManager;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;
use Artisan;

class InvoicePayableController extends Controller
{
public function invoicePayable(Request $request)
{
    $query = Accounts::where('debit', '!=', '0')
                     ->where('status', '1');

    // Add search
    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                   ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->orWhereHas('order', function ($oq) use ($search) {
                $oq->where('code', 'like', "%{$search}%")           // PO Number
                   ->orWhere('invoice_number', 'like', "%{$search}%")
                   ->orWhere('tracking_code', 'like', "%{$search}%");
            })
            // You can add more: company_name, transactionId, etc.
            // Example for company_name:
            ->orWhereHas('user.registerCredit', function ($cq) use ($search) {
                $cq->where('company_name', 'like', "%{$search}%");
            });
        });
    }

    $data["orders"] = $query->orderBy('id', "desc")->paginate(10);

    // Keep old query string (for pagination links to remember search)
    $data["search"] = $search;

    return view('backend.customer.account_payable.invoice_payable', $data);
}

       public function paymentException(Request $request)
{
    $search = $request->search;

    $data['orders'] = Accounts::with(['order', 'user'])
        ->where('status', '2')

        // remittance payment empty
        ->where(function ($q) {
            $q->whereNull('remmittan_payment_number')
              ->orWhere('remmittan_payment_number', '');
        })

        // comments empty
        ->where(function ($q) {
            $q->whereNull('comments')
              ->orWhere('comments', '');
        })

        // 🔍 SEARCH LOGIC
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {

                // Invoice number
                $q->whereHas('order', function ($o) use ($search) {
                    $o->where('invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%")        // PO Number
                      ->orWhere('tracking_code', 'LIKE', "%{$search}%");
                })

                // Customer name
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                })

                // Company name (credit customers)
                ->orWhereHas('user.registerCredit', function ($c) use ($search) {
                    $c->where('company_name', 'LIKE', "%{$search}%");
                });

            });
        })

        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends($request->query()); // 🔥 pagination + search together

    $data['remittance'] = Remittance::where('status', '2')->paginate(10);

    return view('backend.customer.account_payable.payment_exceptions', $data);
}

 public function paymentConfirmation(Request $request)
{
    $search = $request->search;

    $data['orders'] = Accounts::with(['order', 'user'])
        ->where('status', '3')
        ->where('debit', '0')

        // 🔍 SEARCH LOGIC
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {

                // Order related search
                $q->whereHas('order', function ($o) use ($search) {
                    $o->where('invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%")          // PO Number
                      ->orWhere('tracking_code', 'LIKE', "%{$search}%");
                })

                // Customer name search
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                })

                // Company name (credit customers)
                ->orWhereHas('user.registerCredit', function ($c) use ($search) {
                    $c->where('company_name', 'LIKE', "%{$search}%");
                });

            });
        })

        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends($request->query()); // 🔥 keep search on pagination

    $data['remittance'] = Remittance::where('status', '1')->paginate(10);

    return view('backend.customer.account_payable.payment_confirmation', $data);
}


    public function paymentOverdue()
    {
        //  Artisan::call('storage:link');

        // exec('chmod -R 775 storage');

        $data["orders"] = Accounts::where('status', "!=", '3')
            ->where('status', "!=", '5')
            ->orderBy('id', "desc")
            ->paginate(10);
        $data["customer"] = CustomerDetail::all();

        return view('backend.customer.account_payable.payment_overdue')->with($data);
    }
      public function invoicePaid()
    {
        $data["orders"] = Accounts::where('debit', "=", '0')->where('status', '5')->orderBy('id', "desc")->paginate(10);
        $data["remittance"] = Remittance::where('status', '5')->orderBy('id', "desc")->paginate(10);

        return view('backend.customer.account_payable.invoice_paid')->with($data);
    }

    public function update(Request $request)
    {
      
        // $request->validate([
        //     'account_id' => 'required|exists:accounts,id',
        //     'payment_date' => 'required|date',
        //     'payment_ref' => 'required|string|max:255',
        //     'payment_amount' => 'required|numeric',
        // ]);

        $accounts_val = Accounts::where('order_id', '=', $request->account_id)->first();
       
        // dd($accounts_val->customer_detail_id);
        // Find the record by account_id
        $account = new Accounts;

        // Update the record
        $account->order_id = $request->account_id;

        $account->customer_detail_id = $accounts_val->customer_detail_id;

        $account->debit = 0;
        $account->credit = $request->payment_amount;
        $account->due_date = $accounts_val->due_date;

        $account->payment_date = $request->payment_date;

        $account->comments = $request->payment_ref;
        // $account->status = $request->remaining_amount == 0?3:2;

        // Handle PDF upload if provided
        if ($request->hasFile('payment_pdf')) {
            $filePath = $request->file('payment_pdf')->store('uploads', 'public');
            $account->attachment = $filePath;
        }

        $account->save();


            if ($request->remaining_amount == 0) {
                Accounts::where('order_id', '=', $request->account_id)->update(['status' => 3,'remmittan_payment_number'=>$request->payment_ref]);
            } else {
                Accounts::where('order_id', '=', $request->account_id)->update(['status' => 2]);
            }
        //       $remittance = new Remittance();
        // $remittance->customer_detail_id = $accounts_val->customer_detail_id;
        // $remittance->payment_date = $request->payment_date;
        // $remittance->payment_ref = $request->input('payment_ref');
        // $remaining_invoice = $request->input("remaining_amount");
        // $paid_amount = $request->input("payment_amount");

        // $remittance->add_remittance_value = $request->input('due_amount');
        // $remittance->remaining_invoice = $remaining_invoice;
        // $remittance->paid_amount = $paid_amount;
        // $remittance->status = $remaining_invoice == 0 ? 1 : 2;
        //         $remittance->save();
        flash('Payment details updated successfully.')->success();

        return redirect()->back()->with('success', 'Payment details updated successfully.');
    }

    public function send_invoice(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $array['view'] = 'emails.invoice';
        $array['subject'] = 'Reminder Overdue Invoice - ' . $order->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['order'] = $order;

        if (env('MAIL_USERNAME') != null) {
            try {
                Mail::to($request->email)->queue(new InvoiceEmailManager($array));
                Accounts::where('order_id', '=', $request->order_id)->update(["last_sending_mail" => 1]);
            } catch (\Exception $e) {
            }
        }
        return response()->json([
            'id' => 1,
            'message' => "sent",
        ]);
    }
    public function send_statement(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $invoice = Accounts::where('order_id', '=', $request->order_id)->get();

        $array['view'] = 'emails.statement';
        $array['subject'] = 'Order Statement - ' . $order->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['order'] = $order;
        $array['invoices'] = $invoice;

        if (env('MAIL_USERNAME') != null) {
            try {
                Mail::to($request->email)->queue(new InvoiceEmailManager($array));
                Accounts::where('order_id', '=', $request->order_id)->update(["last_sending_mail" => 1]);
            } catch (\Exception $e) {
                dd($e);
            }
        }
        return response()->json([
            'id' => 1,
            'message' => "sent",
        ]);
    }


    public function confirmation($id)
    {
        $order = Accounts::where('order_id', '=', $id);
        $order->status = '3';
        $order->save();
        return redirect()->with('success', 'Payment confirmation complete');
    }

    public function remittance_form()
    {
  $customer = User::whereIn('user_type', ['customer', 'customer_credit'])->get();
        return view('backend.customer.account_payable.add_remittance', compact('customer'));
    }
    // public function searchInvoice(Request $request)
    // {
    //     $invoice = $request->invoice;
    //     $order = Order::where('invoice_number', $invoice)->first();
    //     $account_data = Accounts::where ( 'order_id', $order->id)->first();


    //     if ($order) {
    //         return response()->json(['id' => $account_data->order_id ,'po_number'=>$order->purchase_order_number,'tracking_number'=>$order->tracking_code,'due_amount'=>due_price($account_data->order_id)]);
    //     } else {
    //         return response()->json(['due_amount' => null]);
    //     }
    // }
    public function searchCustomer(Request $request)
    {
        $customerId = $request->customer_id;

        if ($customerId) {
            return response()->json(['due_amount' => due_price_customer($customerId)]);
        } else {
            return response()->json(['due_amount' => null]);
        }
    }
    public function searchInvoiceExcep(Request $request)
    {
        $invoiceNumber = $request->invoice;


        if ($invoiceNumber) {
            $orders = Order::where('invoice_number', 'LIKE', "%{$invoiceNumber}%")
                ->with('customer_details.contactInformation')
                ->get();
        } else {

            $orders = collect();
        }

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->user->name . ' ' . $order->user->last_name,
                'po_number' => $order->code ?? 'N/A',
                'invoice_number' => $order->invoice_number ?? 'N/A',
                'tracking_number' => $order->tracking_code ?? 'N/A',
                'payment_due_date' => $order->due_date ?? 'N/A',
                'due_payment' => due_price($order->id) ?? 'N/A',
            ];
        });

        return response()->json(['orders' => $formattedOrders]);
    }
    public function searchInvoice(Request $request)
    {
        $invoiceNumber = $request->invoice;


        if ($invoiceNumber) {
            $orders = Order::where('invoice_number', 'LIKE', "%{$invoiceNumber}%")
                ->with('customer_details.contactInformation')
                ->get();
        } else {

            $orders = collect();
        }

        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->user->name . ' ' . $order->user->last_name,
                'po_number' => $order->code ?? 'N/A',
                'invoice_number' => $order->invoice_number ?? 'N/A',
                'tracking_number' => $order->tracking_code ?? 'N/A',
                'payment_due_date' => $order->due_date ?? 'N/A',
                'due_payment' => due_price($order->id) ?? 'N/A',
            ];
        });

        return response()->json(['orders' => $formattedOrders]);
    }


    public function remittanceStore(Request $request)
    {
        // dd($request->input('customer_id'));

        $remittance = new Remittance();
        $remittance->customer_detail_id = $request->input('customer_id');
        $remittance->payment_date = $request->input('payment_date');
        $remittance->payment_ref = $request->input('remmittan_payment_number');
        $remaining_invoice = $request->input("remaining_invoice");
        $paid_amount = $request->input("total_invoice");

        $remittance->add_remittance_value = $request->input('due_amount');
        $remittance->remaining_invoice = $remaining_invoice;
        $remittance->paid_amount = $paid_amount;
        $remittance->status = $remaining_invoice == 0 ? 1 : 2;
        // dd($remittance->add_remittance_value);
        if ($request->hasFile('payment_pdf')) {
            $filePath = $request->file('payment_pdf')->store('uploads', 'public');
            $remittance->PDF = $filePath;
        }
        $remittance->save();
        $accounts = Accounts::where('remmittan_payment_number', '=', $request->input('remmittan_payment_number'))->get();
        foreach ($accounts as $key => $value) {
            if ($remaining_invoice == 0) {
                Accounts::where('order_id', '=', $value->order_id)->update(['status' => 3]);
            } else {
                Accounts::where('order_id', '=', $value->order_id)->update(['status' => 2]);
            }
        }
        return response()->json(['message' => "sent"]);
    }
    public function excep_amount_view($id)
    {
        $accountData = Accounts::where('order_id', $id)  ->where(function($q){
        $q->whereNull('debit')
          ->orWhere('debit', '');
    })->get();


        if (!$accountData) {
            return redirect()->back()->with('error', 'Remittance not found.');
        }

        // Pass the data to the view
        return view('backend.customer.account_payable.excep_amount_view', compact('accountData'));
    }
   public function orderReturn($id)
{
    Accounts::where('order_id', $id)
        ->where(function ($q) {
            $q->whereNull('debit')
              ->orWhere('debit', '');
        })
        ->delete();

    $dataStatus = Accounts::where('order_id', $id)
        ->where(function ($q) {
            $q->whereNull('credit')
              ->orWhere('credit', '');
        })
        ->first();

    if ($dataStatus) {
        $dataStatus->status = 1;
        $dataStatus->save();
    }
     

     return back();
}

    public function payment_ref($payment_ref)
    {
        $accountData = Accounts::where('remmittan_payment_number', $payment_ref)->get();

        $remittance = Remittance::where('payment_ref', $payment_ref)->first(); // Using where() if 'payment_ref' isn't the primary key

        if (!$remittance) {
            return redirect()->back()->with('error', 'Remittance not found.');
        }

        // Pass the data to the view
        return view('backend.customer.account_payable.remittance_view', compact('accountData', 'remittance'));
    }



    public function reUpdate(Request $request)
    {
        $order_id = $request->input('account_id');
        $order = Order::where('id', $order_id)->first();
        // dd($order);
        // $customer = $request->input('customer_name');
        // $customer = CustomerDetail::where('first_name', $customer)->first();

        $remaining = $request->input('remaining_amount');
        $account_data = new Accounts;
        $account_data->order_id = $order_id;
        $account_data->customer_detail_id = $order->user_id;
        $account_data->credit = $request->input('payment_amount');
        $account_data->debit = 0;
        $account_data->payment_date = $request->payment_date;
        $account_data->comments = $request->payment_ref;
        $account_data->remmittan_payment_number = $request->remmittan_payment_number;

        $account_data->amount_withheld = $request->payment_held;

        if ($request->hasFile('payment_pdf')) {
            $filePath = $request->file('payment_pdf')->store('uploads', 'public');
            $account_data->attachment = $filePath;
        }


        if ($account_data->save()) {

            return response()->json([
                'customer_name' =>$order->user->name . ' ' . $order->user->last_name,
                'invoice_number' => $order->invoice_number,
                'invoice_date' => $request->payment_date,
                'due_date' => Accounts::where("order_id", '=', $order_id)->where("debit", '!=', null)->first()->due_date,
                'purchase_order_number' => $order->code,
                'remaining_amount' => $remaining,
                'net_amount' => $request->input('payment_amount'),
                'total_amount' => $request->input('payment_amount'),
            ]);
        } else {
            return response()->json(['due_amount' => null]);
        }
    }

    public function confirm(Request $request)
    {

        if ($request->input("request") == "invoice") {

            $account = Accounts::find($request->input("prim_id"));
            // Find the record by account_id
            $account->status = 5;
            // Update the record

            $account->save();
        } else {

            $account = Remittance::find($request->input("rem_id"));
            // Find the record by account_id
            $account->status = 5;
            // Update the record

            $account->save();
            $invoices = Accounts::where("remmittan_payment_number", "=", $request->input("payment_ref"))->get();
            foreach ($invoices as $key => $invoice) {
                Accounts::where("order_id", "=", $invoice->order_id)->update(["status" => 2]);
            }
        }

        flash('Payment confirm successfully.')->success();

        return redirect()->back()->with('success', 'Payment details updated successfully.');
    }

    // Filter Date
    public function filterOrders(Request $request)
    {
        $dateRange = $request->input('date');

        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);

            $startDate = Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d');

            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->with(['customer_details.contactInformation'])
                ->get()
                ->map(function ($order) {
                    return [
                        'customer_name' => $order->user->name . ' ' . $order->user->last_name,
                        'invoice_number' => $order->invoice_number ?? 'N/A',
                        'code' => $order->code ?? 'N/A',
                        'due_date' => $order->due_date,
                        'due_price' => due_price($order->id),
                        'order_id' => $order->id,
                        'customer_email' => $order->customer_details->accountPayable->first()->confirmation_email ?? null,
                    ];
                });
        } else {
            $orders = [];
        }

        return response()->json(['orders' => $orders]);
    }

    public function send_customers_statement(Request $request)
{
    // try {
        // Parse the date range
        // $dateRange = explode("to", $request->date);
        // $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
        // $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();

        // Retrieve invoices for the customer
        $invoices = Accounts::where('customer_detail_id', '=', $request->customer_id)
            ->whereNotIn('status', ['3', '5'])
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->get();

        // Check if invoices exist
        if ($invoices->isEmpty()) {
            return back()->with('error', 'No records found!');
        }

        // Get customer email
        $order = Order::find($invoices->first()->order_id);
        $email = optional($order->customer_details->accountPayable->first())->confirmation_email;

        if (!$email) {
            return back()->with('error', 'Customer email not found!');
        }

        // ✅ Generate PDF with mPDF
        $view = view('backend.invoices.statement', compact('order', 'invoices'))->render();
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($view);

        // ✅ Save PDF to storage
        $pdfFilePath = storage_path('app/public/order-statement.pdf');
        $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

        // ✅ Generate Excel file
        $excelFilePath = public_path('public/order-statement.xlsx');
        Excel::store(new InvoicesExport($invoices), 'public/order-statement.xlsx');

        // // ✅ Check if files exist
        // if (!file_exists($pdfFilePath) || !file_exists($excelFilePath)) {
        //     return back()->with('error', 'Failed to generate statement files.');
        // }

        // ✅ Prepare Email Data
        $array = [
            'view' => 'emails.customer_statement',
            'subject' => 'Customer Overdue Statement - ' . $request->date,
            'from' => env('MAIL_USERNAME'),
            'order' => $order,
            'invoices' => $invoices,
        ];

        // ✅ Send Email with Attachments
        Mail::to($email)->send(new InvoiceEmailManager($array, $pdfFilePath, $excelFilePath));

        // ✅ Update sending status
        Accounts::where('customer_detail_id', '=', $request->customer_id)
            ->update(['last_sending_statement' => 1]);

        // ✅ Cleanup: Delete the temporary files
        unlink($pdfFilePath);
        unlink($excelFilePath);
        flash('Send successfully.')->success();
        return back()->with('success', 'Statement sent successfully!');
    // } catch (\Exception $e) {
    //     flash($e->getMessage())->error();
    //     return back()->with('error', 'Error: ' . $e->getMessage());
    // }
}

    // public function send_customers_statement(Request $request)
    // {
        
    //     $dateRange = explode("to", $request->date);
    //     $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
    //     $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();


    //     $invoices = Accounts::where('customer_detail_id', '=', $request->customer_id)
    //     ->where('status', "!=", '3')
    //     ->where('status', "!=", '5')
    //     ->where('due_date', "<", date("Y-m-d"))
    //     ->whereBetween('created_at', [$from_date, $to_date])
    //     ->get();


        
    //     $email = '';
    //     if (isset($invoices[0])) {
    //         $order = Order::find($invoices[0]->order_id);
    //         $email = $order->customer_details->accountPayable->first()->confirmation_email;
    //     }else{
    //         flash('Record does not exist!')->error();
    //     }
    //     // $order = Order::find($invoices[0]->order_id);
    //     // $email = $order->customer_details->accountPayable->first()->confirmation_email;
    //     // Generate PDF with mPDF
    //     $view = view('emails.statement', compact('order', 'invoices'))->render();
    //     $mpdf = new Mpdf();
    //     $mpdf->WriteHTML($view);

    //     // Save PDF to a temporary file
    //     $filePath = storage_path('app/public/order-statement.pdf');
    //     $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

    //     // Generate Excel file
    //     $excelFilePath = storage_path('app/public/order-statement.xlsx');
    //     Excel::store(new InvoicesExport($invoices), 'public/order-statement.xlsx');

    //     // Email configuration
    //     $array = [
    //         'view' => 'emails.customer_statement', // Blade view for the email
    //         'subject' => 'Customer Statement - ' . $request->date,
    //         'from' => env('MAIL_USERNAME'),
    //         'order' => $order,
    //         'invoices' => $invoices,
    //     ];

    //     // Check if mail is configured
    //     if (env('MAIL_USERNAME') != null) {
    //         try {
    //             Mail::to($email)->send(new InvoiceEmailManager($array, $filePath, $excelFilePath));

    //             // Update the last sending status
    //             Accounts::where('customer_detail_id', '=', $request->customer_id)
    //                 ->update(['last_sending_statement' => 1]);
    //         } catch (\Exception $e) {
    //             dd($e->getMessage()); // For debugging, shows the error
    //         } finally {
    //             // Cleanup: Delete the temporary PDF file
    //             if (file_exists($filePath)) {
    //                 unlink($filePath);
    //             }
    //             if (file_exists($excelFilePath)) {
    //                 unlink($excelFilePath);
    //             }

    //         }
    //     }

    //     flash('Send successfully.')->success();
    //     return redirect()->back()->with('success', 'Payment details updated successfully.');

    // }
}
