<form action="{{route('admin.service-providers.upload-media.all')}}" class="provider-media-form" method="post">
    @csrf
    <div class="modal fade" id="provider_all_medias" tabindex="-1" role="dialog"
         aria-labelledby="provider_all_medias_uploadLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="provider_all_medias_uploadLabel">{{__('Upload Media')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-outline">
                        <input id="media-upload-for-all" name="media" class="form-control"
                        placeholder="{{ __('Youtube Link') }}"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>


