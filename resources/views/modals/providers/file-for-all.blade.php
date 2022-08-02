<form action="{{route('admin.service-providers.upload-file.all')}}" method="post" enctype="multipart/form-data">
@csrf
<div class="modal fade" id="provider_all_files" tabindex="-1" role="dialog" aria-labelledby="provider_all_files_uploadLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="provider_all_files_uploadLabel">{{__('Upload FIle')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="file-upload-for-all" class="custom-file-upload">
                    {{__('Upload File')}}
                </label>
                <span class="custom-name"></span>
                <input id="file-upload-for-all" name="file" type="file" value="12"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
</form>

