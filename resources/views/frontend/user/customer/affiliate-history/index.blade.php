@extends('frontend.layouts.app')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">
                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3">{{ translate('Affiliate Dashboard') }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row gutters-10">
                        @if ($affiliator->wallet)
                            <div class="col-md-12">
                                <h3>Your Affiliate Link</h3>
                                <!-- when project live then comment this  -->
                                <!-- <p>http://127.0.0.1:8000/?ref={{ $affiliator->affiliator_code }}</p> -->

                                <!-- when project live then uncomment this  -->
                                <div class="d-flex justify-content-between">
                                    <p>https://{{ request()->getHost() }}/?ref={{ $affiliator->affiliator_code }}</p>

                                    <form action="{{ route('withdraw.store') }}" method="POST" class="mb-2">
                                        @csrf
                                        <input type="number" step="any" class="d-none" name="amount"
                                            value="{{ $affiliator->commission }}">
                                        <button class="btn btn-sm btn-primary">Withdraw</button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 shadow-lg" style="background: #e6f9ec; border-left: 6px solid #28a745;">
                                    <div class="card-body">
                                        <h5 class="card-title text-success fw-bold">
                                            <i class="las la-wallet me-2"></i> Your Wallet Balance
                                        </h5>
                                        <p class="card-text fs-4 text-dark">
                                            {{ $affiliator->wallet->balance ?? 'N/A' }} <span class="text-muted fs-6">৳</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-lg" style="background: #e6f9ec; border-left: 6px solid #28a745;">
                                    <div class="card-body">
                                        <h5 class="card-title text-success fw-bold">
                                            <i class="las la-mouse-pointer me-2"></i> Your Link Click
                                        </h5>
                                        <p class="card-text fs-4 text-dark">
                                            {{ $affiliator->clicks ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="card border-0 shadow-lg">
                                    <div class="card-header text-white fw-bold" style="background: #28a745;">
                                        <i class="las la-clipboard-list me-2"></i> Your Affiliate Orders
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>Commission</th>
                                                        <th>Order Amount</th>
                                                        <th>Order Date</th>
                                                        <th>Order Status</th>
                                                        <th>Payment Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($affiliator->affiliateOrders as $order)
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-decoration-none text-success fw-semibold">
                                                                    {{ $order->order->code }}
                                                                </a>
                                                            </td>
                                                            <td>{{ single_price($order->commission_amount) }}</td>
                                                            <td>{{ single_price($order->order->grand_total) }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($order->created_at)) }}</td>
                                                            <td>
                                                                @if ($order->status == 'pending')
                                                                    <span class="badge bg-danger">Pending</span>
                                                                @else
                                                                    <span class="badge bg-success">Completed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($order->order->payment_status == 'paid')
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif($order->order->payment_status == 'unpaid')
                                                                    <span class="badge bg-danger">Unpaid</span>
                                                                @else
                                                                    <span class="badge bg-primary">{{ ucfirst($order->order->payment_status) }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @else
                            <div class="col-md-12 d-flex justify-content-center align-items-center flex-column">
                                <a href="{{ route('affiliate_history.create_user') }}" class="btn btn-primary ">Create
                                    Affiliator Wallet</a>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
