<form action="{{url('admin/permission/delete/:permission')}}" id="permission_delete_form" method="post"
      autocomplete="off">
    @csrf
    @method('DELETE')
    <div class="modal fade" id="permission-delete" tabindex="-1" role="dialog" aria-labelledby="permission_deleteLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="permission_deleteLabel">{{__('Delete Permission')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{__('Do you want to delete permission?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('No')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Yes')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
