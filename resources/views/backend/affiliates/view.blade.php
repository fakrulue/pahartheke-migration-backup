@extends('backend.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Affiliates') }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Affiliators') }}</h5>
            <div class="text-right">
                <a href="{{ route('affiliates.affiliators') }}" class="btn btn-dark btn-sm">
                    <i class="las la-arrow-left"></i> {{ translate('Back to Affiliators') }}
                </a>
            </div>
        </div>
        <div class="card-body">
        <div class="row">
    {{-- Profile Card --}}
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center shadow-sm">
            <div class="card-body">
                <img src="https://png.pngtree.com/png-clipart/20241110/original/pngtree-cartoon-man-character-clipart-png-image_16886683.png"
                     alt="{{ $affiliator->full_name }}"
                     class="rounded-circle mb-3 shadow"
                     width="100" height="100">
                <h5 class="card-title mb-0">{{ $affiliator->full_name }}</h5>
                <small class="text-muted">{{ translate('Joined on') }}: {{ $affiliator->created_at->format('d M, Y') }}</small>
                
                <div class="mt-3">
                    <p><i class="las la-phone"></i> {{ $affiliator->phone }}</p>
                    <p><i class="las la-envelope"></i> {{ $affiliator->email }}</p>
                </div>

                <div class="d-flex justify-content-center gap-2 mt-3">
                    @if ($affiliator->status == 'active')
                        <span class=" btn btn-outline-success p-2">{{ translate('Approved') }}</span>
                    @else
                        <a href="{{ route('affiliates.affiliators.approve', $affiliator->id) }}"
                           class="btn btn-outline-success btn-sm">
                            <i class="las la-check-circle"></i> {{ translate('Approve') }}
                        </a>
                    @endif
                </div>

                <ul class="list-group list-group-flush mt-4">
                    @foreach($affiliator->socialLinks as $social)
                        <li class="list-group-item">
                            <a href="{{ $social->url }}" target="_blank">
                                <i class="lab la-{{ $social->platform }}"></i> {{ ucfirst($social->platform) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Tabbed Info Section --}}
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header  text-white" style="background-color:#b0cf3c;">
                <h6 class="mb-0">{{ translate('Affiliator Additional Details') }}</h6>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="affiliatorTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basic" type="button">Basic</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#address" type="button">Address</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank" type="button">Bank</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mobile" type="button">Mobile</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nominee" type="button">Nominee</button>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- Basic Info --}}
                    <div class="tab-pane fade show active" id="basic">
                        <table class="table table-sm">
                            <tr><th>NID</th><td>{{ $affiliator->nid }}</td></tr>
                            <tr><th>Code</th><td>{{ $affiliator->affiliator_code ?? 'N/A' }}</td></tr>
                            <tr><th>Payment Method</th><td>{{ ucfirst($affiliator->payment_method) }}</td></tr>
                            <tr><th>Promotion Method</th><td>{{ $affiliator->promotion_method ?? 'N/A' }}</td></tr>
                        </table>
                    </div>

                    {{-- Address --}}
                    <div class="tab-pane fade" id="address">
                        <table class="table table-sm">
                            <tr><th>Street 1</th><td>{{ $affiliator->address_street1 ?? 'N/A' }}</td></tr>
                            <tr><th>Street 2</th><td>{{ $affiliator->address_street2 ?? 'N/A' }}</td></tr>
                            <tr><th>City</th><td>{{ $affiliator->address_city ?? 'N/A' }}</td></tr>
                            <tr><th>State</th><td>{{ $affiliator->address_state ?? 'N/A' }}</td></tr>
                            <tr><th>Postal Code</th><td>{{ $affiliator->address_postal_code ?? 'N/A' }}</td></tr>
                        </table>
                    </div>

                    {{-- Bank Info --}}
                    <div class="tab-pane fade" id="bank">
                        <table class="table table-sm">
                            <tr><th>Bank Name</th><td>{{ $affiliator->bank_name ?? 'N/A' }}</td></tr>
                            <tr><th>Account Name</th><td>{{ $affiliator->account_name ?? 'N/A' }}</td></tr>
                            <tr><th>Account Number</th><td>{{ $affiliator->account_number ?? 'N/A' }}</td></tr>
                            <tr><th>Branch Name</th><td>{{ $affiliator->branch_name ?? 'N/A' }}</td></tr>
                        </table>
                    </div>

                    {{-- Mobile Banking --}}
                    <div class="tab-pane fade" id="mobile">
                        <table class="table table-sm">
                            <tr><th>Provider</th><td>{{ $affiliator->mobile_provider ?? 'N/A' }}</td></tr>
                            <tr><th>Mobile Number</th><td>{{ $affiliator->mobile_number ?? 'N/A' }}</td></tr>
                        </table>
                    </div>

                    {{-- Nominee Info --}}
                    <div class="tab-pane fade" id="nominee">
                        <table class="table table-sm">
                            <tr><th>Name</th><td>{{ $affiliator->nominee_name ?? 'N/A' }}</td></tr>
                            <tr><th>Phone</th><td>{{ $affiliator->nominee_phone ?? 'N/A' }}</td></tr>
                            <tr><th>Relation</th><td>{{ $affiliator->nominee_relation ?? 'N/A' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    </script>
@endsection
