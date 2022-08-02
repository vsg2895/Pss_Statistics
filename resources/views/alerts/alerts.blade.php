@if ($message = Session::get('default'))
<div class="alert custom-alert alert-default alert-dismissible fade show mt-3 mr-2" role="alert">
    <span class="alert-text">{{$message}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($message = Session::get('primary'))
<div class="alert custom-alert alert-primary alert-dismissible fade show mt-3 mr-2" role="alert">
    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
    <span class="alert-text">{{$message}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($message = Session::get('secondary'))
<div class="alert custom-alert alert-secondary alert-dismissible fade show mt-3 mr-2" role="alert">
    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
    <span class="alert-text">{{$message}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($message = Session::get('dismiss'))
<div class="alert custom-alert alert-info alert-dismissible fade show mt-3 mr-2" role="alert">
    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
    <span class="alert-text">{{$message}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($message = Session::get('success'))
<div class="alert custom-alert alert-success alert-dismissible fade show mt-3 mr-2" role="alert">
    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
    <span class="alert-text">{{$message}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert custom-alert alert-danger alert-dismissible fade show mt-3 mr-2" role="alert">
    <span class="alert-text">{{$message}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert custom-alert alert-warning alert-dismissible fade show mt-3 mr-2" role="alert">
    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
    <span class="alert-text">{{$message}}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($errors->any())
    <div class="alert custom-alert alert-danger alert-dismissible fade show mt-3 mr-2" role="alert">
        <span class="alert-text">{{$errors->all()[0]}}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
