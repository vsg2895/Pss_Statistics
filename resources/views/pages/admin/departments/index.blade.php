@extends('layouts.app', [
    "activePage" => "departments",
    "headerName" => "Departments",
])

@push('css')
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
@endpush

@section('content')

    <div class="container-fluid main-container">
        <div class="row mt-2">
            <div class="col-12 mb-5 mb-xl-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link departmets-tab-link active" id="active-departments-tab" data-toggle="tab"
                           href="#active-departments" role="tab"
                           aria-controls="active-departments" aria-selected="true">{{__('Active')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link departmets-tab-link" id="inactive-departments-tab" data-toggle="tab"
                           href="#inactive-departments"
                           role="tab"
                           aria-controls="inactive-departments" aria-selected="false">{{__('Inactive')}}</a>
                    </li>
                </ul>
                <div class="tab-content">

                    <div class="tab-pane fade show active" id="active-departments" role="tabpanel"
                         aria-labelledby="range-tab">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">{{__('Departments')}}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <!-- Projects table -->
                            <table class="table align-items-center table-flush table-striped border">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Companies')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($activeDepartments as $department)
                                    <form
                                        action="{{ route('admin.department.update',['department' => $department->id]) }}"
                                        method="post">
                                        @csrf
                                        <tr style="{{ ($department->company) ? '' : 'background-color: aliceblue' }}">
                                            <td>
                                                {{ $department->name }}
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-xl-10 col-lg-10 col-12">
                                                        <select class="tags-select w-100 companies_select"
                                                                name="company_id">
                                                            <option value="">No Company</option>
                                                            @foreach($allCompanies as $company)
                                                                <option
                                                                    {{($department->company && $department->company_id == $company->company_id) ? 'selected' : ''}}
                                                                    value="{{ $company->company_id }}">
                                                                    {{ $company->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="submit"
                                                        class="btn btn-sm btn-primary">{{is_null($department->company) ? __('Attach') : __('Detach')}}</button>
                                                <button type="button" class="btn btn-sm btn-danger delete-department"
                                                        data-id="{{ $department->id }}" data-toggle="modal"
                                                        data-target="#departments_delete">{{__('Delete')}}</button>
                                            </td>
                                        </tr>
                                    </form>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $activeDepartments->links('vendor.pagination.bootstrap-4') }}
                    </div>
                    {{--            Inactive Part    --}}
                    <div class="tab-pane fade show" id="inactive-departments" role="tabpanel"
                         aria-labelledby="range-tab">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">{{__('Inactive Departments')}}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <!-- Projects table -->
                            <table class="table align-items-center table-flush table-striped border">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    {{--                                    <th>{{__('Companies')}}</th>--}}
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($deletedDepartmetns as $department)
                                    <form
                                        action="{{ route('admin.department.activate',['id' => $department->id]) }}"
                                        method="post">
                                        @csrf
                                        <tr style="{{ ($department->company) ? '' : 'background-color: aliceblue' }}">
                                            <td>
                                                {{ $department->name }}
                                            </td>
                                            {{--                                            <td>--}}
                                            {{--                                                <div class="row">--}}
                                            {{--                                                    <div class="col-6">--}}
                                            {{--                                                        <select class="tags-select w-100 companies_select"--}}
                                            {{--                                                                name="company_id">--}}
                                            {{--                                                            <option value="">No Company</option>--}}
                                            {{--                                                            @foreach($allCompanies as $company)--}}
                                            {{--                                                                <option--}}
                                            {{--                                                                    {{($department->company && $department->company_id == $company->company_id) ? 'selected' : ''}}--}}
                                            {{--                                                                    value="{{ $company->company_id }}">--}}
                                            {{--                                                                    {{ $company->name }}--}}
                                            {{--                                                                </option>--}}
                                            {{--                                                            @endforeach--}}
                                            {{--                                                        </select>--}}
                                            {{--                                                    </div>--}}
                                            {{--                                                </div>--}}
                                            {{--                                            </td>--}}
                                            <td>
                                                <button type="submit"
                                                        class="btn btn-sm btn-primary">{{__('Activate')}}</button>
                                            </td>
                                        </tr>
                                    </form>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $deletedDepartmetns->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>


            </div>
        </div>
    </div>
    @include('layouts.footers.pages')
    @include('modals.departments.delete')
@endsection

@push('js')
    <script src="{{asset('assets/vendor/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/departments.js')}}"></script>

@endpush
