<!-- Modal -->
<form action="{{url('admin/companies/:id')}}" id="companies_form" method="post">
    @csrf
    <input type="hidden" name="_method" value="patch">
    <input type="hidden" id="company_id" name="company" value="">
    <div class="modal fade" id="companies_edit" tabindex="-1" role="dialog" aria-labelledby="companies_editLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="companies_editLabel">{{__('Edit')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                            <label for="tags">{{__('Select Tags')}} :</label>
                            <select class="form-control tags-select" name="tags[]" id="tags" multiple="multiple">
                                @foreach($tags as $tag)
                                    <option
                                        value="{{$tag->id}}" {{in_array($tag->id, $company->tag_ids) ? 'selected' : ''}}>
                                        {{$tag->name}}
                                    </option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="service_provider_id">{{__('Select Service Provider')}}</label>
                        <select class="form-control" id="service_provider_id" name="service_provider_id">
                            <option></option>
                            @foreach($providers as $provider)
                                <option
                                    value="{{$provider->id}}" {{$provider->id == $company->service_provider_id ? 'selected' : ''}}>
                                    {{$provider->name}}
                                </option>
                            @endforeach
                        </select>
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
