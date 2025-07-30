<div class="card border-0 shadow-sm rounded">
    <div class="card-header">
        <h3 class="fs-16 fw-600 mb-0">{{ translate('Summary') }}</h3>
        <div class="text-right">
            <span class="badge badge-inline badge-primary">{{ count(Session::get('cart')) }}
                {{ translate('Items') }}</span>
        </div>
    </div>

    <div class="card-body">
        @if (
            \App\Addon::where('unique_identifier', 'club_point')->first() != null &&
                \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
            @php
                $total_point = 0;
            @endphp
            @foreach (Session::get('cart') as $key => $cartItem)
                @php
                    $product = \App\Product::find($cartItem['id']);
                    $total_point += $product->earn_point * $cartItem['quantity'];
                @endphp
            @endforeach
            <div class="rounded px-2 mb-2 bg-soft-primary border-soft-primary border">
                {{ translate('Total Club point') }}:
                <span class="fw-700 float-right">{{ $total_point }}</span>
            </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th class="product-name">{{ translate('Product') }}</th>
                    <th class="product-total text-right">{{ translate('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $referral_code = session()->get('ref_code');
                @endphp
                @php
                    $affiliateDiscount = 0;
                    $subtotal = 0;
                    $tax = 0;
                    $shipping = 0;
                    $cart_categories = [];
                @endphp
                @foreach (Session::get('cart') as $key => $cartItem)
                    @php
                        $product = \App\Product::find($cartItem['id']);

                        $affiliator = \App\Affiliator::where('affiliator_code', $referral_code)->with('products')->first();

                        //dd($affiliator);

                        if ($affiliator != null) {
                            $affiliatorProducts = $affiliator->products()->get();

                            $affArr = [];

                            foreach ($affiliatorProducts as $affPro) {
                                $affArr['id'][] = $affPro->id;
                                $affArr['has_variant'][] = $affPro->pivot->has_variant;
                                $affArr['variant_name'][] = $affPro->pivot->variant_name;
                            }
                            //dd($check);
                            $check = collect($affArr['id'] ?? null)->contains($product->id) ?? false;


                            if ($check) {
                                $ids = $affArr['id'];
                                $count = array_count_values($ids)[$cartItem['id']] ?? 0;

                                if ($count > 0) {
                                    $cartVariant = $cartItem['variant'];
                                   
                                    $commission = $affiliator
                                        ->products()
                                        ->wherePivot('variant_name', 'like', '%' . $cartVariant . '%')
                                        ->where('product_id', $product->id)
                                        ->first()->pivot->discount;
                                        //dd($commission);

                                    $affiliateDiscount += $commission * $cartItem['quantity'];
                                } else {
                                    $commission = $affiliator->products()->where('product_id', $product->id)->first()
                                        ->pivot->commission;

                                    $affiliateDiscount += $commission * $cartItem['quantity'];
                                }
                            }

                            //dd($affiliatorProducts->toArray());
                        }

                        $subtotal += ($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity'];

                        $tax += $cartItem['tax'] * $cartItem['quantity'];

                        $product_name_with_choice = $product->getTranslation('name');

                        if ($cartItem['variant'] != null) {
                            $product_name_with_choice = $product->getTranslation('name') . ' - ' . $cartItem['variant'];
                        }

                        if (!in_array($product->category->id, $cart_categories)) {
                            array_push($cart_categories, $product->category->id);
                        }

                    @endphp
                    <tr class="cart_item">
                        <td class="product-name">
                            {{ $product_name_with_choice }}
                            <strong class="product-quantity">× {{ $cartItem['quantity'] }}</strong>
                        </td>
                        <td class="product-total text-right">
                            <span
                                class="pl-4">{{ single_price(($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity']) }}</span>
                        </td>
                    </tr>
                @endforeach
                @php
                    $shipping = 0;
                    if (Session::has('ec_shipping_info')) {
                        $shipping += Session::get('ec_shipping_info')->cost;
                    }
                    // $shipping = \App\Category::whereIn('id', $cart_categories)->pluck('shipping_cost')->max();
                @endphp

            </tbody>
        </table>

        <table class="table">

            <tfoot>
                <tr class="cart-subtotal">
                    <th>{{ translate('Subtotal') }}</th>
                    <td class="text-right">
                        <span class="fw-600">{{ single_price($subtotal) }}</span>
                    </td>
                </tr>

                <tr class="cart-shipping">
                    <th>{{ translate('Tax') }}</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($tax) }}</span>
                    </td>
                </tr>

                <tr class="cart-shipping">
                    <th>{{ translate('Shipping cost') }}</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($shipping) }}</span>
                    </td>
                </tr>

                @if (Session::has('coupon_discount'))
                    <tr class="cart-shipping">
                        <th>{{ translate('Coupon Discount') }}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price(Session::get('coupon_discount')) }}</span>
                        </td>
                    </tr>
                @endif

                @if (Session::has('total_rules_discount'))
                    <tr class="cart-shipping">
                        <th>{{ translate('Discount') }}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price(Session::get('total_rules_discount')) }}</span>
                        </td>
                    </tr>
                @endif




                @if ($referral_code != null)
                    <tr class="cart-shipping">
                        <th>{{ translate('Affiliate Discount') }}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price($affiliateDiscount) }}</span>
                        </td>
                    </tr>
                @endif

                @php

                    if ($affiliateDiscount > 0) {
                        $total = $subtotal + $tax + $shipping - $affiliateDiscount;
                    } else {
                        $total = $subtotal + $tax + $shipping;
                    }

                    if (Session::has('coupon_discount')) {
                        $total -= Session::get('coupon_discount');
                    }
                    if (Session::has('total_rules_discount')) {
                        $total -= Session::get('total_rules_discount');
                    }
                @endphp

                <tr class="cart-total">
                    <th><span class="strong-600">{{ translate('Total') }}</span></th>
                    <td class="text-right">
                        <strong><span>{{ single_price($total) }}</span></strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        @if (\App\BusinessSetting::where('type', 'coupon_system')->first()->value == 1)
            @if (Session::has('coupon_discount'))
                <div class="mt-3">
                    <form class="" action="{{ route('checkout.remove_coupon_code') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <div class="form-control">{{ \App\Coupon::find(Session::get('coupon_id'))->code }}</div>
                            <div class="input-group-append">
                                <button type="submit"
                                    class="btn btn-primary">{{ translate('Change Coupon') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-3">
                    <form class="" action="{{ route('checkout.apply_coupon_code') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" name="code"
                                placeholder="{{ translate('Have coupon code? Enter here') }}" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">{{ translate('Apply') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endif

    </div>
</div>
