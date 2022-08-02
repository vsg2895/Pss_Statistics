<!-- Modal -->
<form action="{{url('admin/service-provider-users/:id')}}" id="provider_user_update_form" method="post" autocomplete="off">
    @csrf
    <input type="hidden" name="_method" value="patch">
    <div class="modal fade" id="provider_user_update" tabindex="-1" role="dialog" aria-labelledby="provider_user_updateLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="provider_user_updateLabel">{{__('Update Provider User Data')}}</h5>
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
                            <input type="text" name="name" id="provider_user_name_update" class="form-control border-left pl-2" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{__('Email')}}</span>
                            </div>
                            <input type="email" id="provider_user_email_update" name="email" class="form-control border-left pl-2" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3 d-flex align-items-center">
                                <label class="" for="tags">{{__('Select Provider')}}:</label>
                            </div>
                            <div class="col-9">
                                <select class="w-100 form-control" name="service_provider_id" id="service_provider_id">
                                    @foreach($providers as $provider)
                                        <option value="{{$provider->id}}">
                                            {{$provider->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
