<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\SalesExport;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\AffiliateController;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\ProductStock;
use App\Color;
use App\CouponUsage;
use App\OtpConfiguration;
use App\User;
use App\BusinessSetting;
use App\CommissionHistory;
use App\DeliveryMan;
use Auth;
use Session;
use DB;
use PDF;
use Mail;
use App\Mail\InvoiceEmailManager;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_status != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
            
        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = \App\Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }
     return view('frontend.user.seller.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search'));
    }

    // All Orders
    public function all_orders(Request $request)
    {
        //dd(Carbon::now());
        $payment_status = null;
        $delivery_status = null;
        $delivery_man_id = null;
        $date = $request->date;

     
        $sort_search = null;
        $orders = Order::orderByRaw('CASE WHEN orders.pos_order = 1 THEN orders.created_at ELSE orders.updated_at END DESC');
        // $orders->orderByRaw('CASE WHEN pos_order = 1 THEN  ELSE updated_at END DESC');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('shipping_address', 'like', '%' . $sort_search . '%');
        }
    
        if ($date != null) {
            $orders = $orders->where(function($query) use ($date) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))
                      ->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])))
                      ->orWhere(function($query) use ($date) {
                          $query->where('pos_order', 0)
                                ->whereDate('updated_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))
                                ->whereDate('updated_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
                      });
            });

            //dd($orders);
        }

        if ($request->payment_type != null) {
            $orders = $orders->whereHas('orderDetails', function ($query) use ($request){
                $query->where('payment_status', $request->payment_type);
            });
            $payment_status = $request->payment_type;
        }
    
        if ($request->delivery_status != null) {
            $orders = $orders->whereHas('orderDetails', function ($query) use ($request){
                $query->where('delivery_status', $request->delivery_status);
            });
            $delivery_status = $request->delivery_status;
        }

        if ($request->delivery_man_id != null) {
            $orders = $orders->where('delivery_man_id', $request->delivery_man_id);;
            $delivery_man_id = $request->delivery_man_id;
        }
    
        $dateName = $date ? $date : Carbon::now();
    
        $data = [
            'customer' => $orders->pluck('user_id')->unique()->count(),
            'orders' => $orders->count(),
            'purchases' => single_price($orders->sum('grand_total')),
            'discounts' => single_price($orders->sum('total_discount')),
        ];
    
        if ($request->has('export')) {
            $orders = $orders->latest()->get();
            return view('backend.sales.all_orders.print', compact('data','orders'));
        }
        $deliverymen = DeliveryMan::all();
    
        $orders = $date ? $orders->get() : $orders->paginate(15);
        $paginate = $date ? false : true;
        return view('backend.sales.all_orders.index', compact('data','orders', 'sort_search','delivery_man_id', 'date','paginate','delivery_status','payment_status','deliverymen'));
    }
    
    

    public function all_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $delivery_charge = \DB::table('order_details')->where('order_id', decrypt($id))->first('shipping_cost');
        return view('backend.sales.all_orders.show', compact('order', 'delivery_charge'));
    }
    public function all_ordersupdate_price(Request $request, $id)
    {
        $discount = $request->discount ? $request->discount : 0;
        $old_discount = $request->old_discount ? $request->old_discount : 0;


        $order_details = OrderDetail::findOrFail($request->orderDetailsID);
        $order_details->price = $request->price * $request->quantity;
        $order_details->discount = $discount; //*$request->quantity;
        $order_details->quantity = $request->quantity;
        $order_details->update();
        $total = $request->price * $request->quantity;
        $order = Order::findOrFail($id);


        $new_discount = $discount - $old_discount;

        $dd = $request->old_total - $request->old_price;
        $grand_total = $dd + $total;
        $order->grand_total = $grand_total;
        $order->total_discount = $new_discount;
        $order->update();

        flash(translate('Customer has been updated successfully'))->success();
        return redirect()->back();
        // return redirect()->route('inhouse_orders.index');
    }

    public function all_ordersupdate_price_2(Request $request, $id)
    {

        $shipping_cost = $request->shipping_cost ? $request->shipping_cost : 0;
        //dd($request->all()); 

        $grand_total = 0;
        $total_discount = 0;
        foreach ($request->order_details_id as $key => $order_item) {
            $prod_price = $request->prod_price[$key] * $request->quantity[$key];
            $discount_type = $request->discount_type[$key];

            $discount = $request->discount[$key] ? $request->discount[$key] : 0;

            if ($discount_type == 'percent') {
                //$prod_price -= ( $prod_price * $discount ) / 100;
                $total_discount += (($request->prod_price[$key] * $request->quantity[$key]) * $discount) / 100;
                $discount_amount = (($request->prod_price[$key] * $request->quantity[$key]) * $discount) / 100;
            } elseif ($discount_type == 'amount') {
                //$prod_price -= $discount;
                $total_discount += $discount;
                $discount_amount = $discount;
            } else {
                //$prod_price -= 0;
                $total_discount += $discount;
                $discount_amount = $discount;
            }


            $order_details = OrderDetail::findOrFail($order_item);
            $order_details->price = $prod_price;
            $order_details->discount = $discount_amount;
            $order_details->discount_percent = ($discount_type == 'percent') ? $discount : 0;
            $order_details->discount_type = $discount_type;
            $order_details->quantity = $request->quantity[$key];
            $order_details->shipping_cost = $shipping_cost;
            $order_details->update();
            $grand_total += $prod_price;
        }
        $coupon_discount = $request->coupon_discount ? $request->coupon_discount : 0;
        $advance_payment = $request->advance_payment ? $request->advance_payment : 0;
        $previous_due_payment = $request->previous_due_payment ? $request->previous_due_payment : 0;
        $order = Order::findOrFail($id);
        $order->coupon_discount = $coupon_discount;
        $order->advance_payment = $advance_payment;
        $order->order_note = $request->order_note;
        $order->previous_due_payment = $previous_due_payment;
        $order->grand_total = ($grand_total + $shipping_cost) - $coupon_discount;
        $order->total_discount = $total_discount;
        $order->update();



        flash(translate('Customer has been updated successfully'))->success();
        return redirect()->back();
        // return redirect()->route('inhouse_orders.index');
    }


    // Inhouse Orders
    public function admin_orders(Request $request)
    {

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->where('pos_order', 0)
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', $admin_user_id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('shipping_address', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('orders.updated_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.updated_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.inhouse_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();

        $delivery_charge = \DB::table('order_details')->where('order_id', decrypt($id))->first('shipping_cost');
        return view('backend.sales.inhouse_orders.show', compact('order', 'delivery_charge'));
    }

    public function all_orders_edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order_details = OrderDetail::where('order_id', $id)->get();
        return view('backend.sales.all_orders.edit', compact('order', 'order_details'));
    }

    public function all_orders_update(Request $request, Order $order)
    {
        $table = Order::findOrFail($request->id);
        $data[] = array(
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        );

        $shipping_address = json_encode($data);

        $table->update(['shipping_address' => $shipping_address]);

        flash(translate('Addrress has been updated successfully'))->success();
        return redirect()->route('inhouse_orders.index');

    }

    public function pos_orders(Request $request)
    {
        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->where('pos_order', 1)
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.pos_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'date'));
    }

    // Seller Orders
    public function seller_orders(Request $request)
    {

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $seller_id = $request->seller_id;

        $admin_user_id = User::where('user_type', 'admin')->first()->id;

        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', '!=', $admin_user_id);

        if ($seller_id != null) {
            $orders = $orders->where('order_details.seller_id', '=', $seller_id);
        }

        $orders = $orders->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('orders.updated_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.updated_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.seller_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date', 'seller_id'));
    }

    public function seller_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        $delivery_charge = \DB::table('order_details')->where('order_id', decrypt($id))->first('shipping_cost');
        return view('backend.sales.seller_orders.show', compact('order', 'delivery_charge'));
    }


    // Pickup point orders
    public function pickup_point_order_index(Request $request)
    {
        $date = $request->date;
        $sort_search = null;

        if (Auth::user()->user_type == 'staff' && Auth::user()->staff->pick_up_point != null) {
            //$orders = Order::where('pickup_point_id', Auth::user()->staff->pick_up_point->id)->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.pickup_point_id', Auth::user()->staff->pick_up_point->id)
                ->select('orders.id')
                ->distinct();

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.updated_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.updated_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders'));
        } else {
            //$orders = Order::where('shipping_type', 'Pick-up Point')->get();
            $orders = DB::table('orders')
                ->orderBy('code', 'desc')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.shipping_type', 'pickup_point')
                ->select('orders.id')
                ->distinct();

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.updated_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.updated_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders', 'sort_search', 'date'));
        }
    }

    public function pickup_point_order_sales_show($id)
    {
        if (Auth::user()->user_type == 'staff') {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        } else {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        }
    }

    /**
     * Display a single sale to admin.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = new Order;
        if (Auth::check()) {
            $order->user_id = Auth::user()->id;
        } else {
            $order->guest_id = mt_rand(100000, 999999);
        }

        $order->shipping_address = json_encode($request->session()->get('shipping_info'));

        $order->payment_type = $request->payment_option;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->date = strtotime('now');

        if ($order->save()) {
            $subtotal = 0;
            $tax = 0;
            $total_discount = 0;
            $shipping = 0;

            //calculate shipping is to get shipping costs of different types
            $admin_products = array();
            $seller_products = array();

            //Order Details Storing
            $count_ = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
                $product = Product::find($cartItem['id']);

                if ($product->added_by == 'admin') {
                    array_push($admin_products, $cartItem['id']);
                } else {
                    $product_ids = array();
                    if (array_key_exists($product->user_id, $seller_products)) {
                        $product_ids = $seller_products[$product->user_id];
                    }
                    array_push($product_ids, $cartItem['id']);
                    $seller_products[$product->user_id] = $product_ids;
                }

                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $total_discount += $cartItem['discount'] * $cartItem['quantity'];

                $product_variation = $cartItem['variant'];

                if ($product_variation != null) {
                    $product_stock = $product->stocks->where('variant', $product_variation)->first();
                    if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }
                } else {
                    if ($product->digital != 1 && $cartItem['quantity'] > $product->current_stock) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product->current_stock -= $cartItem['quantity'];
                        $product->save();
                    }
                }

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                $order_detail->discount = $cartItem['discount'] * $cartItem['quantity'];
                $order_detail->profit = $cartItem['price'] * $cartItem['quantity'] - $cartItem['purchase_price'] * $cartItem['quantity'];
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                $order_detail->shipping_type = 'home_delivery';
                $order_detail->product_referral_code = $cartItem['product_referral_code'];

                //Dividing Shipping Costs
                if ($order_detail->shipping_type == 'home_delivery') {
                    $order_detail->shipping_cost = getShippingCost($key);
                } else {
                    $order_detail->shipping_cost = 0;
                }

                if ($count_ == 0) {
                    $shipping = $order_detail->shipping_cost;
                }
                $count_++;


                //End of storing shipping cost

                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();

                $product->num_of_sale++;
                $product->save();
            }

            $order->grand_total = $subtotal + $tax + $shipping;

            if (Session::has('coupon_discount')) {
                $order->grand_total -= Session::get('coupon_discount');
                $order->coupon_discount = Session::get('coupon_discount');

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Session::get('coupon_id');
                $coupon_usage->save();
            }

            $order->total_discount = $total_discount;

            $order->save();

            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
            $array['from'] = env('MAIL_USERNAME');
            $array['order'] = $order;

            foreach ($seller_products as $key => $seller_product) {
                try {
                    Mail::to(\App\User::find($key)->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {

                }
            }

            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
                try {
                    $otpController = new OTPVerificationController;
                    $otpController->send_order_code($order);
                } catch (\Exception $e) {

                }
            }

            //sends email to customer with the invoice pdf attached
            if (env('MAIL_USERNAME') != null) {
                try {
                    Mail::to($request->session()->get('shipping_info')['email'])->queue(new InvoiceEmailManager($array));
                    Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {

                }
            }

            $request->session()->put('order_id', $order->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $table = Order::findOrFail($request->id);
        $id = $request->id;
        $data = array(
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => 'Bangladesh',
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'checkout_type' => $request->checkout_type

        );

        $shipping_address = json_encode($data);

        Order::where(function ($query) use ($id, $shipping_address) {
            $query->where('id', $id);
        })->update(['shipping_address' => $shipping_address]);


        // $table = $shipping_address;
        // $table->update();

        flash(translate('Addrress has been updated successfully'))->success();
        return redirect()->route('all_orders.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {
                    if ($orderDetail->variation != null) {
                        $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                        if ($product_stock != null) {
                            $product_stock->qty += $orderDetail->quantity;
                            $product_stock->save();
                        }
                    } else {
                        $product = $orderDetail->product;
                        $product->current_stock += $orderDetail->quantity;
                        $product->save();
                    }
                } catch (\Exception $e) {

                }

                $orderDetail->delete();
            }
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {
                    if ($orderDetail->variation != null) {
                        $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                        if ($product_stock != null) {
                            $product_stock->qty += $orderDetail->quantity;
                            $product_stock->save();
                        }
                    } else {
                        $product = $orderDetail->product;
                        $product->current_stock += $orderDetail->quantity;
                        $product->save();
                    }
                } catch (\Exception $e) {

                }

                //$orderDetail->delete();
            }
            $order->cancelled = 1;
            $order->save();

            flash(translate('Order has been cancel successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('frontend.user.seller.order_details_seller', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->save();
        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();
            }
        }

        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_delivery_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_delivery_status($order);
            } catch (\Exception $e) {
            }
        }

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status == 'unpaid') {
                $status = 'unpaid';
            } elseif ($orderDetail->payment_status == 'advance') {
                $status = 'advance';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            if ($order->payment_type == 'cash_on_delivery') {
                if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                    $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                    foreach ($order->orderDetails as $key => $orderDetail) {
                        $orderDetail->payment_status = 'paid';
                        $orderDetail->save();
                        if ($orderDetail->product->user->user_type == 'seller') {
                            $seller = $orderDetail->product->user->seller;
                            $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                            $seller->save();


                            $commission_history = new CommissionHistory;
                            $commission_history->order_id = $order->id;
                            $commission_history->order_detail_id = $orderDetail->id;
                            $commission_history->seller_id = $orderDetail->seller_id;
                            $commission_history->seller_earning = ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                            $commission_history->admin_commission = ($orderDetail->price * $commission_percentage) / 100 + $orderDetail->shipping_cost;
                            $commission_history->save();
                        }
                    }
                } else {
                    foreach ($order->orderDetails as $key => $orderDetail) {
                        $orderDetail->payment_status = 'paid';
                        $orderDetail->save();
                        if ($orderDetail->product->user->user_type == 'seller') {
                            $commission_percentage = $orderDetail->product->category->commision_rate;
                            $seller = $orderDetail->product->user->seller;
                            $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                            $seller->save();

                            $commission_history = new CommissionHistory;
                            $commission_history->order_id = $order->id;
                            $commission_history->order_detail_id = $orderDetail->id;
                            $commission_history->seller_id = $orderDetail->seller_id;
                            $commission_history->seller_earning = ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax;
                            $commission_history->admin_commission = ($orderDetail->price * $commission_percentage) / 100 + $orderDetail->shipping_cost;
                            $commission_history->save();
                        }
                    }
                }
            }

            if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliatePoints($order);
            }

            if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
                if ($order->user != null) {
                    $clubpointController = new ClubPointController;
                    $clubpointController->processClubPoints($order);
                }
            }

            $order->commission_calculated = 1;
            $order->save();
        }

        if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\OtpConfiguration::where('type', 'otp_for_paid_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_payment_status($order);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }
}
