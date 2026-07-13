<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Middleware\EnsureSystemKey;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;

Route::post('/email/verify', [VerificationController::class, 'verify'])->name('verification.verify');


Route::group(['prefix' => 'v2/auth', 'middleware' => ['app_language']], function () {
    Route::post('login', 'App\Http\Controllers\Api\V2\AuthController@login');
    Route::get('check_approved_pharma', 'App\Http\Controllers\Api\V2\AuthController@check_approved_pharma');
    Route::post('signup', 'App\Http\Controllers\Api\V2\AuthController@signup');
    Route::post('social-login', 'App\Http\Controllers\Api\V2\AuthController@socialLogin');
    Route::post('password/forget_request', 'App\Http\Controllers\Api\V2\PasswordResetController@forgetRequest');
    Route::post('password/verifyCode', 'App\Http\Controllers\Api\V2\PasswordResetController@verifyCode');
    Route::post('password/confirm_reset', 'App\Http\Controllers\Api\V2\PasswordResetController@confirmReset');
    Route::post('password/resend_code', 'App\Http\Controllers\Api\V2\PasswordResetController@resendCode');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', 'App\Http\Controllers\Api\V2\AuthController@logout');
        Route::get('account-deletion', 'App\Http\Controllers\Api\V2\AuthController@account_deletion');
        Route::get('user', 'App\Http\Controllers\Api\V2\AuthController@user');
        Route::get('resend_code', 'App\Http\Controllers\Api\V2\AuthController@resendCode');
        Route::post('confirm_code', 'App\Http\Controllers\Api\V2\AuthController@confirmCode');
        Route::post('update_password', 'App\Http\Controllers\Api\V2\AuthController@updatePassword');
    });

    Route::post('info', 'App\Http\Controllers\Api\V2\AuthController@getUserInfoByAccessToken');
});




Route::group(['prefix' => 'v2', 'middleware' => ['app_language', 'prevent-back-history']], function () {




    /* --- start customer registration --- */


    /* required api for customer registration
        register customer
        credit customer,
        pharmaceutical customer,
        guest customer
    */

    // for all customer creation base of customer_type payload
    Route::post('customer-create', [CustomerController::class, 'customerCreate']);
    Route::post('customer-update', 'App\Http\Controllers\Api\V2\CustomerController@allCustomerUpdate')->middleware('auth:sanctum');
    Route::post('customer-info', 'App\Http\Controllers\Api\V2\CustomerController@show')->middleware('auth:sanctum');

    /* --- end customer registration --- */




    /* --- start landing page  --- */

    Route::get('banners-all', 'App\Http\Controllers\Api\V2\BannarController@getAllBanners');
    Route::get('/partners/list', [\App\Http\Controllers\Api\V2\HomePageController::class, 'partnersList']);
    Route::get('/blog/categories', [\App\Http\Controllers\Api\V2\HomePageController::class, 'blogCategories']);
    Route::get('/blogs', [\App\Http\Controllers\Api\V2\BlogController::class, 'index']);
    Route::get('/blogs/{slug}', [\App\Http\Controllers\Api\V2\BlogController::class, 'show']);
    Route::get('/client-reviews', [\App\Http\Controllers\Api\V2\HomePageController::class, 'clientReviews']);

    // blogs banners, partners, cilent reviews everything is handle in this pai
    Route::get('/home-page-content', [\App\Http\Controllers\Api\V2\HomePageController::class, 'homePage'])->middleware('optional.auth');


    Route::get('/all-categories/{path?}', [ProductController::class, 'all_categories_v2'])->where('path', '.*')->middleware('optional.auth');
    Route::get('categories-list', 'App\Http\Controllers\Api\V2\ProductController@categories_data')->middleware('optional.auth');
    Route::post('products/search', 'App\Http\Controllers\Api\V2\ProductController@search')->middleware('optional.auth');
    Route::get('single_products/{id}', 'App\Http\Controllers\Api\V2\ProductController@single')->middleware('optional.auth');
    Route::apiResource('brands', 'App\Http\Controllers\Api\V2\BrandController')->only(['index', 'show']);




    // all carts routes
    Route::post('cart-summary', 'App\Http\Controllers\Api\V2\CartController@summary');
    Route::post('cart-count', 'App\Http\Controllers\Api\V2\CartController@count');
    Route::post('user_id_update', 'App\Http\Controllers\Api\V2\CartController@user_id_update');
    Route::post('carts/process', 'App\Http\Controllers\Api\V2\CartController@process');
    Route::post('carts/add', 'App\Http\Controllers\Api\V2\CartController@add');
    Route::post('carts/delete', 'App\Http\Controllers\Api\V2\CartController@delete');
    Route::post('carts/change-quantity', 'App\Http\Controllers\Api\V2\CartController@changeQuantity');
    Route::apiResource('carts', 'App\Http\Controllers\Api\V2\CartController');
    Route::post('carts', 'App\Http\Controllers\Api\V2\CartController@getList');
    Route::post('payment_order', 'App\Http\Controllers\Api\V2\CartController@payment_order');
    // end carts routes


    // wishlist routes
    Route::get('wishlists-check-product', 'App\Http\Controllers\Api\V2\WishlistController@isProductInWishlist')->middleware('auth:sanctum');
    Route::post('wishlists-add-product', 'App\Http\Controllers\Api\V2\WishlistController@add')->middleware('auth:sanctum');
    Route::post('wishlists-remove-product', 'App\Http\Controllers\Api\V2\WishlistController@remove')->middleware('auth:sanctum');
    Route::get('wishlists', 'App\Http\Controllers\Api\V2\WishlistController@index')->middleware('auth:sanctum');
    Route::apiResource('wishlists', 'App\Http\Controllers\Api\V2\WishlistController')->except(['index', 'update', 'show']);
    // end wishlist routes



    // orders

    Route::post('order/store', 'App\Http\Controllers\Api\V2\OrderController@store')->middleware('optional.auth');
    Route::get('orders/{type}', 'App\Http\Controllers\Api\V2\OrderController@getOrders')->middleware('auth:sanctum');
    Route::get('/order/view/{id}', 'App\Http\Controllers\Api\V2\OrderController@viewOrder')->middleware('auth:sanctum');
    Route::get('/order/invoice/{id}', 'App\Http\Controllers\Api\V2\OrderController@invoiceDownload')->middleware('auth:sanctum')->name('api.invoice.download');

    // end order


    // payments

    Route::post('payment/stripe/create-payment-intent', 'App\Http\Controllers\Api\V2\StripeController@create_payment_intent');
    Route::post('payment/stripe/webhook', 'App\Http\Controllers\Api\V2\StripeController@webhook');
    Route::post('payment/stripe/success', 'App\Http\Controllers\Api\V2\StripeController@payment_success');
    Route::post('payment/stripe/cancel', 'App\Http\Controllers\Api\V2\StripeController@cancel');

    // end payments


    // auction products routes
    Route::get('auction/products', [AuctionProductController::class, 'index']);
    Route::get('auction/bided-products', [AuctionProductController::class, 'bided_products_list'])->middleware('auth:sanctum');
    Route::get('auction/purchase-history', [AuctionProductController::class, 'user_purchase_history'])->middleware('auth:sanctum');
    Route::get('auction/products/{id}', [AuctionProductController::class, 'details_auction_product']);
    Route::post('auction/place-bid', [AuctionProductBidController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/variations/packqty', 'App\Http\Controllers\Api\V2\ProductController@filter_variations');
    Route::post('/filter-by-packqty', 'App\Http\Controllers\Api\V2\ProductController@filter_by_packqty');

    // varient price
    // Route::get('varient-price', [ProductController::class, 'getPrice']);

    Route::get('/sitemap', 'App\Http\Controllers\Api\V2\SitemapController@index');

    Route::prefix('delivery-boy')->group(function () {
        Route::get('dashboard-summary/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@dashboard_summary')->middleware('auth:sanctum');
        Route::get('deliveries/completed/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@completed_delivery')->middleware('auth:sanctum');
        Route::get('deliveries/cancelled/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@cancelled_delivery')->middleware('auth:sanctum');
        Route::get('deliveries/on_the_way/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@on_the_way_delivery')->middleware('auth:sanctum');
        Route::get('deliveries/picked_up/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@picked_up_delivery')->middleware('auth:sanctum');
        Route::get('deliveries/assigned/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@assigned_delivery')->middleware('auth:sanctum');
        Route::get('collection-summary/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@collection_summary')->middleware('auth:sanctum');
        Route::get('earning-summary/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@earning_summary')->middleware('auth:sanctum');
        Route::get('collection/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@collection')->middleware('auth:sanctum');
        Route::get('earning/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@earning')->middleware('auth:sanctum');
        Route::get('cancel-request/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@cancel_request')->middleware('auth:sanctum');
        Route::post('change-delivery-status', 'App\Http\Controllers\Api\V2\DeliveryBoyController@change_delivery_status')->middleware('auth:sanctum');
        //Delivery Boy Order
        Route::get('purchase-history-details/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@details')->middleware('auth:sanctum');
        Route::get('purchase-history-items/{id}', 'App\Http\Controllers\Api\V2\DeliveryBoyController@items')->middleware('auth:sanctum');
    });

    Route::group(['middleware' => ['app_user_unbanned']], function () {
        // customer downloadable product list
        Route::get('/digital/purchased-list', 'App\Http\Controllers\Api\V2\PurchaseHistoryController@digital_purchased_list')->middleware('auth:sanctum');
        Route::get('/purchased-products/download/{id}', 'App\Http\Controllers\Api\V2\DigitalProductController@download')->middleware('auth:sanctum');

        Route::get('wallet/history', 'App\Http\Controllers\Api\V2\WalletController@walletRechargeHistory')->middleware('auth:sanctum');
        Route::get('chat/conversations', 'App\Http\Controllers\Api\V2\ChatController@conversations')->middleware('auth:sanctum');
        Route::get('chat/messages/{id}', 'App\Http\Controllers\Api\V2\ChatController@messages')->middleware('auth:sanctum');
        Route::post('chat/insert-message', 'App\Http\Controllers\Api\V2\ChatController@insert_message')->middleware('auth:sanctum');
        Route::get('chat/get-new-messages/{conversation_id}/{last_message_id}', 'App\Http\Controllers\Api\V2\ChatController@get_new_messages')->middleware('auth:sanctum');
        Route::post('chat/create-conversation', 'App\Http\Controllers\Api\V2\ChatController@create_conversation')->middleware('auth:sanctum');
        Route::get('purchase-history', 'App\Http\Controllers\Api\V2\PurchaseHistoryController@index')->middleware('auth:sanctum');
        Route::get('purchase-history-details/{id}', 'App\Http\Controllers\Api\V2\PurchaseHistoryController@details')->middleware('auth:sanctum');
        Route::get('purchase-history-items/{id}', 'App\Http\Controllers\Api\V2\PurchaseHistoryController@items')->middleware('auth:sanctum');
        Route::get('re-order/{id}', 'App\Http\Controllers\Api\V2\PurchaseHistoryController@re_order')->middleware('auth:sanctum');
        Route::get('invoice/download/{id}', 'App\Http\Controllers\Api\V2\InvoiceController@invoice_download')->middleware('auth:sanctum');

        Route::prefix('classified')->group(function () {
            Route::get('/own-products', 'App\Http\Controllers\Api\V2\CustomerProductController@ownProducts')->middleware('auth:sanctum');
            Route::delete('/delete/{id}', 'App\Http\Controllers\Api\V2\CustomerProductController@delete')->middleware('auth:sanctum');
            Route::post('/change-status/{id}', 'App\Http\Controllers\Api\V2\CustomerProductController@changeStatus')->middleware('auth:sanctum');
        });

        Route::get('customer/info', 'App\Http\Controllers\Api\V2\CustomerController@show')->middleware('auth:sanctum');





        Route::post('coupon-apply', 'App\Http\Controllers\Api\V2\CheckoutController@apply_coupon_code')->middleware('auth:sanctum');
        Route::post('coupon-remove', 'App\Http\Controllers\Api\V2\CheckoutController@remove_coupon_code')->middleware('auth:sanctum');

        Route::post('update-address-in-cart', 'App\Http\Controllers\Api\V2\AddressController@updateAddressInCart')->middleware('auth:sanctum');

        Route::post('update-shipping-type-in-cart', 'App\Http\Controllers\Api\V2\AddressController@updateShippingTypeInCart')->middleware('auth:sanctum');
        Route::get('get-home-delivery-address', 'App\Http\Controllers\Api\V2\AddressController@getShippingInCart')->middleware('auth:sanctum');
        Route::post('shipping_cost', 'App\Http\Controllers\Api\V2\ShippingController@shipping_cost')->middleware('auth:sanctum');
        Route::post('carriers', 'App\Http\Controllers\Api\V2\CarrierController@index')->middleware('auth:sanctum');


        //Follow
        Route::controller(FollowSellerController::class)->group(function () {
            Route::get('/followed-seller', 'index')->middleware('auth:sanctum');
            Route::get('/followed-seller/store/{id}', [FollowSellerController::class, 'store'])->middleware('auth:sanctum');
            Route::get('/followed-seller/remove/{shopId}', [FollowSellerController::class, 'remove'])->middleware('auth:sanctum');
            Route::get('/followed-seller/check/{shopId}', [FollowSellerController::class, 'checkFollow'])->middleware('auth:sanctum');
        });


        Route::post('reviews/submit', 'App\Http\Controllers\Api\V2\ReviewController@submit')->name('api.reviews.submit')->middleware('auth:sanctum');

        Route::get('shop/user/{id}', 'App\Http\Controllers\Api\V2\ShopController@shopOfUser')->middleware('auth:sanctum');


        Route::get('user/shipping/address', 'App\Http\Controllers\Api\V2\AddressController@addresses')->middleware('auth:sanctum');
        Route::post('user/shipping/create', 'App\Http\Controllers\Api\V2\AddressController@createShippingAddress')->middleware('auth:sanctum');
        Route::post('user/shipping/update', 'App\Http\Controllers\Api\V2\AddressController@updateShippingAddress')->middleware('auth:sanctum');
        Route::post('user/shipping/update-location', 'App\Http\Controllers\Api\V2\AddressController@updateShippingAddressLocation')->middleware('auth:sanctum');
        Route::post('user/shipping/make_default', 'App\Http\Controllers\Api\V2\AddressController@makeShippingAddressDefault')->middleware('auth:sanctum');
        Route::get('user/shipping/delete/{address_id}', 'App\Http\Controllers\Api\V2\AddressController@deleteShippingAddress')->middleware('auth:sanctum');

        Route::get('clubpoint/get-list', 'App\Http\Controllers\Api\V2\ClubpointController@get_list')->middleware('auth:sanctum');
        Route::post('clubpoint/convert-into-wallet', 'App\Http\Controllers\Api\V2\ClubpointController@convert_into_wallet')->middleware('auth:sanctum');

        Route::get('refund-request/get-list', 'App\Http\Controllers\Api\V2\RefundRequestController@get_list')->middleware('auth:sanctum');
        Route::post('refund-request/send', 'App\Http\Controllers\Api\V2\RefundRequestController@send')->middleware('auth:sanctum');

        Route::get('bkash/begin', 'App\Http\Controllers\Api\V2\BkashController@begin')->middleware('auth:sanctum');
        Route::get('nagad/begin', 'App\Http\Controllers\Api\V2\NagadController@begin')->middleware('auth:sanctum');
        Route::post('payments/pay/wallet', 'App\Http\Controllers\Api\V2\WalletController@processPayment')->middleware('auth:sanctum');
        Route::post('payments/pay/cod', 'App\Http\Controllers\Api\V2\PaymentController@cashOnDelivery')->middleware('auth:sanctum');
        Route::post('payments/pay/manual', 'App\Http\Controllers\Api\V2\PaymentController@manualPayment')->middleware('auth:sanctum');



        Route::get('profile/counters', 'App\Http\Controllers\Api\V2\ProfileController@counters')->middleware('auth:sanctum');

        Route::post('profile/update', 'App\Http\Controllers\Api\V2\ProfileController@update')->middleware('auth:sanctum');

        Route::post('profile/update-device-token', 'App\Http\Controllers\Api\V2\ProfileController@update_device_token')->middleware('auth:sanctum');
        Route::post('profile/update-image', 'App\Http\Controllers\Api\V2\ProfileController@updateImage')->middleware('auth:sanctum');
        Route::post('profile/image-upload', 'App\Http\Controllers\Api\V2\ProfileController@imageUpload')->middleware('auth:sanctum');
        Route::post('profile/check-phone-and-email', 'App\Http\Controllers\Api\V2\ProfileController@checkIfPhoneAndEmailAvailable')->middleware('auth:sanctum');

        Route::post('file/image-upload', 'App\Http\Controllers\Api\V2\FileController@imageUpload')->middleware('auth:sanctum');
        Route::get('file-all', 'App\Http\Controllers\Api\V2\FileController@index')->middleware('auth:sanctum');
        Route::post('file/upload', 'App\Http\Controllers\Api\V2\AizUploadController@upload')->middleware('auth:sanctum');

        Route::get('wallet/balance', 'App\Http\Controllers\Api\V2\WalletController@balance')->middleware('auth:sanctum');
        Route::post('wallet/offline-recharge', 'App\Http\Controllers\Api\V2\WalletController@offline_recharge')->middleware('auth:sanctum');


        Route::get('payment-types', 'App\Http\Controllers\Api\V2\PaymentTypesController@getList')->middleware('auth:sanctum');

        Route::controller(CustomerPackageController::class)->group(function () {
            Route::post('offline/packages-payment', 'purchase_package_offline')->middleware('auth:sanctum');
            Route::post('free/packages-payment', 'purchase_package_free')->middleware('auth:sanctum');
        });
    });

    //end user bann
    Route::controller(OnlinePaymentController::class)->group(function () {
        Route::get('online-pay/init', 'init')->middleware('auth:sanctum');;
        Route::get('online-pay/success', 'paymentSuccess');
        Route::get('online-pay/done', 'paymentDone');
        Route::get('online-pay/failed', 'paymentFailed');
    });


    Route::get('coupon-list', [CouponController::class, 'couponList']);
    Route::get('coupon-products/{id}', [CouponController::class, 'getCouponProducts']);


    Route::get('get-search-suggestions', 'App\Http\Controllers\Api\V2\SearchSuggestionController@getList');
    Route::get('languages', 'App\Http\Controllers\Api\V2\LanguageController@getList');

    Route::get('classified/all', 'App\Http\Controllers\Api\V2\CustomerProductController@all');
    Route::get('classified/related-products/{id}', 'App\Http\Controllers\Api\V2\CustomerProductController@relatedProducts');
    Route::get('classified/product-details/{id}', 'App\Http\Controllers\Api\V2\CustomerProductController@productDetails');



    Route::get('seller/top', 'App\Http\Controllers\Api\V2\SellerController@topSellers');

    // Route::apiResource('banners', 'App\Http\Controllers\Api\V2\BannerController')->only('index');

    Route::get('brands/top', 'App\Http\Controllers\Api\V2\BrandController@top');

    Route::apiResource('business-settings', 'App\Http\Controllers\Api\V2\BusinessSettingController')->only('index');

    Route::get('categories/featured', 'App\Http\Controllers\Api\V2\CategoryController@featured');
    Route::get('categories/home', 'App\Http\Controllers\Api\V2\CategoryController@home');
    Route::get('categories/top', 'App\Http\Controllers\Api\V2\CategoryController@top');
    Route::apiResource('categories', 'App\Http\Controllers\Api\V2\CategoryController')->only('index');
    Route::get('sub-categories/{id}', 'App\Http\Controllers\Api\V2\SubCategoryController@index')->name('subCategories.index');

    Route::apiResource('colors', 'App\Http\Controllers\Api\V2\ColorController')->only('index');

    Route::apiResource('currencies', 'App\Http\Controllers\Api\V2\CurrencyController')->only('index');

    Route::apiResource('customers', 'App\Http\Controllers\Api\V2\CustomerController')->only('show');

    Route::apiResource('general-settings', 'App\Http\Controllers\Api\V2\GeneralSettingController')->only('index');

    Route::apiResource('home-categories', 'App\Http\Controllers\Api\V2\HomeCategoryController')->only('index');



    Route::get('filter/categories', 'App\Http\Controllers\Api\V2\FilterController@categories');
    Route::get('filter/brands', 'App\Http\Controllers\Api\V2\FilterController@brands');

    // Route::get('products/admin', 'App\Http\Controllers\Api\V2\ProductController@admin');
    Route::get('products/All', 'App\Http\Controllers\Api\V2\ProductController@all_product');
    Route::get('products/inhouse/{id?}', 'App\Http\Controllers\Api\V2\ProductController@inhouse');
    Route::get('products/seller/{id}', 'App\Http\Controllers\Api\V2\ProductController@seller');
    Route::get('products/category/{id}', 'App\Http\Controllers\Api\V2\ProductController@category')->name('api.products.category');
    Route::get('products/all-category', 'App\Http\Controllers\Api\V2\ProductController@all_categories');
    Route::get('products/sub-category/{id}', 'App\Http\Controllers\Api\V2\ProductController@subCategory')->name('products.subCategory');
    Route::get('products/sub-categorySpecific', 'App\Http\Controllers\Api\V2\ProductController@subCategorySpecific')->name('products.subCategory');
    Route::get('products/sub-sub-category/{id}', 'App\Http\Controllers\Api\V2\ProductController@subSubCategory')->name('products.subSubCategory');
    Route::get('products/brand/{id}', 'App\Http\Controllers\Api\V2\ProductController@brand')->name('api.products.brand');
    Route::get('products/todays-deal', 'App\Http\Controllers\Api\V2\ProductController@todaysDeal');
    Route::post('check_approved_pharma', 'App\Http\Controllers\Api\V2\AuthController@check_approved_pharma');
    Route::get('products/featured', 'App\Http\Controllers\Api\V2\ProductController@featured');
    Route::get('products/best-seller', 'App\Http\Controllers\Api\V2\ProductController@bestSeller');
    Route::get('products/top-from-seller/{id}', 'App\Http\Controllers\Api\V2\ProductController@topFromSeller');
    Route::get('products/related/{id}', 'App\Http\Controllers\Api\V2\ProductController@related')->name('products.related');


    // category 
    Route::post('categories', 'App\Http\Controllers\Api\V2\CategoryController@categories');




    Route::get('products/featured-from-seller/{id}', 'App\Http\Controllers\Api\V2\ProductController@newFromSeller')->name('products.featuredromSeller');
    //   Route::get('/products-name', 'App\Http\Controllers\Api\V2\ProductController@productName');
    Route::get('/name-list/{type}', 'App\Http\Controllers\Api\V2\ProductController@nameList');
    Route::post('products/variant/price', 'App\Http\Controllers\Api\V2\ProductController@getPrice');
    // Route::get('products/home', 'App\Http\Controllers\Api\V2\ProductController@home');
    Route::get('products/digital', 'App\Http\Controllers\Api\V2\ProductController@digital')->name('products.digital');
    Route::apiResource('products', 'App\Http\Controllers\Api\V2\ProductController')->except(['store', 'update', 'destroy']);


    Route::get('pharma-customer/edit/{id}', 'App\Http\Controllers\Api\V2\CustomerController@pharmaceuticalEdit');
    Route::get('credit-customer/edit/{id}', 'App\Http\Controllers\Api\V2\CustomerController@customerCreditEdit');



    Route::post('delivery-form-store', 'App\Http\Controllers\Api\V2\CustomerController@deliveryFormStore');
    // Route::post('password/forget_request', 'App\Http\Controllers\Api\V2\PasswordResetController@forgetRequest');
    // Route::post('password/verifyCode', 'App\Http\Controllers\Api\V2\PasswordResetController@verifyCode');
    // Route::post('password/confirm_reset', 'App\Http\Controllers\Api\V2\PasswordResetController@confirmReset');
    // Route::post('password/resend_code', 'App\Http\Controllers\Api\V2\PasswordResetController@resendCode');



    Route::get('delivery-info', 'App\Http\Controllers\Api\V2\ShippingController@getDeliveryInfo');
    Route::post('multi-delivery', 'App\Http\Controllers\Api\V2\ShippingController@multiDelivery');
    Route::post('verify', 'App\Http\Controllers\Api\V2\CustomerController@verify');
    Route::post('updateDelivery', 'App\Http\Controllers\Api\V2\CustomerController@updateDelivery');
    Route::controller(CustomerPackageController::class)->group(function () {
        Route::get("customer-packages", "customer_packages_list");
    });


    Route::get('reviews/product/{id}', 'App\Http\Controllers\Api\V2\ReviewController@index')->name('api.reviews.index');


    Route::get('shops/details/{id}', 'App\Http\Controllers\Api\V2\ShopController@info')->name('shops.info');
    Route::get('shops/products/all/{id}', 'App\Http\Controllers\Api\V2\ShopController@allProducts')->name('shops.allProducts');
    Route::get('shops/products/top/{id}', 'App\Http\Controllers\Api\V2\ShopController@topSellingProducts')->name('shops.topSellingProducts');
    Route::get('shops/products/featured/{id}', 'App\Http\Controllers\Api\V2\ShopController@featuredProducts')->name('shops.featuredProducts');
    Route::get('shops/products/new/{id}', 'App\Http\Controllers\Api\V2\ShopController@newProducts')->name('shops.newProducts');
    Route::get('shops/brands/{id}', 'App\Http\Controllers\Api\V2\ShopController@brands')->name('shops.brands');
    Route::apiResource('shops', 'App\Http\Controllers\Api\V2\ShopController')->only('index');

    // Route::get('sliders', 'App\Http\Controllers\Api\V2\SliderController@sliders');
    // Route::get('banners-one', 'App\Http\Controllers\Api\V2\SliderController@bannerOne');
    // Route::get('banners-two', 'App\Http\Controllers\Api\V2\SliderController@bannerTwo');
    // Route::get('banners-three', 'App\Http\Controllers\Api\V2\SliderController@bannerThree');



    Route::get('policies/seller', 'App\Http\Controllers\Api\V2\PolicyController@sellerPolicy')->name('policies.seller');
    Route::get('policies/support', 'App\Http\Controllers\Api\V2\PolicyController@supportPolicy')->name('policies.support');
    Route::get('policies/return', 'App\Http\Controllers\Api\V2\PolicyController@returnPolicy')->name('policies.return');

    // Route::get('user/info/{id}', 'App\Http\Controllers\Api\V2\UserController@info')->middleware('auth:sanctum');
    // Route::post('user/info/update', 'App\Http\Controllers\Api\V2\UserController@updateName')->middleware('auth:sanctum');

    Route::post('get-user-by-access_token', 'App\Http\Controllers\Api\V2\UserController@getUserInfoByAccessToken');

    Route::get('cities', 'App\Http\Controllers\Api\V2\AddressController@getCities');
    Route::get('states', 'App\Http\Controllers\Api\V2\AddressController@getStates');
    Route::get('countries', 'App\Http\Controllers\Api\V2\AddressController@getCountries');

    Route::get('cities-by-state/{state_id}', 'App\Http\Controllers\Api\V2\AddressController@getCitiesByState');
    Route::get('states-by-country/{country_id}', 'App\Http\Controllers\Api\V2\AddressController@getStatesByCountry');


    // Route::post('coupon/apply', 'App\Http\Controllers\Api\V2\CouponController@apply')->middleware('auth:sanctum');


    Route::any('stripe', 'App\Http\Controllers\Api\V2\StripeController@stripe');
    Route::any('stripe/payment/callback', 'App\Http\Controllers\Api\V2\StripeController@callback')->name('api.stripe.callback');


    Route::any('paypal/payment/url', 'App\Http\Controllers\Api\V2\PaypalController@getUrl')->name('api.paypal.url');
    Route::any('amarpay', [AamarpayController::class, 'pay'])->name('api.amarpay.url');
    Route::any('khalti/payment/pay', 'App\Http\Controllers\Api\V2\KhaltiController@pay')->name('api.khalti.url');
    Route::any('razorpay/pay-with-razorpay', 'App\Http\Controllers\Api\V2\RazorpayController@payWithRazorpay')->name('api.razorpay.payment');
    Route::any('razorpay/payment', 'App\Http\Controllers\Api\V2\RazorpayController@payment')->name('api.razorpay.payment');
    Route::any('paystack/init', 'App\Http\Controllers\Api\V2\PaystackController@init')->name('api.paystack.init');
    Route::any('iyzico/init', 'App\Http\Controllers\Api\V2\IyzicoController@init')->name('api.iyzico.init');

    Route::get('bkash/api/webpage/{token}/{amount}', 'App\Http\Controllers\Api\V2\BkashController@webpage')->name('api.bkash.webpage');


    Route::any('bkash/api/execute/{token}', 'App\Http\Controllers\Api\V2\BkashController@execute')->name('api.bkash.execute');
    Route::any('bkash/api/fail', 'App\Http\Controllers\Api\V2\BkashController@fail')->name('api.bkash.fail');
    Route::post('bkash/api/process', 'App\Http\Controllers\Api\V2\BkashController@process')->name('api.bkash.process');


    Route::any('nagad/verify/{payment_type}', 'App\Http\Controllers\Api\V2\NagadController@verify')->name('app.nagad.callback_url');
    Route::post('nagad/process', 'App\Http\Controllers\Api\V2\NagadController@process');

    Route::get('sslcommerz/begin', 'App\Http\Controllers\Api\V2\SslCommerzController@begin');

    Route::any('flutterwave/payment/url', 'App\Http\Controllers\Api\V2\FlutterwaveController@getUrl')->name('api.flutterwave.url');

    Route::any('paytm/payment/pay', 'App\Http\Controllers\Api\V2\PaytmController@pay')->name('api.paytm.pay');
    Route::get('instamojo/pay', 'App\Http\Controllers\Api\V2\InstamojoController@pay');

    Route::get('payfast/initiate', 'App\Http\Controllers\Api\V2\PayfastController@pay');

    Route::get('/myfatoorah/initiate', 'App\Http\Controllers\Api\V2\MyfatoorahController@pay');

    Route::post('offline/payment/submit', 'App\Http\Controllers\Api\V2\OfflinePaymentController@submit')->name('api.offline.payment.submit');


    Route::get('flash-deals', 'App\Http\Controllers\Api\V2\FlashDealController@index');
    Route::get('flash-deal-products/{id}', 'App\Http\Controllers\Api\V2\FlashDealController@products');

    //Addon list
    Route::get('addon-list', 'App\Http\Controllers\Api\V2\ConfigController@addon_list');
    //Activated social login list
    Route::get('activated-social-login', 'App\Http\Controllers\Api\V2\ConfigController@activated_social_login');

    //Business Sttings list
    Route::post('business-settings', 'App\Http\Controllers\Api\V2\ConfigController@business_settings');
    //Pickup Point list
    Route::get('pickup-list', 'App\Http\Controllers\Api\V2\ShippingController@pickup_list');


    Route::withoutMiddleware([EnsureSystemKey::class])->group(function () {
        Route::get('google-recaptcha', function () {
            return view("frontend.google_recaptcha.app_recaptcha");
        });
        Route::any('paypal/payment/done', 'App\Http\Controllers\Api\V2\PaypalController@getDone')->name('api.paypal.done');
        Route::any('paypal/payment/cancel', 'App\Http\Controllers\Api\V2\PaypalController@getCancel')->name('api.paypal.cancel');
        Route::any('amarpay/success', [AamarpayController::class, 'success'])->name('api.amarpay.success');
        Route::any('amarpay/cancel', [AamarpayController::class, 'fail'])->name('api.amarpay.cancel');
        Route::any('khalti/payment/success', 'App\Http\Controllers\Api\V2\KhaltiController@paymentDone')->name('api.khalti.success');
        Route::any('khalti/payment/cancel', 'App\Http\Controllers\Api\V2\KhaltiController@getCancel')->name('api.khalti.cancel');
        Route::post('razorpay/success', 'App\Http\Controllers\Api\V2\RazorpayController@payment_success')->name('api.razorpay.success');
        Route::post('paystack/success', 'App\Http\Controllers\Api\V2\PaystackController@payment_success')->name('api.paystack.success');
        Route::any('iyzico/callback', 'App\Http\Controllers\Api\V2\IyzicoController@callback')->name('api.iyzico.callback');
        Route::post('iyzico/success', 'App\Http\Controllers\Api\V2\IyzicoController@payment_success')->name('api.iyzico.success');

        Route::any('bkash/api/callback', 'App\Http\Controllers\Api\V2\BkashController@callback')->name('api.bkash.callback');
        Route::post('bkash/api/success', 'App\Http\Controllers\Api\V2\BkashController@payment_success')->name('api.bkash.success');
        Route::any('bkash/api/checkout/{token}/{amount}', 'App\Http\Controllers\Api\V2\BkashController@checkout')->name('api.bkash.checkout');

        // Route::any('stripe/create-checkout-session', 'App\Http\Controllers\Api\V2\StripeController@create_checkout_session')->name('api.stripe.get_token');
        // Route::get('stripe/success', 'App\Http\Controllers\Api\V2\StripeController@payment_success');
        // Route::any('stripe/cancel', 'App\Http\Controllers\Api\V2\StripeController@cancel')->name('api.stripe.cancel');

        Route::any('sslcommerz/success', 'App\Http\Controllers\Api\V2\SslCommerzController@payment_success');
        Route::any('sslcommerz/fail', 'App\Http\Controllers\Api\V2\SslCommerzController@payment_fail');
        Route::any('sslcommerz/cancel', 'App\Http\Controllers\Api\V2\SslCommerzController@payment_cancel');
        Route::any('flutterwave/payment/callback', 'App\Http\Controllers\Api\V2\FlutterwaveController@callback')->name('api.flutterwave.callback');
        Route::any('paytm/payment/callback', 'App\Http\Controllers\Api\V2\PaytmController@callback')->name('api.paytm.callback');
        Route::get('instamojo/success', 'App\Http\Controllers\Api\V2\InstamojoController@success');
        Route::get('instamojo/failed', 'App\Http\Controllers\Api\V2\InstamojoController@failed');

        //Payfast routes <starts>
        Route::controller(PayfastController::class)->group(function () {
            Route::any('/payfast/notify', 'payfast_notify')->name('api.payfast.notify');
            Route::any('/payfast/return', 'payfast_return')->name('api.payfast.return');
            Route::any('/payfast/cancel', 'payfast_cancel')->name('api.payfast.cancel');
        });
        //Payfast routes <ends>

        Route::get('/myfatoorah/callback', 'App\Http\Controllers\Api\V2\MyfatoorahController@callback')->name('api.myfatoorah.callback');


        Route::any('/phonepe/redirecturl', 'App\Http\Controllers\Api\V2\PhonepeController@phonepe_redirecturl')->name('api.phonepe.redirecturl');
        Route::any('/phonepe/callbackUrl', 'App\Http\Controllers\Api\V2\PhonepeController@phonepe_callbackUrl')->name('api.phonepe.callbackUrl');
    });



    //====================== New API's =================
});

Route::fallback(function () {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});
