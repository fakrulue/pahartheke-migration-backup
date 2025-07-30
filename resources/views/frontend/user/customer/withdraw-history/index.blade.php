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
                                <h1 class="h3">{{ translate('Withdraw History') }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="col text-center text-md-left">
                                <h5 class="mb-md-0 h6">{{ translate('Withdraw History') }}</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ translate('Amount')}}</th>
                                        <th>{{ translate('Date')}}</th>
                                        <th>{{ translate('Status')}}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($withdraws as $key => $withdraw)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $withdraw->amount }}</td>
                                        <td>{{ $withdraw->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            @if($withdraw->status === 'pending')
                                                <span class="badge badge-secondary p-3" style="width: auto;font-size: 14px">Pending</span>
                                            @elseif($withdraw->status === 'approve')
                                                <span class="badge badge-success p-3" style="width: auto;font-size: 14px">Approved</span>
                                            @else
                                                <span class="badge badge-danger p-3" style="width: auto;font-size: 14px">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="aiz-pagination">
                                {{ $withdraws->links() }}
                          	</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
