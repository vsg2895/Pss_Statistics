<form action="{{ url('admin/department/delete/:id') }}" id="departments_delete_form" method="post" autocomplete="off">
    @csrf
    @method('DELETE')
{{--    <input type="hidden" name="_method" value="delete">--}}
    <div class="modal fade" id="departments_delete" tabindex="-1" role="dialog" aria-labelledby="departments_deleteLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="departments_deleteLabel">{{__('Delete Departments')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{__('Do you want to delete department?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('No')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Yes')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>

