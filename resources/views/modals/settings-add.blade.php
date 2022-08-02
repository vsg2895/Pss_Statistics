<!-- Modal -->
<form action="{{url('admin/settings/variables')}}" method="post">
    @csrf
    <div class="modal fade" id="settings_add" tabindex="-1" role="dialog" aria-labelledby="settings_addLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settings_addLabel">{{__('Add')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="setting_name0">{{__('Name')}}</span>
                            </div>
                            <input type="text" name="name" class="form-control border-left pl-2" id="setting_name0_input"
                                   aria-label="Sizing example input" aria-describedby="setting_name0" value="{{old('name')}}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="setting_value0">{{__('Value')}}</span>
                            </div>
                            <input type="text" name="value" class="form-control border-left pl-2" id="setting_value0_input"
                                   aria-label="Sizing example input" aria-describedby="setting_value0" value="{{old('value')}}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="setting_slug0">{{__('Slug')}}</span>
                            </div>
                            <input type="text" name="slug" class="form-control border-left pl-2" id="setting_slug0_input"
                                   aria-label="Sizing example input" aria-describedby="setting_slug0" value="{{old('slug')}}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="setting_description0">{{__('Description')}}</span>
                            </div>
                            <input type="text" name="description" class="form-control border-left pl-2" id="setting_description0_input"
                                   aria-label="Sizing example input" aria-describedby="setting_description0" value="{{old('description')}}">
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
