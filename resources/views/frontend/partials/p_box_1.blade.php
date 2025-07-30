<div class="aiz-card-box border rounded-bottom bg-white">
    <div class="position-relative">
        <a href="{{ route('product', $product->slug) }}" class="d-block">
            <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                data-src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ $product->getTranslation('name') }}"
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
        </a>
    </div>
    <div class="p-md-3 p-2 text-center">
        <h3 class="fw-600 fs-14 mb-1 text-truncate-2 lh-1-6 h-45px">
            <a href="{{ route('product', $product->slug) }}"
                class="d-block text-reset">{{ $product->getTranslation('name') }}</a>
        </h3>
        @if ($product->unit != null)
            <div class="opacity-70">{{ $product->getTranslation('unit') }}</div>
        @endif
        <div class="fs-15 mb-2">
            @if (home_base_price($product->id) != home_discounted_base_price($product->id))
                <del class="fw-600 opacity-50 mr-1">{{ home_base_price($product->id) }}</del>
            @endif
            <span class="fw-600 text-primary">{{ home_discounted_base_price($product->id) }}</span>
        </div>

        @php
            $qty = 0;
            if ($product->variant_product) {
                foreach ($product->stocks as $key => $stock) {
                    $qty += $stock->qty;
                }
            } else {
                $qty = $product->current_stock;
            }
        @endphp

        <div class="fs-15 mb-2">
            @if ($qty > 0)
                <span class="badge badge-md badge-inline badge-pill badge-success">{{ translate('In stock') }}</span>
            @else
                <span
                    class="badge badge-md badge-inline badge-pill badge-danger">{{ translate('Out of stock') }}</span>
            @endif
        </div>

        @if ($product->variant_product == 1)
            <button type="button" onclick="showAddToCartModal({{ $product->id }})"
                class="btn btn-sm fs-12 btn-primary text-dark text-uppercase rounded-pill fw-600">
                {{ translate('Add to Bag') }}
            </button>


            {{-- <a href="{{ route('product', $product->slug) }}" class="btn btn-sm fs-12 btn-primary text-dark text-uppercase rounded-pill fw-600">
                   {{ translate('Add to Bag') }}
            </a> --}}
        @else
            <!-- Hidden Form -->
            <form id="buyNowForm-{{ $product->id }}" action="{{ route('cart.addToCart2') }}" method="POST"
                style="display: none;">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
            </form>

            <!-- Button -->
            <a href="#"
                onclick="document.getElementById('buyNowForm-{{ $product->id }}').submit(); return false;"
                class="btn btn-sm fs-12 btn-primary text-dark text-uppercase rounded-pill fw-600">
                {{ translate('Buy Now') }}
            </a>
        @endif



    </div>
</div>
