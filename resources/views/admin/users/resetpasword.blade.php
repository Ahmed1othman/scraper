<div class="form-modal-ex">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inlineForm">
        <i data-feather='key'></i>
    </button>
    <!-- Modal -->
    <div class="modal fade text-start" id="inlineForm" tabindex="-1" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">{{__('admin.change password')}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('users.change.password',$user->id)}}">
                    @method('POST')
                    @csrf
                    <div class="modal-body">
                        <label class="my-1">{{__('admin.new password')}}</label>
                        <div class="mb-1">
                            <input type="password" placeholder="Password" class="form-control" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{__('admin.change password')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
