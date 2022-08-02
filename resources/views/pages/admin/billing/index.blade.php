@extends('layouts.app', [
    "activePage" => "billing",
    "headerName" => "Billing",
])

@push('css')
@endpush
@section('content')
    <div class="container-fluid main-container">
        <form class="mt-4" action="{{ route('admin.billing.index') }}" method="get" autocomplete="off">
            <x-datepicker.date-range class="billing-datapicker" button-title="{{__('Submit Filters')}}"/>
            <div class="row">
                <x-datepicker.default-filters-form/>
            </div>

        </form>
        <div class="row mt-2">
            <div class="col-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    {{--    Table Component      --}}
                    <x-billing.billing-data-table :cdrStatistics="$cdrStatistics" :fixed="$fixed"/>
                </div>
            </div>
        </div>

    </div>
    @include('layouts.footers.pages')
@endsection

@push('js')
    @routes
    <script src="{{asset('assets/js/pages/billing.js')}}"></script>
@endpush
