<!-- Modal -->
<form action="{{url('admin/agents/:id')}}" id="agent_update_form" method="post" autocomplete="off">
    @csrf
    <input type="hidden" name="_method" value="patch">
    <div class="modal fade" id="agent_update" tabindex="-1" role="dialog" aria-labelledby="agent_updateLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agent_updateLabel">{{__('Update agent Data')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{__('Name')}}</span>
                            </div>
                            <input type="text" id="agent_name_update" class="form-control border-left pl-2" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{__('Email')}}</span>
                            </div>
                            <input type="email" id="agent_email_update" class="form-control border-left pl-2" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{__('Agent Point')}}</span>
                            </div>
                            <input type="number" id="agent_point" name="agent_point" class="form-control border-left pl-2"
                                   data-toggle="tooltip" data-placement="top" title="{{__('If you want to set default main point, just leave empty this field')}}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
