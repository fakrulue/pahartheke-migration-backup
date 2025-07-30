@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Affiliates') }}</h1>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('SetEtings') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <form action="{{ route('affiliates.settings.post') }}" method="POST">
                    @csrf
                    <div class="col-md-8 d-flex">
                        <div class="form-group">
                            <label for="">Select Product</label>
                            <select name="product_id" class="form-control">
                                @foreach ($products as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ml-2">
                            <label for="">Commission</label>
                            <input type="number" name="commission" placeholder="0" class="form-control">
                        </div>
                        <div class="form-group ml-2">
                            <label for="">Discount</label>
                            <input type="number" name="discount" placeholder="0" class="form-control">
                        </div>
                        <div class="form-group ml-2" style="margin-top:27px">
                            <button type="submit" id="add_row" class="btn btn-primary">+</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th>SL</th>
                        <th>Product</th>
                        <th>Commission</th>
                        <th>Discount</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($AfProducts as $afProduct)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$afProduct->product->name}}</td>
                                <td>{{$afProduct->commission}}</td>
                                <td>{{$afProduct->discount}}</td>
                                <td>
                                    <a href="{{route('affiliates.settings.delete',$afProduct->id)}}" class="btn btn-danger">-</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#add_row').click(function() {
                console.log('ok');

            });
        });
    </script>
@endsection
