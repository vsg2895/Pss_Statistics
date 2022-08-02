@extends('layouts.app', [
    "activePage" => "planning",
    "headerName" => "Planning",
])

@section('content')

    <div class="container-fluid">
        <div class="mt-4">
            <form action="{{route('admin.planning')}}" method="get" autocomplete="off">
                <div class="input-daterange datepicker row align-items-center planning-datepicker">
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                                </div>
                                <input class="form-control" name="start_date"
                                       placeholder="{{__('Start date')}}" type="text"
                                       value="{{ old('start_date') ?? request()->start_date }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="ni ni-calendar-grid-58"></i></span>
                                </div>
                                <input class="form-control" name="end_date"
                                       placeholder="{{__('End date')}}" type="text"
                                       value="{{ old('end_date') ?? request()->end_date }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12 d-flex justify-content-end">
                        <div class="form-group">
                            <div class="input-group">
                                <button class="btn btn-primary">{{__('Submit')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12 mb-5 mb-xl-0" id="planning-pdf-content">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{__('Planning')}}</h3>
                            </div>
                            @if(auth()->guard('web')->check())
                                <div class="col text-right">
                                    <form action="{{route('admin.export_pdf.planing')}}" method="get"
                                          id="export_form_planing">
                                        <input type="hidden" id="export_start_date" name="start_date" value="">
                                        {{--                                        <input type="hidden" id="export_compare_date" name="compare_date" value="">--}}
                                        {{--                                        <input type="hidden" id="export_start" name="start" value="">--}}
                                        <input type="hidden" id="export_end_date" name="end_date" value="">
                                        <input type="hidden" id="export_date_range" name="date_range" value="true">
                                        <button type="button" class="btn btn-sm btn-primary"
                                                onclick="generatePlanningPDF()"
                                                id="export_pdf_planing">{{__('Export PDF')}}</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table align-items-center table-flush table-striped border" id="planning_table">
                            <thead class="thead-light">
                            <tr>
                                <th rowspan="2">{{__('Time')}}</th>
                                <th rowspan="2">{{__('Total Calls')}}</th>
                                <th rowspan="2">{{__('Missed Calls')}}</th>
                                <th rowspan="2">{{__('Avg Answer Time')}}</th>
                                <th rowspan="2">{{__('Bookings')}}</th>
                                <th rowspan="2">{{__('Chats')}}</th>
                                <th rowspan="2">{{__('Progress')}}</th>
                                <th class="text-center" colspan="3">{{__('Agents')}}</th>
                            </tr>
                            <tr>
                                <th class="text-center">{{__('Theory Count')}}</th>
                                <th class="text-center">{{__('Agents Count')}}</th>
                                <th class="text-center">{{__('Difference')}}</th>
                                {{--                                <th class="text-center">{{__('Diff in percent')}}</th>--}}
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($data as $hour => $datum)
                                <tr>
                                    <td>{{$hour}}</td>
                                    <td class="column-calls">{{$data[$hour]['calls']}}</td>
                                    <td class="column-missed-calls">{{$data[$hour]['missedCalls']}}</td>
                                    <td >{{gmdate("i:s", $data[$hour]['avg_waiting_time'])}}</td>
                                    <td class="column-answer-time hidden">{{$data[$hour]['avg_waiting_time']}}</td>
                                    <td class="column-bookings">{{$data[$hour]['bookings']}}</td>
                                    <td class="column-chats">{{$data[$hour]['chats']}}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2"><span
                                                    class="column-progress">{{$data[$hour]['progress']}}</span>%</span>
                                            <div class="progress m-0" style="max-width: 150px;">
                                                <div
                                                    class="progress-bar bg-gradient-{{$data[$hour]['progress'] < 80 ? 'danger' : ($data[$hour]['progress'] >=100 ? 'green' : 'yellow')}}"
                                                    role="progressbar"
                                                    aria-valuenow="{{$data[$hour]['progress']}}" aria-valuemin="0"
                                                    aria-valuemax="120"
                                                    style="width: {{$data[$hour]['progress']}}%;">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @php
                                        $difference = $data[$hour]['agentsCount'] - $data[$hour]['teoryEmpCount'];
                                        $diffInPercent = round($difference * 100 / max((($data[$hour]['teoryEmpCount'] + $data[$hour]['agentsCount']) / 2), 1))
                                    @endphp
                                    <td class="text-center column-theory-count">{{$data[$hour]['teoryEmpCount']}}</td>
                                    <td class="text-center cursor-help column-agents-count
                                        {{(abs($diffInPercent) <= $data[$hour]['difference_percentage'] && abs($diffInPercent) >= 0)
                                            ? "bg-green"
                                            : ($data[$hour]['teoryEmpCount'] < $data[$hour]['agentsCount'] ? "bg-blue"
                                            : ($data[$hour]['teoryEmpCount'] > $data[$hour]['agentsCount'] ? "bg-orange" : ""))}}
                                        "
                                        data-toggle="tooltip" data-placement="bottom" data-html="true"
                                        title="
                                                @foreach($data[$hour]['agentsList'] as $agentName)
                                        {{$agentName . "<br>"}}
                                        @endforeach
                                            ">
                                        {{$data[$hour]['agentsCount']}}
                                    </td>
                                    <td class="text-center column-difference">{{$difference}}</td>
                                    {{--                                        <td class="text-center">{{$diffInPercent}}</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td class="font-weight-bold">{{__('Totals')}}</td>
                                <td class="font-weight-bold" id="total_calls"></td>
                                <td class="font-weight-bold" id="total_missed"></td>
                                <td class="font-weight-bold" id="avg_answer_time"></td>
                                <td class="font-weight-bold" id="total_bookings"></td>
                                <td class="font-weight-bold" id="total_chats"></td>
                                <td class="font-weight-bold" id="avg_progress"></td>
                                <td class="text-center font-weight-bold" id="avg_theory_count"></td>
                                <td class="text-center font-weight-bold" id="avg_agents_count"></td>
                                <td class="text-center font-weight-bold" id="avg_difference"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.pages')
@endsection

@push('js')
    <script src="{{asset('assets/js/pages/planning.js')}}"></script>
@endpush
