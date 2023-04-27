@if ($errors->any())
    <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
        @foreach ($errors->all() as $error)
        <div class="alert-body d-flex align-items-center">

                <i data-feather="info" class="me-50"></i>
                <span>{{$error}}</span>
        </div>
        @endforeach
    </div>
@endif


