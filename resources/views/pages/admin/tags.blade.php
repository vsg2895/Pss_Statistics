@extends('layouts.app', [
    "activePage" => "company_tags",
    "headerName" => "Company Tags",
])

@push('css')

@endpush
@section('content')
    <div class="container-fluid main-container">
        <div class="row mt-4">
            <div class="col-6 col-sm-4">
                <div class="card">
                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center text-center">
                            <thead class="thead-light">
                            <tr class="text-center">
                                <th scope="col" class="sort">{{__('Name')}}</th>
                                <th scope="col" class="sort">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tags as $tag)
                                <tr>
                                    <td>
                                        {{$tag->name}}
                                    </td>
                                    <td>
                                        <button type="button" data-id="{{$tag->id}}" class="btn btn-sm btn-primary tags-edit"
                                                data-toggle="modal" data-target="#tags_edit">
                                            {{__('Edit')}}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger ml-2 tags-delete" data-id="{{$tag->id}}"
                                                data-toggle="modal" data-target="#tags_delete">
                                            {{__('Delete')}}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-8">
                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#tags_add">
                    {{__('New')}}
                </button>
            </div>
        </div>
    </div>
    @include('layouts.footers.pages')

    @include('modals.tags.tags-edit')
    @include('modals.tags.tags-add')
    @include('modals.tags.tags-delete')
@endsection

@push('js')
    <script>
        $('.tags-edit').click(function () {
            $.get(`/admin/tags/${$(this).attr('data-id')}/edit`, function (data, status) {
                let tag = JSON.parse(data).tag;
                let action = $('#tags_form').attr('action');
                $('#tag_name_input').val(tag.name);
                action = action.replace(':id', tag.id);
                $('#tags_form').attr('action', action);
            });
        })

        $('.tags-delete').click(function () {
            let action = $('#tags_delete_form').attr('action');
            action = action.replace(':id', $(this).attr('data-id'));
            $('#tags_delete_form').attr('action', action);
        })
    </script>
@endpush
