@extends('layouts.app', [
    "activePage" => "reports",
    "headerName" => "Reports",
])

@section('content')
    <div class="container-fluid main-container">
        <ul class="nav nav-tabs companies-providers-general-tabs mt-1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="daily-reports-tab" data-toggle="tab"
                   href="#daily-reports" role="tab"
                   aria-controls="daily-reports" aria-selected="false">{{__('Daily')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="monthly-reports-tab" data-toggle="tab" href="#monthly-reports"
                   role="tab"
                   aria-controls="monthly-reports" aria-selected="false">{{__('Monthly')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="excel-reports" data-toggle="tab" href="#excel-historical"
                   role="tab"
                   aria-controls="excel-historical" aria-selected="false">{{__('Excel')}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="excel-compare-reports" data-toggle="tab" href="#excel-compare"
                   role="tab"
                   aria-controls="excel-compare" aria-selected="false">{{__('Compare')}}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade mt-4 show active" id="daily-reports" role="tabpanel"
                 aria-labelledby="range-tab">
                <div class="row mt-2">
                    <div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Daily Reports')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Projects table -->
                                <table class="table align-items-center table-flush table-striped border" id="">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dailyReports as $dailyReport)
                                        <tr>
                                            <td>
                                                <form action="{{route('admin.reports.download')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$dailyReport}}">
                                                    <a href="#" class="submit_download">
                                                        {{basename($dailyReport)}}
                                                    </a>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{route('admin.reports.delete')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$dailyReport}}">
                                                    <input type="hidden" name="anchor" value="#daily-reports">
                                                    <a href="#" type="button"
                                                       class="submit_delete btn btn-sm btn-danger">
                                                        {{__('Delete')}}
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Agent Reports')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Projects table -->
                                <table class="table align-items-center table-flush table-striped border">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($agentReports as $agentReport)
                                        <tr>
                                            <td>
                                                <form action="{{route('admin.reports.download')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$agentReport}}">

                                                    <a href="#" class="submit_download">
                                                        {{basename($agentReport)}}
                                                    </a>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{route('admin.reports.delete')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$agentReport}}">
                                                    <input type="hidden" name="anchor" value="#daily-reports">
                                                    <a href="#" type="button"
                                                       class="submit_delete btn btn-sm btn-danger">
                                                        {{__('Delete')}}
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade mt-4" id="monthly-reports" role="tabpanel"
                 aria-labelledby="range-tab">

                <div class="row mt-2">
                    <div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Monthly Reports')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Monthly table -->
                                <table class="table align-items-center table-flush table-striped border" id="">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($monthlyReports as $monthlyReport)
                                        <tr>
                                            <td>
                                                <form action="{{route('admin.reports.download')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$monthlyReport}}">
                                                    <a href="#" class="submit_download">
                                                        {{basename($monthlyReport)}}
                                                    </a>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{route('admin.reports.delete')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$monthlyReport}}">
                                                    <input type="hidden" name="anchor" value="#monthly-reports">
                                                    <a href="#" type="button"
                                                       class="submit_delete btn btn-sm btn-danger">
                                                        {{__('Delete')}}
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade mt-4" id="excel-historical" role="tabpanel"
                 aria-labelledby="range-tab">
                <div class="row mt-2">
                    <div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Excel Reports')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Monthly table -->
                                <table class="table align-items-center table-flush table-striped border" id="">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($excelReports as $excelReports)
                                        <tr>
                                            <td>
                                                <form action="{{route('admin.reports.download')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$excelReports}}">
                                                    <a href="#" class="submit_download">
                                                        {{basename($excelReports)}}
                                                    </a>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{route('admin.reports.delete')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$excelReports}}">
                                                    <input type="hidden" name="anchor" value="#excel-historical">
                                                    <a href="#" type="button"
                                                       class="submit_delete btn btn-sm btn-danger">
                                                        {{__('Delete')}}
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade mt-4" id="excel-compare" role="tabpanel" aria-labelledby="range-tab">
                <div class="row mt-2">
                    <div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-5 mb-xl-0">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">{{__('Compare Reports')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Monthly table -->
                                <table class="table align-items-center table-flush table-striped border" id="">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($excelCompareReports as $excelReports)
                                        {{--                                        @dd(basename($excelReports))--}}
                                        <tr>
                                            <td>
                                                <form action="{{route('admin.reports.download')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$excelReports}}">
                                                    <a href="#" class="submit_download">
                                                        {{basename($excelReports)}}
                                                    </a>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{route('admin.reports.delete')}}" method="get">
                                                    <input type="hidden" name="path" value="{{$excelReports}}">
                                                    <input type="hidden" name="anchor" value="#excel-compare">
                                                    <a href="#" type="button"
                                                       class="submit_delete btn btn-sm btn-danger">
                                                        {{__('Delete')}}
                                                    </a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('layouts.footers.pages')
@endsection

@push('js')
    <script>
        $('.submit_download,.submit_delete').click(function (e) {
            e.preventDefault();
            $(this).parent().submit();
        });
    </script>
@endpush
