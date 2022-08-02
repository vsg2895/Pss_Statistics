<!-- Modal -->
<form action="{{url('admin/settings/variables/:id')}}" id="settings_form" method="post">
    @csrf
    <input type="hidden" name="_method" value="patch">
    <input type="hidden" id="setting_id" name="setting" value="">
    <div class="modal fade" id="settings_edit" tabindex="-1" role="dialog" aria-labelledby="settings_editLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settings_editLabel">{{__('Edit')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="setting_name">{{__('Name')}}</span>
                            </div>
                            <input type="text" name="name" class="form-control border-left pl-2" id="setting_name_input"
                                   aria-label="Sizing example input" aria-describedby="setting_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="setting_value">{{__('Value')}}</span>
                            </div>
                            <input type="text" name="value" class="form-control border-left pl-2" id="setting_value_input"
                                   aria-label="Sizing example input" aria-describedby="setting_value" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="setting_description">{{__('Description')}}</span>
                            </div>
                            <input type="text" name="description" class="form-control border-left pl-2" id="setting_description_input"
                                   aria-label="Sizing example input" aria-describedby="setting_description">
                        </div>
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
