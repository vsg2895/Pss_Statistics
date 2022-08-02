<form action="{{route('admin.import.excel', ['company' => $company, 'start' => $todayStart,'end' => $todayEnd])}}"
      method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="companies_import_upload" tabindex="-1" role="dialog" aria-labelledby="companies_import_uploadLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="companies_import_uploadLabel">{{__('Upload FIle')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="companies-import" class="custom-file-upload">
                        {{__('Upload File')}}
                    </label>
                    <span class="custom-name"></span>
                    <input id="companies-import" class="d-none" name="import" type="file" value=""/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
