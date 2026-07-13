<?php

namespace App\Http\Controllers;

use Auth;
use Mpdf\Mpdf;
use App\Exports\StockReportExport;
use App\Exports\SaleReportExport;
use App\Exports\CancelledOrderReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Search;
use App\Models\Wallet;
use App\Models\Product;
use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\CustomerDetail;
use App\Models\CommissionHistory;

class ReportController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:in_house_product_sale_report'])->only('in_house_sale_report');
        $this->middleware(['permission:seller_products_sale_report'])->only('seller_sale_report');
        $this->middleware(['permission:products_stock_report'])->only('stock_report');
        $this->middleware(['permission:product_wishlist_report'])->only('wish_report');
        $this->middleware(['permission:user_search_report'])->only('user_search_report');
        $this->middleware(['permission:commission_history_report'])->only('commission_history');
        $this->middleware(['permission:wallet_transaction_report'])->only('wallet_transaction_history');
    }
public function sale_report(Request $request)
{
    $date = $request->date;
    $customer_id = $request->customer_id;

    // Fetch customers
    $customers = CustomerDetail::with('contactInformation')->get();

    // Start building query
    $ordersQuery = Order::where('status', 4)
    ->with([
        'orderDetails' => function ($query) {
            $query->where('status', '!=', 10);
        }
    ]);


    if ($request->has('customer_id') && $customer_id) {
        $customerExists = CustomerDetail::where('id', $customer_id)->exists();

        if ($customerExists) {
            $ordersQuery->where('customer_detail_id', $customer_id);
        }
    }

    if ($request->has('date') && $date) {
        $dates = explode(" to ", $date);
        $ordersQuery->whereBetween('created_at', [
            date('Y-m-d 00:00:00', strtotime($dates[0])),
            date('Y-m-d 23:59:59', strtotime($dates[1]))
        ]);
    }

    // Fetch orders for calculations
    $orders = $ordersQuery->get(); // Convert to collection for calculations

    // Initialize totals
    $totalNetAmount = $orders->flatMap->orderDetails->sum('price');
    $tax = $orders->flatMap->orderDetails->sum('tax');
    $shipping_cost = $orders->flatMap->orderDetails->sum('shipping_cost');
    $coupon_discount = $orders->flatMap->orderDetails->sum('coupon_discount');
    $debit = $orders->pluck('account')->flatten()->sum('debit'); // Fixed this line

    // Fetch paginated orders separately to avoid conflicts
    $orders = $ordersQuery->paginate(15);

    // Pass the sums and paginated orders to the view
    return view('backend.reports.sale_report', compact(
        'orders', 'customers', 'date', 'totalNetAmount', 'tax', 'shipping_cost', 'coupon_discount', 'debit'
    ));
}


  public function stock_report(Request $request)
{
    $sort_by = null;

  $products = Product::whereHas('orderDetails', function ($query) use ($request) {
    $query->whereHas('order', function ($q) {
        $q->where('status', 4)           // Include only orders with status = 4
          ->where('status', '!=', 10);   // This line is redundant now (since 4 != 10), but okay if you're checking multiple conditions later
    });


        if ($request->has('date')) {
            $dateRange = explode("to", $request->date);
            $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
            $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();

            $query->whereBetween('created_at', [$from_date, $to_date]);
        }
    })
    ->orderBy('created_at', 'desc')
    ->paginate(15);

    return view('backend.reports.stock_report', compact('products', 'sort_by'));
}

   public function cancelled_report(Request $request)
{
    $sort_by = null;

    $products = Product::whereHas('orderDetails.order', function ($query) use ($request) {
        $query->where('status', 10);
// dd( $products);
        // Optional date filter
        if ($request->has('date')) {
            $dateRange = explode("to", $request->date);
            $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
            $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();

            $query->whereBetween('created_at', [$from_date, $to_date]);
        }
    })
    ->orderBy('created_at', 'desc') // Order products
    ->paginate(15); // Paginate result
    return view('backend.reports.cancelled_order', compact('products', 'sort_by'));
}


  public function pdfStockReport($date)
{
    if ($date === 'default') {
  $products = Product::whereHas('orderDetails', function ($query) use ($request) {
    $query->whereHas('order', function ($q) {
        $q->where('status', 4)           // Include only orders with status = 4
          ->where('status', '!=', 10);   // This line is redundant now (since 4 != 10), but okay if you're checking multiple conditions later
    });
        })->orderBy('created_at', 'desc')->get();
    } else {
        $clean_date = preg_replace('/[^0-9\-to ]/', '', $date);

        if (!str_contains($clean_date, 'to')) {
            return back()->with('error', 'Invalid date format. Use "YYYY-MM-DD to YYYY-MM-DD".');
        }

        $dateRange = explode("to", $clean_date);

        if (count($dateRange) < 2 || empty(trim($dateRange[0])) || empty(trim($dateRange[1]))) {
            return back()->with('error', 'Invalid date range. Please select a valid date range.');
        }

        try {
            $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
            $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid date format. Ensure correct "YYYY-MM-DD to YYYY-MM-DD" format.');
        }

          $products = Product::whereHas('orderDetails', function ($query) use ($from_date, $to_date) {
            $query->whereBetween('created_at', [$from_date, $to_date])
                  ->whereHas('order', function ($q) {
                      $q->where('status', 4)->where('status', '!=', 10);
                  });
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    $view = view('backend.report_download.stock', compact('date', 'products'))->render();
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($view);

    return response()->streamDownload(function () use ($mpdf) {
        $mpdf->Output();
    }, 'stock-report-' . str_replace(' ', '_', $date) . '.pdf');
}

 public function pdfCancelledReport($date)
{
    // Check if the date is set to 'default'
    if ($date == 'default') {
        $products = Product::whereHas('orderDetails', function ($query) {
            $query->whereHas('order', function ($q) {
                $q->where('status', '=', 10); // Only cancelled orders
            });
        })
        ->orderBy('created_at', 'desc')
        ->get();
    } else {
        // Sanitize the date string
        $clean_date = preg_replace('/[^0-9\-to ]/', '', $date);

        // Check if the date string contains "to"
        if (!str_contains($clean_date, 'to')) {
            return back()->with('error', 'Invalid date format. Use "YYYY-MM-DD to YYYY-MM-DD".');
        }

        $dateRange = explode("to", $clean_date);

        // Validate date range format
        if (count($dateRange) !== 2 || empty(trim($dateRange[0])) || empty(trim($dateRange[1]))) {
            return back()->with('error', 'Invalid date range. Please select a valid date range.');
        }

        try {
            // Parse dates and set time boundaries
            $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
            $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid date format. Ensure correct "YYYY-MM-DD to YYYY-MM-DD" format.');
        }

        // Filter products by order details and status within the date range
        $products = Product::whereHas('orderDetails', function ($query) use ($from_date, $to_date) {
            $query->whereBetween('created_at', [$from_date, $to_date])
                  ->whereHas('order', function ($q) {
                      $q->where('status', '=', 10); // Cancelled
                  });
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    // Generate the PDF view
    $view = view('backend.report_download.cancelled', compact('date', 'products'))->render();
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($view);

    // Stream the PDF for download
    return response()->streamDownload(function () use ($mpdf) {
        $mpdf->Output();
    }, 'cancelled-report-' . str_replace(' ', '_', $date) . '.pdf');
}


  public function pdfSaleReport($date, $customer_id = '')
{
    $customers = CustomerDetail::with('contactInformation')->get();

     $ordersQuery = Order::where('status', 4)
    ->with([
        'orderDetails' => function ($query) {
            $query->where('status', '!=', 10);
        }
    ]);

    if (!empty($customer_id)) {
        $customerExists = CustomerDetail::where('id', $customer_id)->exists();
        if ($customerExists) {
            $ordersQuery->where('customer_detail_id', $customer_id);
        }
    }

    if ($date !== 'default') {
        $dates = explode(" to ", $date);
        if (count($dates) === 2) {
            $ordersQuery->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime(trim($dates[0]))),
                date('Y-m-d 23:59:59', strtotime(trim($dates[1])))
            ]);
        }
    }

    // Fetch data
    $orders = $ordersQuery->get();

    // Totals for PDF summary
    $totalNetAmount = $orders->flatMap->orderDetails->sum('price');
    $tax = $orders->flatMap->orderDetails->sum('tax');
    $shipping_cost = $orders->flatMap->orderDetails->sum('shipping_cost');
    $coupon_discount = $orders->flatMap->orderDetails->sum('coupon_discount');
    $debit = $orders->pluck('account')->flatten()->sum('debit');

    // Render view and generate PDF
    $view = view('backend.report_download.sale', compact(
        'date', 'orders', 'customers', 'totalNetAmount', 'tax', 'shipping_cost', 'coupon_discount', 'debit'
    ))->render();

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($view);

    return response()->streamDownload(function () use ($mpdf) {
        $mpdf->Output();
    }, 'sale-report-' . str_replace(' ', '_', $date) . '_.pdf');
}


    public function downloadExcelReport($date)
    {
        return Excel::download(new StockReportExport($date), 'stock-report-' . $date . '_.xlsx');
    }
      public function downloadCancelledReport($date)
    {
        return Excel::download(new CancelledOrderReportExport($date), 'cancelled-order-report-' . $date . '_.xlsx');
    }
    public function downloadExcelReportSale($date,$customer_id = '')
    {
        return Excel::download(new SaleReportExport($date,$customer_id), 'sale-report-' . $date . '_.xlsx');
    }

      
    public function in_house_sale_report(Request $request)
    {
        $sort_by = null;
        $products = Product::orderBy('num_of_sale', 'desc')->where('added_by', 'admin');
        if ($request->has('date')) {
            $dateRange = explode("to", $request->date);
            $from_date = Carbon::parse(trim($dateRange[0]))->startOfDay();
            $to_date = Carbon::parse(trim($dateRange[1]))->endOfDay();
            $products = $products->whereBetween('created_at', [$from_date, $to_date]);
        }
        $products = $products->paginate(15);
        return view('backend.reports.in_house_sale_report', compact('products', 'sort_by'));
    }

    public function seller_sale_report(Request $request)
    {
        $sort_by = null;
        // $sellers = User::where('user_type', 'seller')->orderBy('created_at', 'desc');
        $sellers = Shop::with('user')->orderBy('created_at', 'desc');
        if ($request->has('verification_status')) {
            $sort_by = $request->verification_status;
            $sellers = $sellers->where('verification_status', $sort_by);
        }
        $sellers = $sellers->paginate(10);
        return view('backend.reports.seller_sale_report', compact('sellers', 'sort_by'));
    }

    public function wish_report(Request $request)
    {
        $sort_by = null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')) {
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(10);
        return view('backend.reports.wish_report', compact('products', 'sort_by'));
    }

    public function user_search_report(Request $request)
    {
        $searches = Search::orderBy('count', 'desc')->paginate(10);
        return view('backend.reports.user_search_report', compact('searches'));
    }

    public function commission_history(Request $request)
    {
        $seller_id = null;
        $date_range = null;

        if (Auth::user()->user_type == 'seller') {
            $seller_id = Auth::user()->id;
        }
        if ($request->seller_id) {
            $seller_id = $request->seller_id;
        }

        $commission_history = CommissionHistory::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($seller_id) {

            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }

        $commission_history = $commission_history->paginate(10);
        if (Auth::user()->user_type == 'seller') {
            return view('seller.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
        }
        return view('backend.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
    }

    public function wallet_transaction_history(Request $request)
    {
        $user_id = null;
        $date_range = null;

        if ($request->user_id) {
            $user_id = $request->user_id;
        }

        $users_with_wallet = User::whereIn('id', function ($query) {
            $query->select('user_id')->from(with(new Wallet)->getTable());
        })->get();

        $wallet_history = Wallet::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $wallet_history = $wallet_history->where('created_at', '>=', $date_range1[0]);
            $wallet_history = $wallet_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($user_id) {
            $wallet_history = $wallet_history->where('user_id', '=', $user_id);
        }

        $wallets = $wallet_history->paginate(10);

        return view('backend.reports.wallet_history_report', compact('wallets', 'users_with_wallet', 'user_id', 'date_range'));
    }
}
