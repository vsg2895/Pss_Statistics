@extends('layouts.app', [
    "activePage" => strpos(request()->route()->getName(), 'variables') ? "variables" : "default_prices",
    "headerName" => strpos(request()->route()->getName(), 'variables') ? "Variables" : "Default Prices",
])

@push('css')
    <style>
        .setting-description {
            text-align: left !important;
            max-width: 350px !important;
            white-space: pre-wrap !important;
        }
    </style>
@endpush
@section('content')
        <div class="container-fluid main-container">
            <div class="row mt-4">
                <div class="col-md-10">
                    <div class="card">
                        <!-- Light table -->
                        <div class="table-responsive">
                            <table class="table align-items-center">
                                <thead class="thead-light">
                                <tr class="text-center">
                                    <th scope="col" class="sort">{{__('Name')}}</th>
                                    <th scope="col" class="sort">{{__('Value')}}</th>
                                    <th scope="col" class="sort">{{__('Description')}}</th>
                                    <th scope="col" class="sort">{{__('Actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($settings as $setting)
                                    <tr>
                                        <td>
                                            {{$setting->name}}
                                        </td>
                                        <td class="setting-description">
                                            {{$setting->value}}
                                        </td>
                                        <td class="setting-description">
                                            {{$setting->description ?: ""}}
                                        </td>
                                        <td >
                                            <button type="button" data-id="{{$setting->id}}" class="btn btn-primary settings-edit" data-toggle="modal" data-target="#settings_edit">
                                                {{__('Edit')}}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#settings_add">
                        {{__('New')}}
                    </button>
                </div>
            </div>
            @include('layouts.footers.pages')
        </div>

    @include('modals.settings-edit')
    @include('modals.settings-add')
@endsection

@push('js')
    <script>
        $('.settings-edit').click(function () {
            $.get(`/admin/settings/variables/${$(this).attr('data-id')}/edit`, function(data, status){
                let setting = JSON.parse(data).setting;
                let action = $('#settings_form').attr('action');
                $('#setting_name_input').val(setting.name);
                $('#setting_value_input').val(setting.value);
                $('#setting_description_input').val(setting.description);
                action = action.replace(':id', setting.id);
                $('#settings_form').attr('action', action);
            });
        })
    </script>
@endpush
