<div class="modal fade" id="show_ttu_info" tabindex="-1" role="dialog" aria-labelledby="available"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="available">{{__('User Available')}}</h5>
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
                        <input type="text" id="ttuFullName" class="form-control border-left pl-2">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{__('ID')}}</span>
                        </div>
                        <input type="text" id="ttuId" class="form-control border-left pl-2">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{{__('Available')}}</span>
                        </div>
                        <input type="text" id="ttuAvailable" class="form-control border-left pl-2">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
