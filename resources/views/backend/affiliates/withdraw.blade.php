@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Withdraw') }}</h1>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Withdraws') }}</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th>SL</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($withdraws as $withdraw)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$withdraw->affiliator->name}}</td>
                                <td>{{$withdraw->affiliator->email}}</td>
                                <td>{{$withdraw->affiliator->phone}}</td>
                                <td>{{$withdraw->amount}}</td>
                                <td>{{$withdraw->status}}</td>
                                <td>
                                    <div class="d-flex">
                                        @if($withdraw->status === 'rejected' || $withdraw->status === 'pending')
                                            <form action="{{route('withdraw.status')}}" method="post">
                                                @csrf
                                                <input type="hidden" value="{{$withdraw->id}}" name="withdraw_id">
                                                <input type="hidden" value="approve" name="status">
                                                <button class="btn btn-sm btn-primary" style="color: white">Approve</button>
                                            </form>
                                        @endif

                                        @if(($withdraw->status === 'approve' || $withdraw->status === 'pending') && $withdraw->status !== 'approve')
                                            <form action="{{route('withdraw.status')}}" class="ml-2" method="post">
                                                @csrf
                                                <input type="hidden" value="{{$withdraw->id}}" name="withdraw_id">
                                                <input type="hidden" value="rejected" name="status">
                                                <button class="btn btn-sm btn-danger" style="color: white">Reject</button>
                                            </form>
                                        @endif
                                    </div>
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

@endsection
