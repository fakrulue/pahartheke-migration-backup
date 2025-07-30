@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Affiliates') }}</h1>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Affiliators') }}</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th>SL</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Assigned Products</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($affiliators as $affiliator)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$affiliator->full_name}}</td>    
                                <td>{{$affiliator->email}}</td>
                                <td>{{$affiliator->phone}}</td>
                                <td>
                                    @if($affiliator->products->count() > 0)
                                        <span style="font-size: 12px;color:white; padding: 4px 8px;background: #007bff; border-radius: 4px;">
                                            {{translate('Assigned')}}:
                                            {{$affiliator->products->count()}} {{translate('Products')}}
                                        </span>
                                    @else
                                        {{translate('No Products Assigned')}}
                                    @endif
                                </td>
                                <td>
                                    @if($affiliator->status == 'active')
                                        <span style="font-size: 12px;color:white; padding: 4px 8px;background: green; border-radius: 4px;">Approved</span>
                                    @elseif($affiliator->status == 'inactive')
                                        <span style="font-size: 12px;color:white; padding: 4px 8px;background: #007bff; border-radius: 4px;">Inactive</span>
                                    @elseif($affiliator->status == 'rejected')
                                        <span style="font-size: 12px;color:white; padding: 4px 8px;background: red; border-radius: 4px;">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                  
                                    <a href="{{route('affiliates.affiliators.view',$affiliator->id)}}" class="btn btn-success btn-sm">
                                        <i class="las la-eye"></i>  
                                    </a>
                                    @if($affiliator->status == 'active')
                                        <a href="{{route('affiliates.affiliators.reject',$affiliator->id)}}" class="btn btn-danger btn-sm">
                                            <i class="las la-times"></i>
                                        </a>
                                    @endif
                                    @if($affiliator->status == 'active')
                                    <a href="{{route('affiliates.affiliators.assign-products',$affiliator->id)}}" class="btn btn-warning btn-sm">
                                        <i class="las la-plus"></i>
                                    </a>
                                @endif
                                @if($affiliator->status == 'active')
                                    <a href="{{route('affiliates.affiliators.orders', $affiliator->id)}}" class="btn bg-secondary btn-sm">
                                        <i class="las la-clipboard-list"></i>
                                    </a>
                                @endif
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
