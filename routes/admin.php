<?php

use App\Http\Controllers\PosController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ClientReviewController;

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\AizUploadController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\FlashDealController;
use App\Http\Controllers\SizeChartController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\PickupPointController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\GuestAccountController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductQueryController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\customerCreditController;
use App\Http\Controllers\DigitalProductController;
use App\Http\Controllers\InvoicePayableController;
use App\Http\Controllers\RegisterCreditController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\CustomerProductController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\MeasurementPointsController;
use App\Http\Controllers\ProductBulkUploadController;
use App\Http\Controllers\CommercialCustomerController;
use App\Http\Controllers\SellerWithdrawRequestController;
use App\Http\Controllers\pharmaceuticalCustomerController;
use App\Http\Controllers\InternationalAccountController;
use App\Models\Banner;
use App\Models\Order;

/*
  |--------------------------------------------------------------------------
  | Admin Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register admin routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
//Update Routes
Route::controller(UpdateController::class)->group(function () {
    Route::post('/update', 'step0')->name('update');
    Route::get('/update/step1', 'step1')->name('update.step1');
    Route::get('/update/step2', 'step2')->name('update.step2');
    Route::get('/update/step3', 'step3')->name('update.step3');
    Route::post('/purchase_code', 'purchase_code')->name('update.code');
});

Route::get('/admin', [AdminController::class, 'admin_dashboard'])->name('admin.dashboard')->middleware(['auth', 'admin']);
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {

    // category
    Route::resource('categories', CategoryController::class);
Route::post('categories/get-children', [CategoryController::class, 'getChildren'])
         ->name('categories.get-children');
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories/edit/{id}', 'edit')->name('categories.edit');
        Route::get('/categories/destroy/{id}', 'destroy')->name('categories.destroy');
        Route::post('/categories/featured', 'updateFeatured')->name('categories.featured');
        Route::post('/categories/status-update', 'updateStatus')->name('categories.status');
        Route::post('/categories/seller-update', 'sellerStatus')->name('seller.status');
        Route::post('/categories/save-big', 'updateSaveBig')->name('categories.save_big');
        Route::post('/categories/categoriesByType', 'categoriesByType')->name('categories.categories-by-type');
        Route::post('/upload-editor-file', 'uploadImage')->name('admin.editor.upload');
    });
    Route::controller(BannerController::class)->group(function () {
        //Hero Bannars
        Route::get('/bannar/hero/index', 'heroIndex')->name('heroBannar.index');
        Route::get('/bannar/hero/create', 'heroCreate')->name('heroBannar.create');
        Route::post('/bannar/hero/store', 'heroStore')->name('heroBannar.store');
        Route::get('/bannar/hero/edit/{id}', 'heroEdit')->name('heroBannar.edit');
        Route::post('/bannar/hero/update', 'heroUpdate')->name('heroBannar.update');
        Route::get('/bannar/hero/delete/{id}', 'heroDelete')->name('heroBannar.delete');
        Route::post('/banner/hero/status', 'status')->name('heroStatus.update');
        // Middle Bannar
        Route::get('/bannar/middle/index', 'middleIndex')->name('middleBannar.index');
        Route::get('/bannar/middle/create', 'middleCreate')->name('middleBannar.create');
        Route::post('/bannar/middle/store', 'middleStore')->name('middleBannar.store');
        Route::get('/bannar/middle/edit/{id}', 'middleEdit')->name('middleBannar.edit');
        Route::post('/bannar/middle/update', 'middleUpdate')->name('middleBannar.update');
        Route::get('/bannar/middle/delete/{id}', 'middleDelete')->name('middleBannar.delete');
        Route::post('/banner/middle/status', 'status')->name('middleStatus.update');
        // monthly bannar
        Route::get('/bannar/monthly/index', 'monthlyIndex')->name('monthlyBannar.index');
        Route::get('/bannar/monthly/create', 'monthlyCreate')->name('monthlyBannar.create');
        Route::post('/bannar/monthly/store', 'monthlyStore')->name('monthlyBannar.store');
        Route::get('/bannar/monthly/edit/{id}', 'monthlyEdit')->name('monthlyBannar.edit');
        Route::post('/bannar/monthly/update', 'monthlyUpdate')->name('monthlyBannar.update');
        Route::get('/bannar/monthly/delete/{id}', 'monthlyDelete')->name('monthlyBannar.delete');
        Route::post('/banner/monthly/status', 'status')->name('monthlyStatus.update');

        // last bannar
        Route::get('/bannar/last/index', 'lastIndex')->name('lastBannar.index');
        Route::get('/bannar/last/create', 'lastCreate')->name('lastBannar.create');
        Route::post('/bannar/last/store', 'lastStore')->name('lastBannar.store');
        Route::get('/bannar/last/edit/{id}', 'lastEdit')->name('lastBannar.edit');
        Route::post('/bannar/last/update', 'lastUpdate')->name('lastBannar.update');
        Route::get('/bannar/last/delete/{id}', 'lastDelete')->name('lastBannar.delete');
        Route::post('/banner/last/status', 'status')->name('lastStatus.update');

        // Best seller bannar
        Route::get('/bannar/best-seller/index', 'BestSellerIndex')->name('BestSellerBannar.index');
        Route::get('/bannar/best-seller/create', 'BestSellerCreate')->name('BestSellerBannar.create');
        Route::post('/bannar/best-seller/store', 'BestSellerStore')->name('BestSellerBannar.store');
        Route::get('/bannar/best-seller/edit/{id}', 'BestSellerEdit')->name('BestSellerBannar.edit');
        Route::post('/bannar/best-seller/update', 'BestSellerUpdate')->name('BestSellerBannar.update');
        Route::get('/bannar/best-seller/delete/{id}', 'BestSellerDelete')->name('BestSellerBannar.delete');
        Route::post('/banner/best-seller/status', 'status')->name('BestSellerStatus.update');

        // Trending bannar
        Route::get('/bannar/trending/index', 'trendingIndex')->name('trendingBannar.index');
        Route::get('/bannar/trending/create', 'trendingCreate')->name('trendingBannar.create');
        Route::post('/bannar/trending/store', 'trendingStore')->name('trendingBannar.store');
        Route::get('/bannar/trending/edit/{id}', 'trendingEdit')->name('trendingBannar.edit');
        Route::post('/bannar/trending/update', 'trendingUpdate')->name('trendingBannar.update');
        Route::get('/bannar/trending/delete/{id}', 'trendingDelete')->name('trendingBannar.delete');
        Route::post('/banner/trending/status', 'status')->name('trendingStatus.update');
    });
    // Bannars


    //===================================== Partner Section ===========================

    Route::resource('partners', PartnerController::class);
    Route::get('/partners/destroy/{id}', [PartnerController::class, 'destroy'])->name('partners.destroy');
    Route::post('/partners/change-status/', [PartnerController::class, 'change_status'])->name('partners.change-status');

    //==================================Blog Section =================================

    Route::resource('blog-category', BlogCategoryController::class);
    Route::get('/blog-category/destroy/{id}', [BlogCategoryController::class, 'destroy'])->name('blog-category.destroy');

    // Blog
    Route::resource('blog', BlogController::class);
    Route::controller(BlogController::class)->group(function () {
        Route::get('/blog/destroy/{id}', 'destroy')->name('blog.destroy');
        Route::post('/blog/change-status', 'change_status')->name('blog.change-status');
    });


    //===================================== Client Review Section ====================

    Route::resource('client-reviews', ClientReviewController::class);
    Route::get('/client-reviews/destroy/{id}', [ClientReviewController::class, 'destroy'])->name('client-reviews.destroy');
    Route::post('/client-reviews/change-status/', [ClientReviewController::class, 'change_status'])->name('client-reviews.change-status');


    //====


    // account
    Route::get('/commercial_account_index', [CommercialCustomerController::class, 'commercial_account_index'])->name('commercial_account_index');
    Route::get('/commercial_account_view/{id}', [CommercialCustomerController::class, 'commercial_account_view'])->name('commercial_account_view');
    Route::get('/commercial_account/{id?}', [CommercialCustomerController::class, 'commercial_account'])->name('commercial_account');
    Route::post('/delivery-address/delete/', [CommercialCustomerController::class, 'deliveryDestroy'])->name('delivery-address.delete');
    Route::get('/commercial_destroy/{id}', [CommercialCustomerController::class, 'commercialDestroy'])->name('commercial_account_destroy');

    // Customer Detail

    Route::post('/customer-details/store', [CommercialCustomerController::class, 'storeCustomerDetails'])->name('customer-details.store');
    Route::post('/Head-Office/store', [CommercialCustomerController::class, 'storeHeadOffice'])->name('head-office.store');
    Route::post('/Register-Office/store', [CommercialCustomerController::class, 'storeRegisterOffice'])->name('register-office.store');
    Route::post('/Delivery-Address/store', [CommercialCustomerController::class, 'storeDeliveryAddress'])->name('delivery-address.store');
    //

    Route::post('/delivery-address/{id}/edit', [CommercialCustomerController::class, 'deliveryEdit'])->name('delivery-address.edit');
    Route::post('/delivery_credit_address/{id}/edit', [RegisterCreditController::class, 'deliveryCreditEdit'])->name('delivery-credit.edit');
    // Route::put('/delivery-address/{id}/update', [CommercialCustomerController::class, 'update']);

    //Acount Aprrovel credit
    Route::post('/reject_account', [RegisterCreditController::class, 'reject_account'])->name('rejectAccount');
    Route::post('/register_reject_account', [RegisterCreditController::class, 'register_reject_account'])->name('registerRejectAccount');
    Route::post('/approved_account', [RegisterCreditController::class, 'approved_account'])->name('approvedAccount');
    //Acount Aprrovel pharma
    Route::post('/reject_account_pharma', [pharmaceuticalCustomerController::class, 'reject_account_pharma'])->name('rejectAccount_pharma');
    Route::post('/approved_account_pharma', [pharmaceuticalCustomerController::class, 'approved_account_pharma'])->name('approvedAccount_pharma');

    Route::post('/Contact-Information/store', [CommercialCustomerController::class, 'storeContactInformation'])->name('contact-information.store');
    Route::post('/login/store', [CommercialCustomerController::class, 'storeLogin'])->name('login.store');
    Route::post('/shipping-form/store', [CommercialCustomerController::class, 'storeShippingTerm'])->name('shipping_term.store');
    Route::post('/account-payable/store', [CommercialCustomerController::class, 'storeAccountPayable'])->name('account_payable.store');

    Route::post('/delivery-update/{id}', [CommercialCustomerController::class, 'update']);
    Route::post('/delivery-credit-update/{deliveryId}', [RegisterCreditController::class, 'updateCredit'])
        ->name('delivery_credit_update'); // customer Credit
    // Route::get('/customer_credit', [RegisterCreditController::class, 'customerCredit'])->name('customer_credit.create');
    Route::get('/customer_credit_list', [RegisterCreditController::class, 'customerCreditList'])->name('customer_credit.list');
    Route::get('/customer_register_list', [RegisterCreditController::class, 'customerRegisterList'])->name('customer_register.list');
    Route::post('/credit-delivery-address/delete/', [RegisterCreditController::class, 'deliveryDestroy'])->name('delivery-address.delete');
    Route::get('/customer_guest.list', [GuestAccountController::class, 'customerGuestList'])->name('customer_guest.list');
    Route::get('/customer_guest.view/{id}', [GuestAccountController::class, 'customerGuestView'])->name('customer_guest.view');
    Route::get('/customer_guest.update/{id}', [GuestAccountController::class, 'customerGuestUpdate'])->name('customer_guest.update');
    Route::get('/credit_customer/{id?}', [RegisterCreditController::class, 'credit_customer'])->name('credit_customer');
    Route::get('/register_customer/{id?}', [RegisterCreditController::class, 'register_customer'])->name('register_customer');
    Route::get('/guest_customer/{id?}', [GuestAccountController::class, 'guest_customer'])->name('guest_customer');
    Route::get('/credit_customer_view/{id}', [RegisterCreditController::class, 'credit_customer_view'])->name('credit_customer_view');
    Route::get('/register_customer_view/{id}', [RegisterCreditController::class, 'register_customer_view'])->name('register_customer_view');
    Route::get('/credit_customer_delete/{id}', [RegisterCreditController::class, 'credit_customer_delete'])->name('credit_customer_delete');
    Route::get('/register_customer_delete/{id}', [RegisterCreditController::class, 'register_customer_delete'])->name('register_customer_delete');
    Route::get('/customer_guest_delete/{id}', [GuestAccountController::class, 'customer_guest_delete'])->name('customer_guest_delete');
    Route::post('/customer_credit_store', [RegisterCreditController::class, 'customerCreditStore'])->name('customer_credit.store');
    Route::post('/customer_register_store', [RegisterCreditController::class, 'customerRegisterStore'])->name('customer_register.store');
    Route::post('/customer_guest_store', [GuestAccountController::class, 'customerGuestStore'])->name('customer_guest.store');
    Route::post('/delivery_form_store', [RegisterCreditController::class, 'deliveryFormStore'])->name('delivery_form.store');
    Route::post('/delivery_guest_form', [GuestAccountController::class, 'delivery_guest_form'])->name('delivery_guest_form.store');
    Route::post('/changeStatus', [RegisterCreditController::class, 'changeStatusCredit'])->name('changeStatus');
    Route::post('/changeStatus1', [RegisterCreditController::class, 'changeStatusCredit1'])->name('changeStatus1');
    Route::post('/changeStatus3', [GuestAccountController::class, 'changeStatus3'])->name('changeStatus3');

    Route::post('/changeStatus', [RegisterCreditController::class, 'changeStatusRegister'])->name('RegisterStatus');


    // internation customer 

    Route::controller(InternationalAccountController::class)->group(function () {
        Route::post('/delivery_credit_address/{id}/edit',  'deliveryCreditEdit')->name('delivery-credit.edit');
        Route::post('/delivery-credit-update/{deliveryId}',  'updateCredit')->name('delivery_credit_update');
        Route::post('/reject_account',  'reject_account')->name('rejectAccount');
        Route::post('/register_reject_account',  'register_reject_account')->name('registerRejectAccount');
        Route::post('/approved_account',  'approved_account')->name('approvedAccount');
        Route::get('/customer_international_list',  'customerCreditList')->name('international.customer_credit.list');
        // Route::get('/customer_register_list',  'customerRegisterList')->name('customer_register.list');
        Route::post('/credit-delivery-address/delete/',  'deliveryDestroy')->name('delivery-address.delete');
        Route::get('/credit_customer/{id?}',  'credit_customer')->name('credit_customer');
        Route::get('/international_register_customer/{id?}',  'register_customer')->name('international_register_customer');
        Route::get('/credit_customer_view/{id}',  'credit_customer_view')->name('credit_customer_view');
        Route::get('/internaional_register_customer_view/{id}',  'register_customer_view')->name('international_register_customer_view');
        Route::get('/credit_customer_delete/{id}',  'credit_customer_delete')->name('credit_customer_delete');
        Route::get('/international_register_customer_delete/{id}',  'register_customer_delete')->name('international_register_customer_delete');
        Route::post('/customer_credit_store',  'customerCreditStore')->name('customer_credit.store');
        Route::post('/customer_register_store',  'customerRegisterStore')->name('customer_register.store');
        Route::post('/delivery_form_store',  'deliveryFormStore')->name('delivery_form.store');
        Route::post('/changeStatus',  'changeStatusCredit')->name('changeStatus');
        Route::post('/changeStatus1',  'changeStatusCredit1')->name('changeStatus1');
        Route::post('/changeStatus',  'changeStatusRegister')->name('RegisterStatus');
    });


    // end Internation





    Route::get('/pharmaceutical_account/{id?}', [pharmaceuticalCustomerController::class, 'pharmaceuticalAccount'])->name('pharmaceutical_account');
    Route::get('/pharmaceutical_account_view/{id}', [pharmaceuticalCustomerController::class, 'pharmaceuticalAccountView'])->name('pharmaceutical_account_view');
    Route::get('/pharmaceutical_account_list', [pharmaceuticalCustomerController::class, 'pharmaceuticalAccountList'])->name('pharmaceutical_account.list');
    Route::get('/pharmaceutical_account_delete/{id}', [pharmaceuticalCustomerController::class, 'pharmaceutical_account_delete'])->name('pharmaceutical_account_delete');

    Route::post('/pharmaceutical_store', [pharmaceuticalCustomerController::class, 'pharmaceuticalStore'])->name('pharmaceutical_account.store');

    // Payment Invoice 
    Route::get('/invoice_payable', [InvoicePayableController::class, 'invoicePayable'])->name('invoice_payable');
    Route::get('/payment_exception', [InvoicePayableController::class, 'paymentException'])->name('payment_exceptions');
    Route::get('/payment_confirmation', [InvoicePayableController::class, 'paymentConfirmation'])->name('payment_confirmation');
    Route::get('/payment_overdue', [InvoicePayableController::class, 'paymentOverdue'])->name('payment_overdue');
    Route::get('/invoice_paid', [InvoicePayableController::class, 'invoicePaid'])->name('invoice_paid');
    Route::get('/remittance', [InvoicePayableController::class, 'remittance_form'])->name('remittance_form');
    Route::post('/search-invoice', [InvoicePayableController::class, 'searchInvoice'])->name('search-invoice');
    Route::post('/search-invoice-excep', [InvoicePayableController::class, 'searchInvoiceExcep'])->name('search-invoice-excep');
    Route::post('/search-customer', [InvoicePayableController::class, 'searchCustomer'])->name('search-customer');
    Route::post('/remittance_store', [InvoicePayableController::class, 'remittanceStore'])->name('remittance.store');
    Route::get('delivery_note/{custId}', [InvoiceController::class, 'delivery_download'])->name('delivery.note');
    Route::get('invoice/{custId}', [InvoiceController::class, 'invoice_download'])->name('invoice.download');


    Route::post('/payment/confirm', [InvoicePayableController::class, 'confirm'])->name('payment.confirm');
    Route::post('/payment/update', [InvoicePayableController::class, 'update'])->name('payment.update');
    Route::post('/payment/reUpdate', [InvoicePayableController::class, 'reUpdate'])->name('payment.reUpdate');
    Route::post('/send/email/invoice', [InvoicePayableController::class, 'send_invoice'])->name('send.invoices');
    Route::post('/send/invoice', [OrderController::class, 'send_invoice'])->name('send.invoice');
    Route::post('/send/statement', [InvoicePayableController::class, 'send_statement'])->name('send.statement');
    Route::get('/stock/pdf/{date?}', [ReportController::class, 'pdfStockReport'])->name('stock_report.pdf.download');

    Route::get('/cancelled/pdf/{date?}', [ReportController::class, 'pdfCancelledReport'])->name('Cancelled_report.pdf.download');

    Route::get('/stock/excel/{date?}', [ReportController::class, 'downloadExcelReport'])->name('stock_report.excel.download');
    Route::get('/cancelled/excel/{date?}', [ReportController::class, 'downloadCancelledReport'])->name('cancelled_report.excel.download');


    Route::post('/download/excel-sheet', [ReportController::class, 'download_excel'])->name('download-excel');
    Route::post('/send/customers/statement', [InvoicePayableController::class, 'send_customers_statement'])->name('send.customers.statement');
    Route::get('/payment_ref/{payment_ref}', [InvoicePayableController::class, 'payment_ref'])->name('payment_ref');
    Route::get('/excep_amount_view/{id}', [InvoicePayableController::class, 'excep_amount_view'])->name('excep_amount_view');
    Route::get('/orderReturn/{id}', [InvoicePayableController::class, 'orderReturn'])->name('orderReturn');
    // Route::get('/orderReturnConfirm/{id}', [InvoicePayableController::class, 'orderReturnConfirm'])->name('orderReturn_confirm');
    Route::post('/filter-orders', [InvoicePayableController::class, 'filterOrders'])->name('filter_date');

    // Brand
    Route::resource('brands', BrandController::class);
    Route::controller(BrandController::class)->group(function () {
        Route::get('/brands/edit/{id}', 'edit')->name('brands.edit');
        Route::get('/brands/destroy/{id}', 'destroy')->name('brands.destroy');
        Route::post('/brands/featured', 'updateFeatured')->name('brands.featured');
        Route::post('/brands/order/order_level', 'updateOrder')->name('brands.order.order_level');
    });
    Route::controller(AdminController::class)->group(function () {
        Route::post('/dashboard/top-category-products-section', 'top_category_products_section')->name('dashboard.top_category_products_section');
        Route::post('/dashboard/inhouse-top-brands', 'inhouse_top_brands')->name('dashboard.inhouse_top_brands');
        Route::post('/dashboard/inhouse-top-categories', 'inhouse_top_categories')->name('dashboard.inhouse_top_categories');
        Route::post('/dashboard/top-sellers-products-section', 'top_sellers_products_section')->name('dashboard.top_sellers_products_section');
        Route::post('/dashboard/top-brands-products-section', 'top_brands_products_section')->name('dashboard.top_brands_products_section');
    });
    Route::controller(AdminController::class)->group(function () {
        Route::post('/dashboard/top-category-products-section', 'top_category_products_section')->name('dashboard.top_category_products_section');
        Route::post('/dashboard/inhouse-top-brands', 'inhouse_top_brands')->name('dashboard.inhouse_top_brands');
        Route::post('/dashboard/inhouse-top-categories', 'inhouse_top_categories')->name('dashboard.inhouse_top_categories');
        Route::post('/dashboard/top-sellers-products-section', 'top_sellers_products_section')->name('dashboard.top_sellers_products_section');
        Route::post('/dashboard/top-brands-products-section', 'top_brands_products_section')->name('dashboard.top_brands_products_section');
    });

    // Products
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products/admin', 'admin_products')->name('products.admin');
        Route::get('/products/seller/{product_type}', 'seller_products')->name('products.seller');
        Route::get('/products/all', 'all_products')->name('products.all');
        Route::get('/products/create', 'create')->name('products.create');
        Route::post('/products/store/', 'store')->name('products.store');
        Route::get('/products/admin/{id}/edit', 'admin_product_edit')->name('products.admin.edit');
        Route::get('/products/seller/{id}/edit', 'seller_product_edit')->name('products.seller.edit');
        Route::post('/products/update/{product}', 'update')->name('products.update');
        Route::post('/products/todays_deal', 'updateTodaysDeal')->name('products.todays_deal');
        Route::post('/products/featured', 'updateFeatured')->name('products.featured');
        Route::post('/products/update_trending', 'updateTrending')->name('products.update_trending');
        Route::post('/products/update_monthly_deals', 'update_monthly_deals')->name('products.update_monthly_deals');
        Route::post('/products/update_save_big', 'updateSaveBig')->name('products.update_save_big');
        Route::post('/products/pharma', 'updatePharma')->name('products.pharma');
        Route::post('/products/published', 'updatePublished')->name('products.published');
        Route::post('/products/approved', 'updateProductApproval')->name('products.approved');
        Route::post('/products/get_products_by_subcategory', 'get_products_by_subcategory')->name('products.get_products_by_subcategory');
        Route::get('/products/duplicate/{id}', 'duplicate')->name('products.duplicate');
        Route::get('/products/destroy/{id}', 'destroy')->name('products.destroy');
        Route::post('/bulk-product-delete', 'bulk_product_delete')->name('bulk-product-delete');
        Route::post('/product-seller-status', 'product_seller_status')->name('product_seller.status');

        Route::post('/products/sku_combination', 'sku_combination')->name('products.sku_combination');
        Route::post('/products/sku_combination_edit', 'sku_combination_edit')->name('products.sku_combination_edit');
        Route::post('/products/add-more-choice-option', 'add_more_choice_option')->name('products.add-more-choice-option');
    });

    // Digital Product
    Route::resource('digitalproducts', DigitalProductController::class);
    Route::controller(DigitalProductController::class)->group(function () {
        Route::get('/digitalproducts/edit/{id}', 'edit')->name('digitalproducts.edit');
        Route::get('/digitalproducts/destroy/{id}', 'destroy')->name('digitalproducts.destroy');
        Route::get('/digitalproducts/download/{id}', 'download')->name('digitalproducts.download');
    });

    Route::controller(ProductBulkUploadController::class)->group(function () {
        //Product Export
        Route::get('/product-bulk-export', 'export')->name('product_bulk_export.index');

        //Product Bulk Upload
        Route::get('/product-bulk-upload/index', 'index')->name('product_bulk_upload.index');
        Route::post('/bulk-product-upload', 'bulk_upload')->name('bulk_product_upload');
        // Haseeb

        Route::get('/customer-product-bulk-upload/customer_products', 'customer_products')->name('customer_product_bulk_upload.index');
        Route::post('/bulk-product-customer-upload', 'bulk_upload_customer_price')->name('customer_product_bulk_upload');
        // Haseeb
        Route::get('/product-csv-download/{type}', 'import_product')->name('product_csv.download');
        Route::get('/vendor-product-csv-download/{id}', 'import_vendor_product')->name('import_vendor_product.download');
        Route::group(['prefix' => 'bulk-upload/download'], function () {
            Route::get('/category', 'pdf_download_category')->name('pdf.download_category');
            Route::get('/brand', 'pdf_download_brand')->name('pdf.download_brand');
            Route::get('/seller', 'pdf_download_seller')->name('pdf.download_seller');
        });
    });

    // Seller
    Route::resource('sellers', SellerController::class);
    Route::controller(SellerController::class)->group(function () {
        Route::get('sellers_ban/{id}', 'ban')->name('sellers.ban');
        Route::get('/sellers/destroy/{id}', 'destroy')->name('sellers.destroy');
        Route::post('/bulk-seller-delete', 'bulk_seller_delete')->name('bulk-seller-delete');
        Route::get('/sellers/view/{id}/verification', 'show_verification_request')->name('sellers.show_verification_request');
        Route::get('/sellers/approve/{id}', 'approve_seller')->name('sellers.approve');
        Route::get('/sellers/reject/{id}', 'reject_seller')->name('sellers.reject');
        Route::get('/sellers/login/{id}', 'login')->name('sellers.login');
        Route::post('/sellers/payment_modal', 'payment_modal')->name('sellers.payment_modal');
        Route::post('/sellers/profile_modal', 'profile_modal')->name('sellers.profile_modal');
        Route::post('/sellers/approved', 'updateApproved')->name('sellers.approved');
    });

    // Seller Payment
    Route::controller(PaymentController::class)->group(function () {
        Route::get('/seller/payments', 'payment_histories')->name('sellers.payment_histories');
        Route::get('/seller/payments/show/{id}', 'show')->name('sellers.payment_history');
    });

    // Seller Withdraw Request
    Route::resource('/withdraw_requests', SellerWithdrawRequestController::class);
    Route::controller(SellerWithdrawRequestController::class)->group(function () {
        Route::get('/withdraw_requests_all', 'index')->name('withdraw_requests_all');
        Route::post('/withdraw_request/payment_modal', 'payment_modal')->name('withdraw_request.payment_modal');
        Route::post('/withdraw_request/message_modal', 'message_modal')->name('withdraw_request.message_modal');
    });

    // Customer
    Route::resource('customers', CustomerController::class);
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customers_ban/{customer}', 'ban')->name('customers.ban');
        Route::get('/customers/login/{id}', 'login')->name('customers.login');
        Route::get('/customers/destroy/{id}', 'destroy')->name('customers.destroy');
        Route::post('/bulk-customer-delete', 'bulk_customer_delete')->name('bulk-customer-delete');
    });

    // Newsletter
    Route::controller(NewsletterController::class)->group(function () {
        Route::get('/newsletter', 'index')->name('newsletters.index');
        Route::post('/newsletter/send', 'send')->name('newsletters.send');
        Route::post('/newsletter/test/smtp', 'testEmail')->name('test.smtp');
    });

    Route::resource('profile', ProfileController::class);

    // Business Settings
    Route::controller(BusinessSettingsController::class)->group(function () {
        Route::post('/business-settings/update', 'update')->name('business_settings.update');
        Route::post('/business-settings/update/activation', 'updateActivationSettings')->name('business_settings.update.activation');
        Route::get('/general-setting', 'general_setting')->name('general_setting.index');
        Route::get('/activation', 'activation')->name('activation.index');
        Route::get('/payment-method', 'payment_method')->name('payment_method.index');
        Route::get('/file_system', 'file_system')->name('file_system.index');
        Route::get('/social-login', 'social_login')->name('social_login.index');
        Route::get('/smtp-settings', 'smtp_settings')->name('smtp_settings.index');
        Route::get('/google-analytics', 'google_analytics')->name('google_analytics.index');
        Route::get('/google-recaptcha', 'google_recaptcha')->name('google_recaptcha.index');
        Route::get('/google-map', 'google_map')->name('google-map.index');
        Route::get('/google-firebase', 'google_firebase')->name('google-firebase.index');

        //Facebook Settings
        Route::get('/facebook-chat', 'facebook_chat')->name('facebook_chat.index');
        Route::post('/facebook_chat', 'facebook_chat_update')->name('facebook_chat.update');
        Route::get('/facebook-comment', 'facebook_comment')->name('facebook-comment');
        Route::post('/facebook-comment', 'facebook_comment_update')->name('facebook-comment.update');
        Route::post('/facebook_pixel', 'facebook_pixel_update')->name('facebook_pixel.update');

        Route::post('/env_key_update', 'env_key_update')->name('env_key_update.update');
        Route::post('/payment_method_update', 'payment_method_update')->name('payment_method.update');
        Route::post('/google_analytics', 'google_analytics_update')->name('google_analytics.update');
        Route::post('/google_recaptcha', 'google_recaptcha_update')->name('google_recaptcha.update');
        Route::post('/google-map', 'google_map_update')->name('google-map.update');
        Route::post('/google-firebase', 'google_firebase_update')->name('google-firebase.update');

        Route::get('/verification/form', 'seller_verification_form')->name('seller_verification_form.index');
        Route::post('/verification/form', 'seller_verification_form_update')->name('seller_verification_form.update');
        Route::get('/vendor_commission', 'vendor_commission')->name('business_settings.vendor_commission');
        Route::post('/vendor_commission_update', 'vendor_commission_update')->name('business_settings.vendor_commission.update');

        //Shipping Configuration
        Route::get('/shipping_configuration', 'shipping_configuration')->name('shipping_configuration.index');
        Route::post('/shipping_configuration/update', 'shipping_configuration_update')->name('shipping_configuration.update');

        // Order Configuration
        Route::get('/order-configuration', 'order_configuration')->name('order_configuration.index');
    });


    //Currency
    Route::controller(CurrencyController::class)->group(function () {
        Route::get('/currency', 'currency')->name('currency.index');
        Route::post('/currency/update', 'updateCurrency')->name('currency.update');
        Route::post('/your-currency/update', 'updateYourCurrency')->name('your_currency.update');
        Route::get('/currency/create', 'create')->name('currency.create');
        Route::post('/currency/store', 'store')->name('currency.store');
        Route::post('/currency/currency_edit', 'edit')->name('currency.edit');
        Route::post('/currency/update_status', 'update_status')->name('currency.update_status');
    });

    //Tax
    Route::resource('tax', TaxController::class);
    Route::controller(TaxController::class)->group(function () {
        Route::get('/tax/edit/{id}', 'edit')->name('tax.edit');
        Route::get('/tax/destroy/{id}', 'destroy')->name('tax.destroy');
        Route::post('tax-status', 'change_tax_status')->name('taxes.tax-status');
    });

    // Language
    Route::resource('/languages', LanguageController::class);
    Route::controller(LanguageController::class)->group(function () {
        Route::post('/languages/{id}/update', 'update')->name('languages.update');
        Route::get('/languages/destroy/{id}', 'destroy')->name('languages.destroy');
        Route::post('/languages/update_rtl_status', 'update_rtl_status')->name('languages.update_rtl_status');
        Route::post('/languages/update-status', 'update_status')->name('languages.update-status');
        Route::post('/languages/key_value_store', 'key_value_store')->name('languages.key_value_store');

        //App Trasnlation
        Route::post('/languages/app-translations/import', 'importEnglishFile')->name('app-translations.import');
        Route::get('/languages/app-translations/show/{id}', 'showAppTranlsationView')->name('app-translations.show');
        Route::post('/languages/app-translations/key_value_store', 'storeAppTranlsation')->name('app-translations.store');
        Route::get('/languages/app-translations/export/{id}', 'exportARBFile')->name('app-translations.export');
    });


    // website setting
    Route::group(['prefix' => 'website'], function () {
        Route::controller(WebsiteController::class)->group(function () {
            Route::get('/footer', 'footer')->name('website.footer');
            Route::get('/header', 'header')->name('website.header');
            Route::get('/appearance', 'appearance')->name('website.appearance');
            Route::get('/select-homepage', 'select_homepage')->name('website.select-homepage');
            Route::get('/authentication-layout-settings', 'authentication_layout_settings')->name('website.authentication-layout-settings');
            Route::get('/pages', 'pages')->name('website.pages');
        });

        // Custom Page
        Route::resource('custom-pages', PageController::class);
        Route::controller(PageController::class)->group(function () {
            Route::get('/custom-pages/edit/{id}', 'edit')->name('custom-pages.edit');
            Route::get('/custom-pages/destroy/{id}', 'destroy')->name('custom-pages.destroy');
        });
    });

    // Staff Roles
    Route::resource('roles', RoleController::class);
    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles/edit/{id}', 'edit')->name('roles.edit');
        Route::get('/roles/destroy/{id}', 'destroy')->name('roles.destroy');

        // Add Permissiom
        Route::post('/roles/add_permission', 'add_permission')->name('roles.permission');
    });

    // Staff
    Route::resource('staffs', StaffController::class);
    Route::get('/staffs/destroy/{id}', [StaffController::class, 'destroy'])->name('staffs.destroy');
    // Route::get('/search-delivery', [PosController::class, 'searchDelivery']);    // Flash Deal
    Route::get('/search/delivery-address', [PosController::class, 'searchDeliveryAddress'])->name('search.delivery.address');

    Route::resource('flash_deals', FlashDealController::class);
    Route::controller(FlashDealController::class)->group(function () {
        Route::get('/flash_deals/edit/{id}', 'edit')->name('flash_deals.edit');
        Route::get('/flash_deals/destroy/{id}', 'destroy')->name('flash_deals.destroy');
        Route::post('/flash_deals/update_status', 'update_status')->name('flash_deals.update_status');
        Route::post('/flash_deals/update_featured', 'update_featured')->name('flash_deals.update_featured');
        Route::post('/flash_deals/product_discount', 'product_discount')->name('flash_deals.product_discount');
        Route::post('/flash_deals/product_discount_edit', 'product_discount_edit')->name('flash_deals.product_discount_edit');
    });

    //Subscribers
    Route::controller(SubscriberController::class)->group(function () {
        Route::get('/subscribers', 'index')->name('subscribers.index');
        Route::get('/subscribers/destroy/{id}', 'destroy')->name('subscriber.destroy');
    });

    // Order
    Route::resource('orders', OrderController::class);
    Route::controller(OrderController::class)->group(function () {
        // All Orders
        Route::get('/all_orders', 'all_orders')->name('all_orders.index');
        Route::get('/inhouse-orders', 'dispatch_orders')->name('inhouse_orders.index');
        Route::post('/save-productss', 'saveProducts')->name('save.products');

        Route::get('/pending_orders', 'pending_orders')->name('pending_orders.index');
        Route::get('/cancelled_orders', 'cancelled_orders')->name('cancelled_orders.index');
        Route::get('/fullfillment-orders', 'fullfillment_orders')->name('fulfillment_orders.index');
        Route::get('/shipment-orders', 'shipment_order')->name('shipment_order.index');
        Route::get('/seller_orders', 'all_orders')->name('seller_orders.index');
        Route::get('orders_by_pickup_point', 'all_orders')->name('pick_up_point.index');

        // intenational order
        Route::get('/international-orders', 'international_orders')->name('international_orders.index');
        Route::get('/international-orders/{id}/show', 'international_orders_show')->name('international_orders.show');
        Route::post('/orders/update-charge-popup', 'updateChargePopup')->name('orders.update_charge_popup');

        Route::get('/orders/{id}/show', 'show')->name('all_orders.show');
        Route::get('/inhouse-orders/{id}/show', 'dispatch_orders_show')->name('inhouse_orders.show');
        Route::get('/pending_orders/{id}/show', 'pending_orders_show')->name('pending_orders.show');
        Route::get('/cancelled_orders/{id}/show', 'cancelled_orders_show')->name('cancelled_orders.show');
        Route::get('/fullfillment-orders/{id}/show', 'fullfillment_orders_show')->name('fullfillment_orders.show');
        Route::get('/orders-view/{id}/show', 'fullfillment_orders_show');

        Route::get('/shipment-orders/{id}/show', 'shipment_orders_show')->name('shipment_orders.show');
        // Route::get('/inhouse-orders/{id}/show', 'show')->name('inhouse_orders.show');
        Route::get('/seller_orders/{id}/show', 'show')->name('seller_orders.show');
        Route::get('/orders_by_pickup_point/{id}/show', 'show')->name('pick_up_point.order_show');

        Route::post('/bulk-order-status', 'bulk_order_status')->name('bulk-order-status');
        Route::post('/updatepriceqty', 'updateOrderDetail')->name('updateQty');

        Route::get('/orders/destroy/{id}', 'destroy')->name('orders.destroy');
        Route::get('/orders/product/destroy/{id}/{prod_id}', 'product_destory')->name('orders.product.destroy');
        Route::post('/bulk-order-delete', 'bulk_order_delete')->name('bulk-order-delete');

        Route::get('/orders/destroy/{id}', 'destroy')->name('orders.destroy');
        Route::post('/orders/details', 'order_details')->name('orders.details');
        Route::post('/orders/update_delivery_status', 'update_delivery_status')->name('orders.update_delivery_status');
        Route::post('/orders/update_payment_status', 'update_payment_status')->name('orders.update_payment_status');
        Route::post('/orders/update_tracking_code', 'update_tracking_code')->name('orders.update_tracking_code');
        Route::post('/orders/purchase_order_number', 'purchase_order_number')->name('orders.purchase_order_number');
        Route::post('/orders/notes', 'notes_update')->name('orders.notes');
        Route::post('/orders/status', 'order_status')->name('orders.status');

        Route::post('/orders/update-status-and-send-invoice', [OrderController::class, 'updateStatusAndSendInvoice'])->name('orders.updateStatusAndSendInvoice');


        //Delivery Boy Assign
        Route::post('/orders/delivery-boy-assign', 'assign_delivery_boy')->name('orders.delivery-boy-assign');
    });

    Route::post('/send-invoice2', [OrderController::class, 'sendInvoice2'])->name('send.invoice2');
    Route::post('/pay_to_seller', [CommissionController::class, 'pay_to_seller'])->name('commissions.pay_to_seller');

    //Reports
    Route::controller(ReportController::class)->group(function () {
        Route::get('/in_house_sale_report', 'in_house_sale_report')->name('in_house_sale_report.index');
        Route::get('/seller_sale_report', 'seller_sale_report')->name('seller_sale_report.index');
        Route::get('/stock_report', 'stock_report')->name('stock_report.index');
        Route::get('/cancelled_report', 'cancelled_report')->name('cancelled_report.index');
        Route::get('/wish_report', 'wish_report')->name('wish_report.index');
        Route::get('/user_search_report', 'user_search_report')->name('user_search_report.index');
        Route::get('/commission-log', 'commission_history')->name('commission-log.index');
        // Route::get('/self_report', 'self_report')->name('self_report.index');
        Route::get('/wallet-history', 'wallet_transaction_history')->name('wallet-history.index');
    });
    Route::get('/sale_report', [ReportController::class, 'sale_report'])->name('sale_report.index');
    Route::get('/sale/pdf/{date?}/{customer_id?}', [ReportController::class, 'pdfSaleReport'])->name('sale_report.pdf.download');
    Route::get('/sale/excel/{date?}/{customer_id?}', [ReportController::class, 'downloadExcelReportSale'])->name('sale_report.pdf.download');


    //Coupons
    Route::resource('coupon', CouponController::class);
    Route::controller(CouponController::class)->group(function () {
        Route::post('/coupon/update-status', 'updateStatus')->name('coupon.update_status');
        Route::get('/coupon/destroy/{id}', 'destroy')->name('coupon.destroy');

        //Coupon Form
        Route::post('/coupon/get_form', 'get_coupon_form')->name('coupon.get_coupon_form');
        Route::post('/coupon/get_form_edit', 'get_coupon_form_edit')->name('coupon.get_coupon_form_edit');
    });

    //Reviews
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/reviews', 'index')->name('reviews.index');
        Route::post('/reviews/published', 'updatePublished')->name('reviews.published');
    });

    //Support_Ticket
    Route::controller(SupportTicketController::class)->group(function () {
        Route::get('support_ticket/', 'admin_index')->name('support_ticket.admin_index');
        Route::get('support_ticket/{id}/show', 'admin_show')->name('support_ticket.admin_show');
        Route::post('support_ticket/reply', 'admin_store')->name('support_ticket.admin_store');
    });

    //Pickup_Points
    Route::resource('pick_up_points', PickupPointController::class);
    Route::controller(PickupPointController::class)->group(function () {
        Route::get('/pick_up_points/edit/{id}', 'edit')->name('pick_up_points.edit');
        Route::get('/pick_up_points/destroy/{id}', 'destroy')->name('pick_up_points.destroy');
    });

    //conversation of seller customer
    Route::controller(ConversationController::class)->group(function () {
        Route::get('conversations', 'admin_index')->name('conversations.admin_index');
        Route::get('conversations/{id}/show', 'admin_show')->name('conversations.admin_show');
    });

    // product Queries show on Admin panel
    Route::controller(ProductQueryController::class)->group(function () {
        Route::get('/product-queries', 'index')->name('product_query.index');
        Route::get('/product-queries/{id}', 'show')->name('product_query.show');
        Route::put('/product-queries/{id}', 'reply')->name('product_query.reply');
    });

    // Product Attribute
    Route::resource('attributes', AttributeController::class);
    Route::controller(AttributeController::class)->group(function () {
        Route::get('/attributes/edit/{id}', 'edit')->name('attributes.edit');
        Route::get('/attributes/destroy/{id}', 'destroy')->name('attributes.destroy');

        //Attribute Value
        Route::post('/store-attribute-value', 'store_attribute_value')->name('store-attribute-value');
        Route::get('/edit-attribute-value/{id}', 'edit_attribute_value')->name('edit-attribute-value');
        Route::post('/update-attribute-value/{id}', 'update_attribute_value')->name('update-attribute-value');
        Route::get('/destroy-attribute-value/{id}', 'destroy_attribute_value')->name('destroy-attribute-value');

        //Colors
        Route::get('/colors', 'colors')->name('colors');
        Route::post('/colors/store', 'store_color')->name('colors.store');
        Route::get('/colors/edit/{id}', 'edit_color')->name('colors.edit');
        Route::post('/colors/update/{id}', 'update_color')->name('colors.update');
        Route::get('/colors/destroy/{id}', 'destroy_color')->name('colors.destroy');
    });

    // Size Chart
    Route::resource('size-charts', SizeChartController::class);
    Route::get('/size-charts/destroy/{id}',  [SizeChartController::class, 'destroy'])->name('size-charts.destroy');
    Route::post('size-charts/get-combination',   [SizeChartController::class, 'get_combination'])->name('size-charts.get-combination');

    // Measurement Points
    Route::resource('measurement-points', MeasurementPointsController::class);
    Route::get('/measurement-points/destroy/{id}',  [MeasurementPointsController::class, 'destroy'])->name('measurement-points.destroy');

    // Addon
    Route::resource('addons', AddonController::class);
    Route::post('/addons/activation', [AddonController::class, 'activation'])->name('addons.activation');

    //Customer Package
    Route::resource('customer_packages', CustomerPackageController::class);
    Route::controller(CustomerPackageController::class)->group(function () {
        Route::get('/customer_packages/edit/{id}', 'edit')->name('customer_packages.edit');
        Route::get('/customer_packages/destroy/{id}', 'destroy')->name('customer_packages.destroy');
    });

    //Classified Products
    Route::controller(CustomerProductController::class)->group(function () {
        Route::get('/classified_products', 'customer_product_index')->name('classified_products');
        Route::post('/classified_products/published', 'updatePublished')->name('classified_products.published');
        Route::get('/classified_products/destroy/{id}', 'destroy_by_admin')->name('classified_products.destroy');
    });

    // Countries
    Route::resource('countries', CountryController::class);
    Route::post('/countries/status', [CountryController::class, 'updateStatus'])->name('countries.status');

    // States
    Route::resource('states', StateController::class);
    Route::post('/states/status', [StateController::class, 'updateStatus'])->name('states.status');

    // Carriers
    Route::resource('carriers', CarrierController::class);
    Route::controller(CarrierController::class)->group(function () {
        Route::get('/carriers/destroy/{id}', 'destroy')->name('carriers.destroy');
        Route::post('/carriers/update_status', 'updateStatus')->name('carriers.update_status');
    });


    // Zones
    Route::resource('zones', ZoneController::class);
    Route::get('/zones/destroy/{id}', [ZoneController::class, 'destroy'])->name('zones.destroy');

    Route::resource('cities', CityController::class);
    Route::controller(CityController::class)->group(function () {
        Route::get('/cities/edit/{id}', 'edit')->name('cities.edit');
        Route::get('/cities/destroy/{id}', 'destroy')->name('cities.destroy');
        Route::post('/cities/status', 'updateStatus')->name('cities.status');
    });

    Route::view('/system/update', 'backend.system.update')->name('system_update');
    Route::view('/system/server-status', 'backend.system.server_status')->name('system_server');
    Route::view('/system/import-demo-data', 'backend.system.import_demo_data')->name('import_demo_data');

    Route::post('/import-data', [BusinessSettingsController::class, 'import_data'])->name('import_data');

    // uploaded files
    Route::resource('/uploaded-files', AizUploadController::class);
    Route::controller(AizUploadController::class)->group(function () {
        Route::any('/uploaded-files/file-info', 'file_info')->name('uploaded-files.info');
        Route::get('/uploaded-files/destroy/{id}', 'destroy')->name('uploaded-files.destroy');
        Route::post('/bulk-uploaded-files-delete', 'bulk_uploaded_files_delete')->name('bulk-uploaded-files-delete');
        Route::get('/all-file', 'all_file');
    });

    Route::get('/all-notification', [NotificationController::class, 'index'])->name('admin.all-notification');

    Route::get('/clear-cache', [AdminController::class, 'clearCache'])->name('cache.clear');

    Route::get('/admin-permissions', [RoleController::class, 'create_admin_permissions']);
    Route::get('/email-template', function() {
        $data['order'] = Order::first();
        return view('emails.order_process_mail', $data);
        // return view('emails.order_dispatched', $data);
        // return view('emails.order_confirmation',$data);
    });
});
