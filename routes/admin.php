<?php

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

use App\Http\Controllers\AffiliatorCommissionController;
use App\Http\Controllers\Backend\WithdrawController;
use App\Http\Controllers\NewAffiliateController;
use App\Http\Controllers\SmsHistoryController;
use App\Http\Controllers\SmsSettingsController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::post('/update', 'UpdateController@step0')->name('update');
Route::get('/update/step1', 'UpdateController@step1')->name('update.step1');
Route::get('/update/step2', 'UpdateController@step2')->name('update.step2');

Route::get('/admin', 'HomeController@admin_dashboard')->name('admin.dashboard')->middleware(['auth', 'admin']);
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {

	//new modules
	Route::group(['prefix' => 'affiliates'],function(){


		Route::get('/settings',[NewAffiliateController::class,'index'])->name('affiliates.settings');
		Route::get('/affiliators',[NewAffiliateController::class,'affiliators'])->name('affiliates.affiliators');
		Route::get('/affiliators/approve/{id}',[NewAffiliateController::class,'approve'])->name('affiliates.affiliators.approve');
		Route::get('/affiliators/show/{id}',[NewAffiliateController::class,'view'])->name('affiliates.affiliators.view');
		Route::get('/affiliators/orders/{id}',[NewAffiliateController::class,'orders'])->name('affiliates.affiliators.orders');

		Route::get('/affiliators/edit/{id}',[NewAffiliateController::class,'edit'])->name('affiliates.affiliators.edit');
		Route::get('/affiliators/reject/{id}',[NewAffiliateController::class,'reject'])->name('affiliates.affiliators.reject');
		Route::post('/settings/store',[NewAffiliateController::class,'store'])->name('affiliates.settings.post');
		Route::get('/settings/delete/{id}',[NewAffiliateController::class,'delete'])->name('affiliates.affiliators.delete');


		Route::get('/affiliators/assign-products/{id}',[NewAffiliateController::class,'assignProducts'])->name('affiliates.affiliators.assign-products');
		Route::post('/affiliators/assign-products/{id}',[NewAffiliateController::class,'assignProductsStore'])->name('affiliates.affiliators.assign-products.store');
		
		//commissun request
		Route::get('/affiliators/commission-requests',[AffiliatorCommissionController::class,'index'])->name('affiliates.commission-requests.index');
		Route::get('/affiliators/commission-requests/update/{id}',[AffiliatorCommissionController::class,'update'])->name('affiliates.commission-requests.update');
	
	
	});

	// add new module team by dev_ak
	Route::group(['prefix' => 'team'],function(){

		Route::get('/', 'TeamController@index')->name('team.index');
		Route::get('/create', 'TeamController@create')->name('team.create');
		Route::post('/store', 'TeamController@store')->name('b.team.store');
		Route::get('edit/{id}', 'TeamController@edit')->name('team.edit');
		Route::post('update/{id}', 'TeamController@update')->name('b.team.update');
		Route::delete('/team/{id}', [TeamController::class, 'destroy'])->name('team.destroy');
	});

    //withdraw modules
    Route::group(['prefix' => 'withdraws'],function(){
        Route::get('/list',[WithdrawController::class,'list'])->name('withdraw.list');
        Route::post('/status',[WithdrawController::class,'status'])->name('withdraw.status');
    });

	//Update Routes
	Route::get('/sms-history/index',[SmsHistoryController::class,'index'])->name('sms_history.index');
	Route::resource('categories', 'CategoryController');
	Route::get('/categories/edit/{id}', 'CategoryController@edit')->name('categories.edit');
	Route::get('/categories/destroy/{id}', 'CategoryController@destroy')->name('categories.destroy');
	Route::post('/categories/featured', 'CategoryController@updateFeatured')->name('categories.featured');

	Route::get('run-job',function(){
		Artisan::call('queue:work');
	})->name('run.job');

	Route::resource('brands', 'BrandController');
	Route::get('/brands/edit/{id}', 'BrandController@edit')->name('brands.edit');
	Route::get('/brands/destroy/{id}', 'BrandController@destroy')->name('brands.destroy');
	
	Route::resource('announcement', 'AnnouncementController');
	Route::get('/announcement/edit/{id}', 'AnnouncementController@edit')->name('announcement.edit');

	Route::get('/products/admin', 'ProductController@admin_products')->name('products.admin');
	Route::get('/products/seller', 'ProductController@seller_products')->name('products.seller');
	Route::get('/products/all', 'ProductController@all_products')->name('products.all');
	Route::get('/products/create', 'ProductController@create')->name('products.create');
	Route::get('/products/admin/{id}/edit', 'ProductController@admin_product_edit')->name('products.admin.edit');
	Route::get('/products/seller/{id}/edit', 'ProductController@seller_product_edit')->name('products.seller.edit');
	Route::post('/products/todays_deal', 'ProductController@updateTodaysDeal')->name('products.todays_deal');
	Route::post('/products/is_suggestion', 'ProductController@updateIsSuggestion')->name('products.is_suggestion');
	Route::post('/products/featured', 'ProductController@updateFeatured')->name('products.featured');
	Route::post('/products/get_products_by_subcategory', 'ProductController@get_products_by_subcategory')->name('products.get_products_by_subcategory');

	Route::resource('sellers', 'SellerController');
	Route::get('sellers_ban/{id}', 'SellerController@ban')->name('sellers.ban');
	Route::get('/sellers/destroy/{id}', 'SellerController@destroy')->name('sellers.destroy');
	Route::get('/sellers/view/{id}/verification', 'SellerController@show_verification_request')->name('sellers.show_verification_request');
	Route::get('/sellers/approve/{id}', 'SellerController@approve_seller')->name('sellers.approve');
	Route::get('/sellers/reject/{id}', 'SellerController@reject_seller')->name('sellers.reject');
	Route::get('/sellers/login/{id}', 'SellerController@login')->name('sellers.login');
	Route::post('/sellers/payment_modal', 'SellerController@payment_modal')->name('sellers.payment_modal');
	Route::get('/seller/payments', 'PaymentController@payment_histories')->name('sellers.payment_histories');
	Route::get('/seller/payments/show/{id}', 'PaymentController@show')->name('sellers.payment_history');

	Route::resource('customers', 'CustomerController');
	Route::get('customers_ban/{customer}', 'CustomerController@ban')->name('customers.ban');
	Route::get('/customers/login/{id}', 'CustomerController@login')->name('customers.login');
	Route::get('/customers/destroy/{id}', 'CustomerController@destroy')->name('customers.destroy');

	//edit customer data //
	Route::get('/customers/edit/{id}', 'CustomerController@edit')->name('customers.edit');

	Route::get('/newsletter', 'NewsletterController@index')->name('newsletters.index');
	Route::post('/newsletter/send', 'NewsletterController@send')->name('newsletters.send');
	Route::post('/newsletter/test/smtp', 'NewsletterController@testEmail')->name('test.smtp');

	Route::resource('profile', 'ProfileController');

	Route::post('/business-settings/update', 'BusinessSettingsController@update')->name('business_settings.update');
	Route::post('/business-settings/update/activation', 'BusinessSettingsController@updateActivationSettings')->name('business_settings.update.activation');
	Route::get('/general-setting', 'BusinessSettingsController@general_setting')->name('general_setting.index');
	Route::get('/activation', 'BusinessSettingsController@activation')->name('activation.index');
	Route::get('/payment-method', 'BusinessSettingsController@payment_method')->name('payment_method.index');
	Route::get('/file_system', 'BusinessSettingsController@file_system')->name('file_system.index');
	Route::get('/social-login', 'BusinessSettingsController@social_login')->name('social_login.index');
	Route::get('/smtp-settings', 'BusinessSettingsController@smtp_settings')->name('smtp_settings.index');
	Route::get('/google-analytics', 'BusinessSettingsController@google_analytics')->name('google_analytics.index');
	Route::get('/google-recaptcha', 'BusinessSettingsController@google_recaptcha')->name('google_recaptcha.index');
	Route::get('/facebook-chat', 'BusinessSettingsController@facebook_chat')->name('facebook_chat.index');
	Route::post('/env_key_update', 'BusinessSettingsController@env_key_update')->name('env_key_update.update');
	Route::post('/payment_method_update', 'BusinessSettingsController@payment_method_update')->name('payment_method.update');
	Route::post('/google_analytics', 'BusinessSettingsController@google_analytics_update')->name('google_analytics.update');
	Route::post('/google_recaptcha', 'BusinessSettingsController@google_recaptcha_update')->name('google_recaptcha.update');
	Route::post('/facebook_chat', 'BusinessSettingsController@facebook_chat_update')->name('facebook_chat.update');
	Route::post('/facebook_pixel', 'BusinessSettingsController@facebook_pixel_update')->name('facebook_pixel.update');
	Route::get('/currency', 'CurrencyController@currency')->name('currency.index');
	Route::post('/currency/update', 'CurrencyController@updateCurrency')->name('currency.update');
	Route::post('/your-currency/update', 'CurrencyController@updateYourCurrency')->name('your_currency.update');
	Route::get('/currency/create', 'CurrencyController@create')->name('currency.create');
	Route::post('/currency/store', 'CurrencyController@store')->name('currency.store');
	Route::post('/currency/currency_edit', 'CurrencyController@edit')->name('currency.edit');
	Route::post('/currency/update_status', 'CurrencyController@update_status')->name('currency.update_status');
	Route::get('/verification/form', 'BusinessSettingsController@seller_verification_form')->name('seller_verification_form.index');
	Route::post('/verification/form', 'BusinessSettingsController@seller_verification_form_update')->name('seller_verification_form.update');
	Route::get('/vendor_commission', 'BusinessSettingsController@vendor_commission')->name('business_settings.vendor_commission');
	Route::post('/vendor_commission_update', 'BusinessSettingsController@vendor_commission_update')->name('business_settings.vendor_commission.update');

	Route::resource('/languages', 'LanguageController');
	Route::post('/languages/{id}/update', 'LanguageController@update')->name('languages.update');
	Route::get('/languages/destroy/{id}', 'LanguageController@destroy')->name('languages.destroy');
	Route::post('/languages/update_rtl_status', 'LanguageController@update_rtl_status')->name('languages.update_rtl_status');
	Route::post('/languages/key_value_store', 'LanguageController@key_value_store')->name('languages.key_value_store');

	// website setting
	Route::group(['prefix' => 'website'], function () {
		Route::view('/header', 'backend.website_settings.header')->name('website.header');
		Route::view('/footer', 'backend.website_settings.footer')->name('website.footer');
		Route::view('/pages', 'backend.website_settings.pages.index')->name('website.pages');
		Route::view('/appearance', 'backend.website_settings.appearance')->name('website.appearance');
		Route::resource('custom-pages', 'PageController');
		Route::get('/custom-pages/edit/{id}', 'PageController@edit')->name('custom-pages.edit');
		Route::get('/custom-pages/destroy/{id}', 'PageController@destroy')->name('custom-pages.destroy');
	});

	Route::resource('roles', 'RoleController');
	Route::get('/roles/edit/{id}', 'RoleController@edit')->name('roles.edit');
	Route::get('/roles/destroy/{id}', 'RoleController@destroy')->name('roles.destroy');

	Route::resource('staffs', 'StaffController');
	Route::get('/staffs/destroy/{id}', 'StaffController@destroy')->name('staffs.destroy');


	//Blog Section
	Route::resource('blog-category', 'BlogCategoryController');
	Route::get('/blog-category/destroy/{id}', 'BlogCategoryController@destroy')->name('blog-category.destroy');
	Route::resource('blog', 'BlogController');
	Route::get('/blog/destroy/{id}', 'BlogController@destroy')->name('blog.destroy');
	Route::post('/blog/change-status', 'BlogController@change_status')->name('blog.change-status');


	Route::resource('flash_deals', 'FlashDealController');
	Route::get('/flash_deals/edit/{id}', 'FlashDealController@edit')->name('flash_deals.edit');
	Route::get('/flash_deals/destroy/{id}', 'FlashDealController@destroy')->name('flash_deals.destroy');
	Route::post('/flash_deals/update_status', 'FlashDealController@update_status')->name('flash_deals.update_status');
	Route::post('/flash_deals/update_featured', 'FlashDealController@update_featured')->name('flash_deals.update_featured');
	Route::post('/flash_deals/product_discount', 'FlashDealController@product_discount')->name('flash_deals.product_discount');
	Route::post('/flash_deals/product_discount_edit', 'FlashDealController@product_discount_edit')->name('flash_deals.product_discount_edit');

	//Subscribers
	Route::get('/subscribers', 'SubscriberController@index')->name('subscribers.index');
	Route::get('/subscribers/destroy/{id}', 'SubscriberController@destroy')->name('subscriber.destroy');

	// Route::get('/orders', 'OrderController@admin_orders')->name('orders.index.admin');
	// Route::get('/orders/{id}/show', 'OrderController@show')->name('orders.show');
	// Route::get('/sales/{id}/show', 'OrderController@sales_show')->name('sales.show');
	// Route::get('/sales', 'OrderController@sales')->name('sales.index')
	Route::resource('orders', 'OrderController');
	// All Orders


	Route::get('/all_orders', 'OrderController@all_orders')->name('all_orders.index');
	Route::get('/all_orders/{id}/show', 'OrderController@all_orders_show')->name('all_orders.show');

	Route::get('/all_orders/{id}/edit', 'OrderController@all_orders_edit')->name('all_orders.edit');
	Route::get('/all_orders/{id}/update', 'OrderController@all_orders_update')->name('all_orders.update');

	Route::post('all_orders_update_price/update/{id}', 'OrderController@all_ordersupdate_price_2')->name('all_orders.update_price');



	// Inhouse Orders
	Route::get('/inhouse-orders', 'OrderController@admin_orders')->name('inhouse_orders.index');
	Route::get('/inhouse-orders/{id}/show', 'OrderController@show')->name('inhouse_orders.show');

	// Inhouse Orders
	Route::get('/pos-orders', 'OrderController@pos_orders')->name('pos_orders.index');
	Route::get('/pos-orders/{id}/show', 'OrderController@show')->name('pos_orders.show');

	// Seller Orders
	Route::get('/seller_orders', 'OrderController@seller_orders')->name('seller_orders.index');
	Route::get('/seller_orders/{id}/show', 'OrderController@seller_orders_show')->name('seller_orders.show');

	// Pickup point orders
	Route::get('orders_by_pickup_point', 'OrderController@pickup_point_order_index')->name('pick_up_point.order_index');
	Route::get('/orders_by_pickup_point/{id}/show', 'OrderController@pickup_point_order_sales_show')->name('pick_up_point.order_show');

	Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');
	Route::get('/orders/cancel/{id}', 'OrderController@cancel')->name('orders.cancel');
	Route::get('invoice/admin/{order_id}', 'InvoiceController@admin_invoice_download')->name('admin.invoice.download');
	Route::get('invoice/print/{order_id}', 'InvoiceController@admin_invoice_print')->name('admin.invoice.print');
	Route::get('invoice/print-a4/{order_id}', 'InvoiceController@admin_invoice_print_a4')->name('admin.invoice.print_a4');


	Route::post('/pay_to_seller', 'CommissionController@pay_to_seller')->name('commissions.pay_to_seller');

	//Reports
	Route::get('/sale_report', 'ReportController@sale_report')->name('sale_report.index');
	Route::get('/prodwise_sale_report', 'ReportController@prodwise_sale_report')->name('sale_report.product_wise');
	Route::get('/stock_report', 'ReportController@stock_report')->name('stock_report.index');
	Route::get('/in_house_sale_report', 'ReportController@in_house_sale_report')->name('in_house_sale_report.index');
	Route::get('/seller_sale_report', 'ReportController@seller_sale_report')->name('seller_sale_report.index');
	Route::get('/wish_report', 'ReportController@wish_report')->name('wish_report.index');
	Route::get('/user_search_report', 'ReportController@user_search_report')->name('user_search_report.index');

	//Coupons
	Route::resource('coupon', 'CouponController');
	Route::post('/coupon/get_form', 'CouponController@get_coupon_form')->name('coupon.get_coupon_form');
	Route::post('/coupon/get_form_edit', 'CouponController@get_coupon_form_edit')->name('coupon.get_coupon_form_edit');
	Route::get('/coupon/destroy/{id}', 'CouponController@destroy')->name('coupon.destroy');

	//Reviews
	Route::get('/reviews', 'ReviewController@index')->name('reviews.index');
	Route::post('/reviews/published', 'ReviewController@updatePublished')->name('reviews.published');

	//Support_Ticket
	Route::get('support_ticket/', 'SupportTicketController@admin_index')->name('support_ticket.admin_index');
	Route::get('support_ticket/{id}/show', 'SupportTicketController@admin_show')->name('support_ticket.admin_show');
	Route::post('support_ticket/reply', 'SupportTicketController@admin_store')->name('support_ticket.admin_store');

	//Pickup_Points
	Route::resource('pick_up_points', 'PickupPointController');
	Route::get('/pick_up_points/edit/{id}', 'PickupPointController@edit')->name('pick_up_points.edit');
	Route::get('/pick_up_points/destroy/{id}', 'PickupPointController@destroy')->name('pick_up_points.destroy');

	//conversation of seller customer
	Route::get('conversations', 'ConversationController@admin_index')->name('conversations.admin_index');
	Route::get('conversations/{id}/show', 'ConversationController@admin_show')->name('conversations.admin_show');

	Route::post('/sellers/profile_modal', 'SellerController@profile_modal')->name('sellers.profile_modal');
	Route::post('/sellers/approved', 'SellerController@updateApproved')->name('sellers.approved');

	Route::resource('attributes', 'AttributeController');
	Route::get('/attributes/edit/{id}', 'AttributeController@edit')->name('attributes.edit');
	Route::get('/attributes/destroy/{id}', 'AttributeController@destroy')->name('attributes.destroy');

	Route::resource('addons', 'AddonController');
	Route::post('/addons/activation', 'AddonController@activation')->name('addons.activation');

	Route::get('/customer-bulk-upload/index', 'CustomerBulkUploadController@index')->name('customer_bulk_upload.index');
	Route::post('/bulk-user-upload', 'CustomerBulkUploadController@user_bulk_upload')->name('bulk_user_upload');
	Route::post('/bulk-customer-upload', 'CustomerBulkUploadController@customer_bulk_file')->name('bulk_customer_upload');
	Route::get('/user', 'CustomerBulkUploadController@pdf_download_user')->name('pdf.download_user');

	Route::get('/users/export', 'CustomerController@export');
	//Customer Package

	Route::resource('customer_packages', 'CustomerPackageController');
	Route::get('/customer_packages/edit/{id}', 'CustomerPackageController@edit')->name('customer_packages.edit');
	Route::get('/customer_packages/destroy/{id}', 'CustomerPackageController@destroy')->name('customer_packages.destroy');

	//Classified Products
	Route::get('/classified_products', 'CustomerProductController@customer_product_index')->name('classified_products');
	Route::post('/classified_products/published', 'CustomerProductController@updatePublished')->name('classified_products.published');

	//Shipping Configuration
	Route::get('/shipping_configuration', 'BusinessSettingsController@shipping_configuration')->name('shipping_configuration.index');
	Route::post('/shipping_configuration/update', 'BusinessSettingsController@shipping_configuration_update')->name('shipping_configuration.update');

	// Route::resource('pages', 'PageController');
	// Route::get('/pages/destroy/{id}', 'PageController@destroy')->name('pages.destroy');

	Route::resource('countries', 'CountryController');
	Route::post('/countries/status', 'CountryController@updateStatus')->name('countries.status');

	Route::resource('cities', 'CityController');
	Route::get('/cities/edit/{id}', 'CityController@edit')->name('cities.edit');
	Route::get('/cities/destroy/{id}', 'CityController@destroy')->name('cities.destroy');
	Route::post('/cities/bulk-cost', 'CityController@bulk_cost')->name('cities.bulk_cost');

	Route::view('/system/update', 'backend.system.update')->name('system_update');
	Route::view('/system/server-status', 'backend.system.server_status')->name('system_server');

	// uploaded files
	Route::any('/uploaded-files/file-info', 'AizUploadController@file_info')->name('uploaded-files.info');
	Route::resource('/uploaded-files', 'AizUploadController');
	Route::get('/uploaded-files/destroy/{id}', 'AizUploadController@destroy')->name('uploaded-files.destroy');

	//Discount ruls
	Route::get('discount-ruls', 'DiscountRuleController@index')->name('discount.index');
	Route::get('discount-ruls-create', 'DiscountRuleController@create')->name('discount.create');
	Route::post('discount-ruls-store', 'DiscountRuleController@store')->name('discount.store');
	Route::get('discount-ruls-edit/{id}', 'DiscountRuleController@edit')->name('discount.edit');
	Route::post('discount-ruls-update/{id}', 'DiscountRuleController@update')->name('discount.update');
	Route::get('discount-ruls-delete/{id}', 'DiscountRuleController@delete')->name('discount.delete');

	Route::resource('deliveryman', 'DeliverymenController');
	Route::get('/deliveryman/delete/{deliveryMan}', 'DeliverymenController@destroy')->name('deliveryman.destroy');
	Route::post('orders/{order}/assign-delivery-man', 'DeliverymenController@assignDeliveryMan')->name('orders.assignDeliveryMan');

	//Added by anik rifat
	Route::get('/cm', 'CrmController@index')->name('cm.index');
	Route::get('/cm', 'CrmController@index')->name('cm.index');


	Route::get('/area_wise', 'CrmController@prodwise_sale_report')->name('cm.product_wise');
	Route::get('/stock_cm', 'CrmController@stock_report')->name('stock_cm.index');
	Route::get('/in_house_cm', 'CrmController@in_house_sale_report')->name('in_house_cm.index');
	Route::get('/seller_cm', 'CrmController@seller_sale_report')->name('seller_cm.index');
	Route::get('/wish_cm', 'CrmController@wish_report')->name('wish_cm.index');
	Route::get('/user_search_cm', 'CrmController@user_search_report')->name('user_search_cm.index');
	Route::get('/area_wise/customers/{city}', 'CrmController@customersByCity')->name('cm.customer.list');
	Route::post('/area_wise/customers/get', 'CrmController@customerGet')->name('crm.customer.get');

	//sms settings
	Route::get('/sms-settings/index',[SmsSettingsController::class,'index'])->name('sms-settings.index');
	Route::post('/sms-settings',[SmsSettingsController::class,'store'])->name('sms-settings.store');
	Route::get('/sms-settings/status',[SmsSettingsController::class,'status'])->name('sms-settings.status');

	Route::get('/sms-send-logs', 'SmsSendLogController@index')->name('smssendlog.index');

	Route::get('/inventory', 'InventoryController@index')->name('inventory.index');
	Route::get('/warehouse', 'InventoryController@warehouse')->name('inventory.warehouse');
	Route::post('/inventory/decrease_inventory', 'InventoryController@decreaseInventory')->name('decrease_inventory');
	Route::post('/inventory/increase_inventory', 'InventoryController@increaseInventory')->name('increase_inventory');
	Route::post('/inventory/adjust_inventory', 'InventoryController@adjustInventory')->name('adjust_inventory');
	Route::post('/inventory/adjust/stock', 'InventoryController@adjustStock')->name('adjust.stock');
	Route::post('/inventory/update/stock', 'InventoryController@updateStock')->name('update.stock');
	Route::get('/inventory/demand-chart/{productId}/{range?}', 'InventoryController@getDemandChartData')->name('demand.chart');
	Route::get('/inventory/sale', 'InventoryController@analyzeSalesPatterns')->name('sale.pattern');
	Route::get('/inventory/product_metrics', 'InventoryController@productMetrics')->name('sale.metrics');

	Route::get('/productsByCategory/{category}', 'CrmController@productsByCategory')->name('products.getcategory');

});


Route::get('/datatable/customers/{city}/{product}/{category}/{date?}', 'CrmController@customersDataTable')->name('datatable.customers');
Route::post('/crm/print', 'CrmController@customersDataTablePrint')->name('crm.print');

