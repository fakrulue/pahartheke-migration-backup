@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Affiliates') }}</h1>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Commission Requests') }}</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th>SL</th>
                        <th>Date</th>
                        <th>Affiliator Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Commission </th>
                        <th>Order Payment Status</th>
                        <th>Order Status</th>
                        <th>Commission Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($affOrders as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                <td>{{ $item->affiliator->full_name }}</td>
                                <td>{{ $item->affiliator->email }}</td>
                                <td>{{ $item->affiliator->phone }}</td>
                                <td>{{ $item->commission_amount }} BDT</td>
                            
                                <td>
                                    @if ($item->order->payment_status == 'paid')
                                        <span style="width: 60px;height:25px" class="badge badge-success">Paid</span>
                                    @elseif($item->payment_status == 'unpaid')
                                        <span style="width: 60px;height:25px" class="badge badge-danger">Unpaid</span>
                                    @else
                                        <span style="width: 60px;height:25px" class="badge badge-warning">{{ $item->order->payment_status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="width: 60px;height:25px" class="badge badge-warning">{{ $item->order->orderDetails[0]->delivery_status }}</span>
                                </td>
                                <td>
                                    @if ($item->status == 0)
                                        <span style="width: 60px;height:25px" class="badge badge-danger">Pending</span>
                                    @elseif($item->status == 1)
                                        <span style="width: 60px;height:25px" class="badge badge-success">Accepted</span>
                                    @else
                                        <span style="width: 60px;height:25px" class="badge badge-warning">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('affiliates.commission-requests.update', $item->id) }}"
                                        class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="Accept">
                                        <i class="las la-check"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection



@section('script')
    <script type="text/javascript">
       
    </script>
@endsection
