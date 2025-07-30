<?php

namespace App\Http\Controllers;

use App\Category;
use App\Jobs\SendBulkSmsJob;
use App\Jobs\SendSmsJob;
use App\messageSendLog;
use App\Product;
use App\Services\CrmService;
use App\SmsJob;
use App\SmsSendLog;
use App\SmsSetting;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrmController extends Controller
{
    public function index(CrmService $crmService)
    {
        $categoryData = $crmService->ordersGroupedByCategory();
        // dd($categoryData);
        $all_cities = $crmService->getAllCities();
        $products = Product::all();
        $categories = Category::all();
        // dd($categories);
        $cityData = $crmService->ordersGroupedByCity();

        return view('backend.crm.customers_list', compact('all_cities', 'categories', 'products', 'cityData', 'categoryData'));
    }

    public function prodwise_sale_report(Request $request, CrmService $crmService)
    {
        $ordersGroupedByCity = $crmService->ordersGroupedByCity();

        return view('backend.crm.orders_by_city', compact('ordersGroupedByCity'));
    }

    public function customersByCity(CrmService $crmService, $city)
    {
        $all_cities = $crmService->getAllCities();
        $all_categories = Category::all();


        return view('backend.crm.customers_list', compact('city', 'all_cities', 'all_categories'));
    }

    public function customerGet(Request $request)
    {
        // dd("ok");
        $data = $request->validate([
            'content' => 'required',
            'selected_customers' => 'required|array',
            'selected_customers.*' => 'exists:users,id',
        ]);

        // Fetch customers and format phone numbers
        $settings = SmsSetting::find(1);
     


        $customers = User::with('orders')
            ->whereIn('id', $request->input('selected_customers'))
            ->get()
            ->each(function ($customer) use ($data,$settings) {
                $startTime = $settings->from_time;
                $endTime = $settings->to_time;
                // sendSMS($customer->phone, env('APP_NAME'), "okok");
                $smsAlreadySend = SmsJob::where('phone_number', $customer->phone)
                    ->where('status', 'sent')
                    ->whereBetween('updated_at', [$startTime, $endTime])
                    ->count();
                if ($smsAlreadySend > 0) {

                    $smsJob = SmsJob::create([
                        // 'phone_number' => $customer->phone,
                        'phone_number' => $customer->phone,
                        'message' => "Sms Already Send",
                        'status' => 'pending',
                    ]);
                    Log::info("Already Send");
                    Log::info($customer->phone);
                    //dd($smsAlreadySend);
                }
                else{

                    $ok = SendBulkSmsJob::dispatch($customer->phone, $data['content']);
                    // dd($ok);
                    Log::info("Send");
                    Log::info($customer->phone);


                }
            });

        flash(translate('SMS has been sent successfully'))->success();
        return redirect()->back();
    }

    private function formatPhoneNumber($phone)
    {
        $phone = ltrim($phone, '+');

        if (str_starts_with($phone, '01')) {
            $phone = '880' . $phone;
        } elseif (!str_starts_with($phone, '8801') || strlen($phone) < 13) {
            return null;
        }

        return $phone;
    }

    public function customerGet090(Request $request)
    {
        $data = $request->validate([
            'content' => 'required',
            'selected_customers' => 'required|array',
            'selected_customers.*' => 'exists:users,id',
        ]);

        // Fetch all selected customers
        $selectedCustomers = $request->input('selected_customers');

        // Process customers in smaller batches of 500 users to avoid timeout issues
        User::whereIn('id', $selectedCustomers)
            ->chunk(500, function ($customers) use ($data) {
                foreach ($customers as $customer) {
                    // Validate phone number: must not be null and must be at least 11 characters long
                    if (!is_null($customer->phone) && strlen($customer->phone) >= 11) {
                        // Send SMS to valid phone numbers
                        sendSMS($customer->phone, env('APP_NAME'), $data['content']);
                    }
                    // Invalid phone numbers are automatically skipped
                }
            });

        // Display success message after sending SMS to valid customers
        flash(translate('Sms has been sent successfully'))->success();
        return redirect()->back();
    }


    public function productsByCategory(Category $category)
    {
        $products = $category->products->pluck('name', 'id');

        $response = $products->map(function ($productName, $productId) {
            return [
                'id' => $productId,
                'product' => $productName,
            ];
        });
        // dd($response);
        return $response->toJson();
    }

    public function customersDataTable(CrmService $crmService, $city, $product, $categoryId, $date = null)
    {

        $summery = $crmService->getOrdersByFilter($city, $product, $categoryId, $date);
        // dd($summery);
        $data = $this->formatCustomersDataTable($summery);
        return $data;
    }

    public function getFilteredCustomers($city, $category)
    {
        if ($city == 0 && $category == 0) {
            return User::with('orders')->get();
        }

        $cityId = ($city == 0) ? null : $city;
        $categoryId = ($category == 0) ? null : $category;
        $query = User::with('orders');

        if ($cityId !== null) {
            $query->whereHas('orders', function ($innerQuery) use ($cityId) {
                $innerQuery->where('cancelled', 0)
                    ->where('payment_status', 'paid')
                    ->where('shipping_address', 'like', '%' . $cityId . '%');
            });
        }

        if ($categoryId !== null) {
            $query->whereHas('orders', function ($innerQuery) use ($categoryId) {
                $innerQuery->where('cancelled', 0)
                    ->where('payment_status', 'paid')
                    ->whereHas('orderDetails', function ($innerInnerQuery) use ($categoryId) {
                        $innerInnerQuery->where('category_id', $categoryId);
                    });
            });
        }

        return $query->get();
    }

    public function customersDataTablePrint(CrmService $crmService, Request $request)
    {

        $summery = $crmService->getOrdersByFilter($request->city, $request->product, $request->categoryId, $request->date);

        return view('backend.crm.print', compact('summery'));
    }

    public function formatCustomersDataTable($summery)
    {
        $customerSummary = $summery['customer_summary'];

        $totalOrders = $summery['orders_summery']['total_orders'];
        $totalSale = $summery['orders_summery']['total_sale'];
        $totalDiscount = $summery['orders_summery']['total_discount'];
        $averageOrder = $totalSale / $totalOrders;

        return DataTables::of($customerSummary)
            ->addColumn('select', function ($customer) {
                return '<input type="checkbox" name="selected_customers[]" value="' . $customer['customer_id'] . '">';
            })
            ->addColumn('sl_no', function ($customer) {
                return $customer['customer_id'];
            })
            ->with([
                'sum_of_total_orders' => $totalOrders,
                'sum_of_purchase_amount' => single_price($totalSale),
                'sum_of_discount' => single_price($totalDiscount),
                'average_order_value' => single_price($averageOrder),
            ])
            ->rawColumns(['select'])
            ->make(true);
    }

    // public function formatCustomersDataTable($summery)
    // {
    //     $totalPurchaseAmount = 0;
    //     $totalDiscount = 0;
    //     $sum_of_total_orders = 0;

    //     foreach ($customers as $customer) {
    //         $customerOrders = $customer->getCustomerOrdersByDate($date)->where('payment_status', 'paid');
    //         $totalPurchaseAmount += $customerOrders->sum('grand_total');
    //         $totalDiscount += $customerOrders->sum('total_discount');
    //         $sum_of_total_orders += $customerOrders->count();
    //     }
    //     // dd($totalPurchaseAmount, $sum_of_total_orders);
    //     return DataTables::of($customers)
    //         ->addColumn('select', function ($customer) {
    //             return '<input type="checkbox" name="selected_customers[]" value="' . $customer->id . '">';
    //         })
    //         ->addColumn('sl_no', function ($customer) {
    //             return $customer->id;
    //         })
    //         ->addColumn('total_orders', function ($customer) use ($date) {
    //             return $customer->getCustomerOrdersByDate($date)->count();
    //         })
    //         ->addColumn('total_purchase_amount', function ($customer) use ($date) {
    //             return single_price($customer->getCustomerOrdersByDate($date)->sum('grand_total'));
    //         })
    //         ->addColumn('total_discount', function ($customer) use ($date) {
    //             return single_price($customer->getCustomerOrdersByDate($date)->sum('total_discount'));
    //         })
    //         ->with([
    //             'sum_of_purchase_amount' => single_price($totalPurchaseAmount),
    //             'sum_of_discount' => single_price($totalDiscount),
    //             'sum_of_total_orders' => $sum_of_total_orders,
    //         ])
    //         ->rawColumns(['select'])
    //         ->make(true);
    // }



}
