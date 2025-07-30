@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Affiliates') }}</h1>
        </div>
    </div>




    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Assign Variable Products</h5>
            <div class="text-right">
                <a href="{{ route('affiliates.affiliators') }}" class="btn btn-dark btn-sm">
                    <i class="las la-arrow-left"></i> {{ translate('Back to Affiliators') }}
                </a>
            </div>
        </div>
        <div class="card-body d-flex">
            <div class="col-md-6 border p-3">
                <form action="{{ route('affiliates.affiliators.assign-products.store', $affiliator->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="variant">
                    <div id="padrent-div">
                        <h3>Assign Variable Products</h3>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="">Select Variant Product</label>
                                <select name="var_product_id" class="form-control" name="" id="select_var_product">
                                    <option value="">Select Variant Product Product</option>
                                    @foreach ($varientProducts as $key => $product)
                                        <option data-variants="{{ $product->choice_options }}" value="{{ $product->id }}">
                                            {{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div id="tab-var">

                        </div>
                    </div>
                    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> Save</button>
                </form>
            </div>
            <div class="col-md-6  border p-3">
                <form action="{{ route('affiliates.affiliators.assign-products.store', $affiliator->id) }}" method="POST">
                    @csrf
                       <input type="hidden" name="type" value="simple">
                    <div id="parent-div">
                        <h3>Assign Simple Product</h3>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Select Product</label>
                                    <select name="product_id[]" class="form-control" name="" id="">
                                        <option value="">Select Product</option>
                                        @foreach ($simpleProducts as $key => $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Commission (BDT)</label>
                                    <input type="number" class="form-control" name="commission[]" id=""
                                        placeholder="Commission" value="">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Discount </label>
                                    <input type="number" class="form-control" name="discount[]" id=""
                                        placeholder="Discount" value="">
                                </div>
                            </div>

                            <div class="col">
                                <a href="#" id="addRow" class="btn btn-primary mt-4">+ Add</a>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>





        </div>
    </div>




    <div class="card">
        <div class="card-header">
            <p>Assigned Products</p>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Product</th>
                        <th>Commission (BDT)</th>
                        <th>Discount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignedProducts as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                {{ $item->product->name ?? 'N/A' }}
                                @if ($item->has_variant)
                                    <small class="text-muted">({{ $item->variant_name }})</small>
                                @endif
                            </td>
                            <td>{{ number_format($item->commission, 2) }}</td>
                            <td>{{ number_format($item->discount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function approve_affiliators(id) {
            $('#approve-modal').modal('show', {
                backdrop: 'static'
            });
            $('#approve-modal').find('input[name="id"]').val(id);
        }




        const addRowButton = document.getElementById('addRow');



        //add new row
        addRowButton.addEventListener('click', function() {

            const parentDiv = document.getElementById('parent-div');

            const newRow = document.createElement('div');
            newRow.className = 'form-row';


            let html = `   <div class="col">
                <div class="form-group">
                    <label for="">Select Product</label>
                    <select class="form-control" name="product_id[]" id="">
                          <option value="">Select Product</option>
                       @foreach ($simpleProducts as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="">Commission (BDT)</label>
                    <input type="number" class="form-control" name="commission[]" id=""
                        placeholder="Commission" value="">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="">Discount </label>
                    <input type="number" class="form-control" name="discount[]" id=""
                        placeholder="Discount" value="">
                </div>
            </div>

            <div class="col">
                <a href="" class="btn btn-danger mt-4">-</a>
            </div>`;
            newRow.innerHTML = html;



            parentDiv.appendChild(newRow);


        });



        $(document).ready(function() {
            $('#select_var_product').change(function() {
                let prodId = $(this).val();

                var selectedOption = $(this).find('option:selected');
                var variations = selectedOption.data('variants'); // This will parse JSON if available



                let tr = "";


                $.each(variations[0].values, function(index, value) {
                    tr += `<tr>
                                <td>${value}</td>
                                <input type="hidden" name="name[]" value="${value}">
                                
                                <td>
                                    <input type="number" class="form-control" name="commission[]" placeholder="eg:300" id="">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="discount[]" placeholder="eg:200" id="">
                                </td>
                            </tr>`;
                });

                let table = `<table class="table">
                                    <thead>
                                        <tr>
                                        <th>Attribute</th>
                                        <th>Commission</th>
                                        <th>Discount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       ${tr}
                                    </tbody>
                               </table>`;


                $('#tab-var').html(table);


            });
        });
    </script>
@endsection
