<!-- Modal -->
<form action="{{url('admin/tags')}}" method="post">
    @csrf
    <div class="modal fade" id="tags_add" tabindex="-1" role="dialog" aria-labelledby="tags_addLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tags_addLabel">{{__('Add')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="tag_name0">{{__('Name')}}</span>
                            </div>
                            <input type="text" name="name" class="form-control border-left pl-2" id="tag_name0_input"
                                   aria-label="Sizing example input" aria-describedby="tag_name0" value="{{old('name')}}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Create')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
