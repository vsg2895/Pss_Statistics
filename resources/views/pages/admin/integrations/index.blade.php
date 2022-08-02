@extends('layouts.app', [
    "activePage" => "integrations",
    "headerName" => "Integrations",
])

@push('css')
@endpush

@section('content')
    <div class="container-fluid main-container">
        <div class="row mt-4">
            <div class="col-xl-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{__('Tele Two Users')}}</h3>
                            </div>
                            <a href="{{route('admin.insert-tele-two-users')}}"
                               class="btn btn-sm btn-success update-ttu">{{__('Update Data')}}
                                <i class="loading-icon-info-update fa-lg fas fa-spinner d-none fa-spin hide"></i>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table align-items-center table-flush table-striped" id="tele_two_users_table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{__('Contact Id')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Phone Number')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($teleTwoUsers as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->full_name}}</td>
                                    <td>{{$user->phone_number}}</td>
                                    <td>{{$user->created_at}}</td>
                                    <td>
                                        {{--                                        data-target="#show_ttu_info"--}}
                                        <button type="button" class="btn btn-sm btn-primary show-info"
                                                data-id="{{$user->id}}"
                                                data-name="{{$user->full_name}}"
                                                data-toggle="modal">{{__('Info')}}
                                            <i class="loading-icon-info fa-lg fas fa-spinner d-none fa-spin hide"></i>
                                        </button>
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
    @include('modals.ttu-moreInfo')
    @include('layouts.footers.pages')

@endsection

@push('js')

    @routes
    <script src="{{asset('assets/js/pages/ttu.js')}}"></script>

@endpush

