@extends('backend.layouts.app')
@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Affiliates') }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Orders') }}</h5>
            <div class="text-right
                <a href="{{ route('affiliates.affiliators') }}"
                class="btn btn-dark btn-sm">
                <i class="las la-arrow-left"></i> {{ translate('Back to Affiliators') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th>SL</th>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Customer Email</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->order->code }}</td>
                                <td>{{ $order->order->user->name }}</td>
                                <td>{{ $order->order->user->email ?? "N/A"}}</td>
                                <td>{{ $order->order->grand_total }} BDT</td>
                                <td>{{ $order->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>




  
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#affiliatorTable').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endsection
