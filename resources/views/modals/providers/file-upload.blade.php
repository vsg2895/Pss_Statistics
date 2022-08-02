<form action="{{route('admin.service-providers.upload-file', ['serviceProvider' => $provider])}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="provider_file_upload" tabindex="-1" role="dialog" aria-labelledby="provider_file_uploadLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="provider_file_uploadLabel">{{__('Upload FIle')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="file-upload" class="custom-file-upload">
                        {{__('Upload File')}}
                    </label>
                    <span class="custom-name"></span>
                    <input id="file-upload" name="file" type="file" value="12"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
