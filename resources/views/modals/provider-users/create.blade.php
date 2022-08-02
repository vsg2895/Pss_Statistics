<!-- Modal -->
<form action="{{url('admin/service-provider-users')}}" id="provider_user_store_form" method="post" autocomplete="off">
    @csrf
    <input type="hidden" id="setting_id" name="setting" value="">
    <div class="modal fade" id="provider_user_store" tabindex="-1" role="dialog" aria-labelledby="provider_user_storeLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="provider_user_storeLabel">{{__('Create Provider User')}}</h5>
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
                            <input type="text" name="name" value="{{old('name')}}" class="form-control border-left pl-2" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{__('Email')}}</span>
                            </div>
                            <input type="email" name="email" value="{{old('email')}}" class="form-control border-left pl-2" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{__('Password')}}</span>
                            </div>
                            <input class="form-control border-left pl-2" type="password" name="password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{__('Confirm')}}</span>
                            </div>
                            <input class="form-control border-left pl-2" type="password" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3 d-flex align-items-center">
                                <label class="" for="tags">{{__('Select Provider')}}:</label>
                            </div>
                            <div class="col-9">
                                <select class="w-100 form-control select2" name="service_provider_id">
                                    <option></option>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </div>
        </div>
    </div>
</form>
