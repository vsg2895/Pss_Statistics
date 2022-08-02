<!-- Modal -->
<input type="hidden" name="_method" value="patch">
<input type="hidden" id="setting_id" name="setting" value="">
<div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="login_Label"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="login_Label">{{__('Choose Login')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="">
                        <a class="btn btn-facebook" href="{{ route('login') }}">
                            <span class="nav-link-inner--text">{{ __('Admin') }}</span>
                        </a>
                    </div>
                    <div class="ml-3">
                        <a class="btn btn-primary" href="{{ route('employee.login') }}">
                            <span class="nav-link-inner--text">{{ __('Employee') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>
