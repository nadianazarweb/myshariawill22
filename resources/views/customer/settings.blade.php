@extends('customer.layouts.master')
@section('css')

@stop

@section('title')
<title>Settings</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="card">
            <div class="mr-auto p-2">
                <form action="{{ route('customer_update_settings') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Full Name</label>
                        <input type="text" required class="form-control" name="name" value="{{ $user->full_name }}">
                    </div>

                    <div class="form-group">
                        <label for="">Username</label>
                        <input type="text" required class="form-control" name="contact_no" value="{{ $user->user_name }}"
                            disabled>
                    </div>

                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="email" required class="form-control" name="contact_no" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="">Contact No</label>
                        <input type="text" required class="form-control" name="contact_no" value="{{ $user->contact_no }}">
                    </div>

                    <div class="form-group">
                        <label for="">Select Avatar</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input onchange="validateFileType(this)" name="avatar" type="file" class="custom-file-input" id="inputGroupFile01"
                                    aria-describedby="inputGroupFileAddon01">
                                <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-info btn-sm BtnChangePassword" type="button">Change Password</button>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>


                </form>
            </div>

            <div class="modal fade" id="ChangePasswordModal" tabindex="-1" aria-labelledby="ShowDataModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ShowDataModalLabel">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height:73vh; overflow:auto">
                        <form action="{{ route('customer_change_password') }}" method="post" class="FormChangePassword">
                            @csrf
                            <div class="form-group">
                                <label for="">Enter New Password</label>
                                <input type="password" required name="password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">Confirm New Password</label>
                                <input type="password" name="confirm_password" required class="form-control">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-sm">Update</button>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('frontend/js/moment.min.js')}}"></script>

<script>
    var SuccessMsg = '<?= session('success_msg') ?>';
    if (SuccessMsg != "") {
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: SuccessMsg,
            showConfirmButton: true,
        });
    }

    var FailureMsg = '<?= session('failure_msg') ?>';
    if (FailureMsg != "") {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: FailureMsg,
            showConfirmBn:true,
        });
    }


    function validateFileType(input) {
        const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.webp)$/i;
        if (!allowedExtensions.exec(input.value)) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Only images with .jpg, .jpeg, .png, or .webp extension are allowed!',
                showConfirmBn:true,
            });
            input.value = '';
            return false;
        }
    }

    $(function(){
        $('.BtnChangePassword').on('click',function(){
                $('#ChangePasswordModal').modal('show');
            });
        $('.FormChangePassword').on('submit',function(e){
            var Password = $('input[name="password"]').val();
            var ConfirmPassword = $('input[name="confirm_password"]').val();

            if(Password!==ConfirmPassword){
                e.preventDefault();
                Swal.fire({
                    icon: "error",
                    title: "Password Dont Match",
                });
            }

        });
    });

</script>
@stop