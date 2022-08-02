@extends('layouts.app', [
    "activePage" => "company_compare",
    "headerName" => "Compare Companies",
])

@push('css')
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
    <style>
        div.dataTables_wrapper div.dataTables_filter label {
            padding-right: 0.5rem;
        }
    </style>
@endpush

@section('content')
    {{--         Compare Companies Data in Date Range   --}}
    <div class="container-fluid main-container export-all-company">
        <div class="row p-4 justify-content-center" id="companies-compare-date-range" role="tabpanel"
             aria-labelledby="range-tab">
            <div class="row justify-content-start w-100 ">
                <x-datepicker.both-date-range class="companies-compare-datepicker"
                                              form="compare-companies-data"/>
                <div class="col-xl-1 col-lg-1 col-md-3 mb-lg-2">
                    <form
                        action="{{ route('admin.compare.export.excel') }}"
                        method="post"
                        id="export_excel_compare">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary mr-2" id="compare_export_excel">
                            {{__('Export Excel')}}</button>
                    </form>
                </div>
            </div>
            <form
                action="{{ route('admin.compare.companies.dateRange',['start' => $thisMonthStart, 'end' => $thisMonthEnd,'s_start' => $lastMonthStart, 's_end' => $lastMonthEnd]) }}"
                id="compare-companies-data" method="get">
            </form>
            <div class="row w-100 justify-content-around flex-wrap compare-tables-row">
                @php
                    $firstRange = $compareData[request()->s_start . ' - ' . request()->s_end]['data']->toArray();
                    $secondRange = $compareData[request()->start . ' - ' . request()->end]['data']->toArray();
                    $counter = 0;
                @endphp

                @foreach($compareData as $key => $data)
                    @php
                        $counter++
                    @endphp
                    <div class="col-xl-6 col-lg-10 col-md-12 col-12">
                        <table class="table compare-table @if(!count($data['data'])) empty-compare @endif" id="compareDataTable{{$counter}}">
                            @if(count($data['data']))
                                <h3 class="mb-0">{{ $key }}</h3>
                                <p>{{__('Total companies')}}: {{count($data['data'])}}</p>
                                <p>
                                    {{$counter === 2 ? __('New companies') : __('Lost companies')}}
                                    : {{$data['diffIds'] !== 0 ? count($data['diffIds']) : 0}}
                                    @if($counter === 2)
                                        | {{__('Netto')}}: {{$data['netto']['companies']}}
                                        <span
                                            class="{{$data['netto']['companies'] >= 0 ? 'text-success' : 'text-danger'}}">
                                            <i class="fa {{$data['netto']['companies'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i>
                                        </span>
                                    @endif
                                </p>
                                <p>
                                    {{$counter === 2 ? __('New') : __('Churn')}}: {{$data['diff_percent']}}%
                                    @if($counter === 2)
                                        <span class="text-success">
                                            <i class="fa fa-arrow-up ql-color-green"></i>
                                        </span>
                                        <span>
                                            | {{__('Netto')}}: {{$data['netto']['percent']}}%
                                            <span
                                                class="{{$data['netto']['percent'] >= 0 ? 'text-success' : 'text-danger'}}">
                                                <i class="fa {{$data['netto']['percent'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i>
                                            </span>
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fa fa-arrow-down"></i>
                                        </span>
                                    @endif
                                </p>
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('Company Name') }}</th>
                                    <th scope="col">{{ __('Calls Count') }}</th>
                                    <th>{{$counter === 2 ? __('New') : __('Lost')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['data'] as $keyData => $elem)
                                    @php
                                        $isExist = $key == request()->start . ' - ' . request()->end
                                                          && array_key_exists($keyData, $firstRange)
                                                          || $key == request()->s_start . ' - ' . request()->s_end
                                                          && array_key_exists($keyData, $secondRange)
                                    @endphp
                                    <tr class="@if($isExist) blue-column
                                               @else {{$counter === 2 ? 'green-column' : 'red-column'}} @endif">
                                        <td>{{ $elem[0]->company_id }}</td>
                                        <td>
                                            <a href="{{route('admin.companies.show', ['company' => $elem[0]->id])}}">{{ $elem[0]->name }}</a>
                                        </td>
                                        <td>{{$elem[0]->calls_count}}</td>
                                        <td>{{$isExist ? 0 : 1}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @else
                                <thead>
                                <tr>
                                    <h3 class="mb-0 no-data-compare">
                                        {{ __('In') }} {{ $key }} {{ __('There are no data') }}
                                    </h3>
                                </tr>
                                </thead>
                            @endif

                        </table>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('assets/js/pages/compare.js')}}"></script>
@endpush
