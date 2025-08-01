<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\SubSubCategory;
use App\Category;
use Session;
use App\Color;
use Cookie;
use Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        //dd($cart->all());
        $categories = Category::all();
        return view('frontend.view_cart', compact('categories'));
    }

    public function showCartModal(Request $request)
    {
        $product = Product::find($request->id);
        //dd($product);
        return view('frontend.partials.addToCart', compact('product'));
    }

    public function updateNavCart(Request $request)
    {
        $total = 0;
        $count = 0;

        if (Session::has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function ($object, $key) use ($request) {
                if ($key == $request->key) {
                    $product = \App\Product::find($object['id']);
                    if ($object['variant'] != null && $product->variant_product) {
                        $product_stock = $product->stocks->where('variant', $object['variant'])->first();
                        $quantity = $product_stock->qty;
                        if ($quantity >= $request->quantity) {
                            if ($request->quantity >= $product->min_qty) {
                                $object['quantity'] = $request->quantity;
                            }
                        }
                    } elseif ($product->current_stock >= $request->quantity) {
                        if ($request->quantity >= $product->min_qty) {
                            $object['quantity'] = $request->quantity;
                        }
                    }
                }
                return $object;
            });
            $request->session()->put('cart', $cart);

            $count = count($cart = Session::get('cart'));
            if ($count > 0) {
                foreach ($cart as $key => $cartItem) {
                    $total = $total + $cartItem['price'] * $cartItem['quantity'];
                }
            }
        }
        return array('total' => $total, 'count' => $count, 'view' => view('frontend.partials.sidebar_cart')->render());
        // return view('frontend.partials.sidebar_cart');
    }

    public function addToCart(Request $request)
    {
        //dd($request->all());
        $product = Product::find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $data['owner_id'] = $product->user_id;
        $str = '';
        $tax = 0;

        // if($product->digital != 1 && $request->quantity < $product->min_qty) {
        //     return array('status' => 0, 'view' => view('frontend.partials.minQtyNotSatisfied', [
        //         'min_qty' => $product->min_qty
        //     ])->render());
        // }


        //check the color enabled or disabled for the product
        if ($request->has('color')) {
            $data['color'] = $request['color'];
            $str = Color::where('code', $request['color'])->first()->name;
        }

        if ($product->digital != 1) {
            //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
            foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }

        $data['variant'] = $str;

        if ($str != null && $product->variant_product) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product_stock->price;
            $purchase_price = $product_stock->purchase_price;
            $quantity = $product_stock->qty;

            if ($quantity < $request['quantity']) {
                return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
            }
        } else {
            if ((int)$product->current_stock < $request['quantity']) {
                return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
            }
            $price = $product->unit_price;
            $purchase_price = $product->purchase_price;
        }

        $orginal_price = $price;
        $discount_price = 0;

        //discount calculation based on flash deal and regular discount
        //calculation of taxes
        $flash_deals = \App\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1  && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                    $discount_price = ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                    $discount_price = $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
                $discount_price = ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
                $discount_price = $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $tax = ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $tax = $product->tax;
        }


        $data['quantity'] = $request['quantity'];
        $data['price'] = $orginal_price;
        $data['discount'] = $discount_price;
        $data['purchase_price'] = $purchase_price;
        $data['tax'] = $tax;
        $data['shipping'] = 0;
        $data['product_referral_code'] = null;
        $data['digital'] = $product->digital;

        if ($request['quantity'] == null) {
            $data['quantity'] = 1;
        }

        if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
            $data['product_referral_code'] = Cookie::get('product_referral_code');
        }

        if ($request->session()->has('cart')) {
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('cart') as $key => $cartItem) {
                if ($cartItem['id'] == (int)$request->id) {
                    if ($cartItem['variant'] == $str && $str != null) {
                        $product_stock = $product->stocks->where('variant', $str)->first();
                        $quantity = $product_stock->qty;

                        if ($quantity < $cartItem['quantity'] + $request['quantity']) {
                            return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
                        } else {
                            $foundInCart = true;
                            $cartItem['quantity'] += $request['quantity'];
                        }
                    } else {
                        //dd((int)$product->current_stock,$cartItem['quantity'] + $request['quantity']);
                        if ((int)$product->current_stock < $cartItem['quantity'] + $request['quantity']) {
                            return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
                        } else {
                            $foundInCart = true;
                            $cartItem['quantity'] += $request['quantity'];
                        }
                    }
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('cart', $cart);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        return array('status' => 1, 'view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render());
    }
    public function addToCart2(Request $request)
    {
        //dd($request->all());
        $product = Product::find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $data['owner_id'] = $product->user_id;
        $str = '';
        $tax = 0;

        // if($product->digital != 1 && $request->quantity < $product->min_qty) {
        //     return array('status' => 0, 'view' => view('frontend.partials.minQtyNotSatisfied', [
        //         'min_qty' => $product->min_qty
        //     ])->render());
        // }


        //check the color enabled or disabled for the product
        if ($request->has('color')) {
            $data['color'] = $request['color'];
            $str = Color::where('code', $request['color'])->first()->name;
        }

        if ($product->digital != 1) {
            //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
            foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }

        $data['variant'] = $str;

        if ($str != null && $product->variant_product) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product_stock->price;
            $purchase_price = $product_stock->purchase_price;
            $quantity = $product_stock->qty;

            if ($quantity < $request['quantity']) {
                return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
            }
        } else {
            if ((int)$product->current_stock < $request['quantity']) {
                return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
            }
            $price = $product->unit_price;
            $purchase_price = $product->purchase_price;
        }

        $orginal_price = $price;
        $discount_price = 0;

        //discount calculation based on flash deal and regular discount
        //calculation of taxes
        $flash_deals = \App\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1  && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                    $discount_price = ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                    $discount_price = $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
                $discount_price = ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
                $discount_price = $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $tax = ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $tax = $product->tax;
        }


        $data['quantity'] = $request['quantity'];
        $data['price'] = $orginal_price;
        $data['discount'] = $discount_price;
        $data['purchase_price'] = $purchase_price;
        $data['tax'] = $tax;
        $data['shipping'] = 0;
        $data['product_referral_code'] = null;
        $data['digital'] = $product->digital;

        if ($request['quantity'] == null) {
            $data['quantity'] = 1;
        }

        if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
            $data['product_referral_code'] = Cookie::get('product_referral_code');
        }

        if ($request->session()->has('cart')) {
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('cart') as $key => $cartItem) {
                if ($cartItem['id'] == (int)$request->id) {
                    if ($cartItem['variant'] == $str && $str != null) {
                        $product_stock = $product->stocks->where('variant', $str)->first();
                        $quantity = $product_stock->qty;

                        if ($quantity < $cartItem['quantity'] + $request['quantity']) {
                            return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
                        } else {
                            $foundInCart = true;
                            $cartItem['quantity'] += $request['quantity'];
                        }
                    } else {
                        //dd((int)$product->current_stock,$cartItem['quantity'] + $request['quantity']);
                        if ((int)$product->current_stock < $cartItem['quantity'] + $request['quantity']) {
                            return array('status' => 0, 'view' => view('frontend.partials.outOfStockCart')->render());
                        } else {
                            $foundInCart = true;
                            $cartItem['quantity'] += $request['quantity'];
                        }
                    }
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('cart', $cart);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

       return redirect()->route('checkout.easy_checkout');



        //return redirect()->route('checkout.easy_checkout');

        //return view('frontend.easy_checkout', compact('product','user', 'data'));
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        return view('frontend.partials.cart_details');
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $product = \App\Product::find($object['id']);
                if ($object['variant'] != null && $product->variant_product) {
                    $product_stock = $product->stocks->where('variant', $object['variant'])->first();
                    $quantity = $product_stock->qty;
                    if ($quantity >= $request->quantity) {
                        if ($request->quantity >= $product->min_qty) {
                            $object['quantity'] = $request->quantity;
                        }
                    }
                } elseif ($product->current_stock >= $request->quantity) {
                    if ($request->quantity >= $product->min_qty) {
                        $object['quantity'] = $request->quantity;
                    }
                }
            }
            return $object;
        });
        $request->session()->put('cart', $cart);

        return view('frontend.partials.cart_details');
    }
}
