<div class="col-xs-12 col-lg-12 col-md-12 col-12 mb-1 mb-xl-0 p-2 d-none" id="permission-part">
    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">{{__('Permissions')}}</h3>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <!-- Roles table -->
            <table class="table align-items-center table-flush table-striped border" id="">
                <thead class="thead-light">
                <tr>
                    <th>{{__('Permission')}}</th>
                    <th>{{__('Delete')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($res['permissions'] as $key => $permission)
                    <tr>
                        <td>{{$permission->name}}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger ml-2 delete-permission"
                                    data-id="{{$permission->id}}"
                                    data-toggle="modal"
                                    data-target="#permission-delete">{{__('Delete')}}</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@include('modals.users.permissionDelete')
