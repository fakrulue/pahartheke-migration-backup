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
            <h1 class="h3">Sms History</h1>
        </div>
    </div>
</div>

<div class="row">
    <!-- Small Card -->
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
            <div class="card-header">Today Pending</div>
            <div class="card-body">
                <h5 class="card-title">{{ $todayPendingCount }}</h5>
            </div>
        </div>
    </div>

    <!-- Another Small Card -->
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3" style="max-width: 18rem;">
            <div class="card-header">Today Sent</div>
            <div class="card-body">
                <h5 class="card-title">{{ $todaySentCount }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3" style="max-width: 18rem;">
            <div class="card-header">Today Failed</div>
            <div class="card-body">
                <h5 class="card-title">{{ $todayFailedCount }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3" style="max-width: 18rem;">
            <div class="card-header">Total Job</div>
            <div class="card-body">
                <h5 class="card-title">{{ $jobCount }}</h5>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <a href="{{route('run.job')}}" class="btn btn-success">Run Job</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6"></h5>
        <div class="pull-right clearfix">
            <form id="sort_categories" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $history)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $history->phone_number }}</td>
                    <td>{{ $history->message }}</td>
                    <td>{{ $history->updated_at->format('d-m-Y') }}</td>
                    <td>{{ $history->updated_at->format('G:i:s') }}</td>
                    <td>
                        @if ($history->status === "sent")
                        <span class="badge-success btn-xs">{{ ucfirst($history->status) }}</span>
                        @elseif($history->status === "pending")
                        <span class="badge-warning btn-xs">{{ ucfirst($history->status) }}</span>
                        @else
                        <span class="badge-danger btn-xs">{{ ucfirst($history->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $history->response }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="aiz-pagination">
            {{ $histories->appends(request()->input())->links() }}
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
    $(document).ready(function() {

        $('#daterange').daterangepicker({
    timePicker: true, // Enable time picker
    timePicker24Hour: false, // Use 24-hour format
    timePickerSeconds: true, // Include seconds in time picker
    locale: {
        format: 'DD-MM-YYYY HH:mm:ss a' // Format for date and time
    }
}, function(start, end) {
    // Callback function when date range is applied
    onDateRangeChange(start, end);
});

// Custom function to handle the date range change
function onDateRangeChange(start, end) {
    let startTime = start.format('DD-MM-YYYY HH:mm:ss a');
    let endTime = end.format('DD-MM-YYYY HH:mm:ss a');
    
  
    
}

       



    });
</script>
@endpush