<form action="{{url('admin/service-providers/delete-media/:id')}}" id="provider_media_delete_form" method="post"
      autocomplete="off">
    @csrf
    <input type="hidden" name="_method" value="delete">
    <div class="modal fade" id="provider_media_delete" tabindex="-1" role="dialog"
         aria-labelledby="provider_media_deleteLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="provider_media_deleteLabel">{{__('Delete Media')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{__('Do you want to delete media?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('No')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Yes')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>

