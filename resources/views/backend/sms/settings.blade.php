@extends('backend.layouts.app')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
@endpush

@section('content')
<style>
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
</style>

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">Sms Settings</h1>
        </div>

    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">Select a date range 
        @if($record->status == 0)    
        <div class="badge badge-danger">Off</div>
        @else
        <div class="badge badge-success">On</div>

        @endif
    
    </h5>
        @if($record->status == 0)
            <a href="{{route('sms-settings.status')}}" class="btn btn-success">Turn On</a>
        @else
            <a href="{{route('sms-settings.status')}}" class="btn btn-danger">Turn Off</a>
        @endif
    </div>
    <div class="card-body">
        <div class="container mt-5">
            <form action="{{route('sms-settings.store')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="daterange">Select Date Range with Time:</label>
                    <input type="text" name="daterange" id="daterange" class="form-control" />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
         </div>
    </div>
</div>
@endsection

@push('js')
<!-- Moment.js -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- DateRangePicker JS -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function () {

      // Laravel variables
      const startDate = "{{ $startDate }}";
      const endDate = "{{ $endDate }}";


        $('#daterange').daterangepicker({
            timePicker: true, // Enable time picker
            timePicker24Hour: true, // Use 24-hour format
            timePickerSeconds: true, // Include seconds in time picker
            locale: {
            format: 'YYYY-MM-DD HH:mm:ss' // Format for date and time
            },
            startDate: moment(startDate, 'YYYY-MM-DD HH:mm:ss'),
            endDate: moment(endDate, 'YYYY-MM-DD HH:mm:ss'),
        });
});

</script>
@endpush
