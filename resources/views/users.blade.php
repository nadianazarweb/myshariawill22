@extends('/layouts/master')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css')}}">
@stop

@section('title')
    <title>Users</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="row">
            <div class="col-xl-12 col-md-6 col-12 mb-1">
                <div class="form-group">
                    <input type="text" class="form-control" id="search-fields" placeholder="Search User">
                </div>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-outline-primary waves-effect add-user"><i class="fa-light fa-user"></i> &nbsp;Add User</button>
                @if(isset($_GET['uid']))
                <a href="{{route('users',$user->ref_key)}}" type="button" class="btn btn-outline-primary"><i class="fa-light fa-users"></i> &nbsp;Show All</a>
                @endif
            </div>
        </div>

        <div class="row" id="table-head">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table id="table-user" class="table table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alluser as $all)
                                @if(isset($_GET['uid']))
                                    @if($all->id!=$_GET['uid'])
                                    @continue
                                    @endif
                                @endif
                                    <tr>
                                        <td>
                                            
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="font-weight-bolder">{{ucwords($all->name)}}</div>
                                                    <div class="font-small-2 text-muted">Email: {{$all->email}}</div>
                                                    <div class="font-small-2 text-muted">Username: {{$all->username}}</div>
                                                    <div class="font-small-2 text-muted">Contact: {{$all->contact_no}}</div>
                                                    @if(isset($all->level))
                                                        <div class="font-small-2 text-muted">Type: {{$all->level_detail ? $all->level_detail->title : ""}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{$all->role_detail ? $all->role_detail->display_name : ""}}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($all->status == 1)
                                                    <div style="border-radius: 0.25rem !important;" class="px-2 badge badge-pill badge-light-success">Active</div>
                                                @elseif($all->status == 0)
                                                    <div style="border-radius: 0.25rem !important;" class="px-2 badge badge-pill badge-light-danger">In Active</div>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($all->status == 1)
                                                <button data-userid = "{{$all->id}}" type="button" data-toggle="tooltip" data-placement="top" data-original-title="Deactivate Account" class="btn btn-outline-danger waves-effect mb-1 deactive-account"><i class="fa-regular fa-ban"></i></button>
                                            @else
                                                <button data-userid = "{{$all->id}}" type="button" data-toggle="tooltip" data-placement="top" data-original-title="Activate Account" class="btn btn-outline-success waves-effect mb-1 active-account"><i class="fa-light fa-lock"></i></button>
                                            @endif
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 d-flex justify-content-center">
                <div>
                    <div>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination mt-2">
                                @if ($alluser->currentPage() == 1)
                                    <li class="page-item prev disabled"><span class="page-link"></span></li>
                                @else
                                    <li class="page-item prev"><a class="page-link" href="{{ $alluser->previousPageUrl() }}"></a></li>
                                @endif
            
                                @foreach ($alluser->getUrlRange(1, $alluser->lastPage()) as $page => $url)
                                    @if ($page == $alluser->currentPage())
                                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach
            
                                @if ($alluser->hasMorePages())
                                    <li class="page-item next"><a class="page-link" href="{{ $alluser->nextPageUrl() }}"></a></li>
                                @else
                                    <li class="page-item next disabled"><span class="page-link"></span></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add User Modal --}}
    <div class="modal fade text-left" id="add-user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add New User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <div class="alert alert-success" style="display: none;" role="alert"><p class="alert-body alert-body-success"></p></div>
                        <label>Name: </label>
                        <div class="form-group">
                            <input id="name" type="text" placeholder="Name" class="form-control" />
                            <div class="feedback" style="display: none;"></div>
                        </div>
                        <label>Username: </label>
                        <div class="form-group">
                            <input id="username" type="text" placeholder="Username" class="form-control" />
                            <div class="feedback" style="display: none;"></div>
                        </div>
                        <label>Email: </label>
                        <div class="form-group">
                            <input id="email" type="email" placeholder="Email Address" class="form-control" />
                        </div>
                        <label>Phone: </label>
                        <div class="form-group">
                            <input id="phone" type="text" placeholder="Phone Number" class="form-control" />
                        </div>
                        <label>Password: </label>
                        <div class="form-group">
                            <input id="password" type="password" placeholder="Password" class="form-control" />
                        </div>
                        <label>Role: </label>
                        <div class="form-group">
                            <select class="form-control" id="roles">
                                <option value="0" selected disabled>Please Select</option>
                                @foreach($roles as $r)
                                    <option value = "{{$r->id}}">{{$r->display_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{-- Add User Modal --}}

@endsection

@section('javascript')
    <script src="{{url('backend/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js" integrity="sha256-yE5LLp5HSQ/z+hJeCqkz9hdjNkk1jaiGG0tDCraumnA=" crossorigin="anonymous" ></script>
    <script>
        document.getElementById("search-fields").addEventListener("input", filterTable);
    
        function filterTable(event) {
            var query = event.target.value.toLowerCase();
            var rows = document.getElementById("table-user").querySelectorAll("tbody tr");
            rows.forEach(function(row) {
                var cells = row.querySelectorAll("td");
                var nameCell = cells[0];
                var match = false;
                if (nameCell.textContent.toLowerCase().includes(query)) {
                    match = true;
                } else {
                    for (var j = 1; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().includes(query)) {
                            match = true;
                            break;
                        }
                    }
                }
                if (match) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        $(document).ready(function(){
            $("#phone").mask('99999999999');
            $(".pin-view").click(function(){
                var pin = $(this).data('pin');
                var name = $(this).data('name');
                if(pin != ""){
                    Swal.fire({
                        title: name + " Pin is " + pin,
                        customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                        buttonsStyling: false
                    });
                }
                else{
                    Swal.fire({
                            title: 'Error!',
                            text: 'Sorry! Pin not found',
                            icon: 'error',
                            customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    }); 
                }
            });

            $(".add-user").click(function(){
                $("#add-user-modal").modal('show');
            });

            $("#username").blur(function(){
                var value = $(this).val();
                var link = '/dashboard/check?username=' + value;
                $.get(link,function(res){
                    if(res['status'] == "success"){
                        if($("#username").hasClass('is-invalid')){
                            $("#username").removeClass('is-invalid');
                        }
                        $("#username").addClass('is-valid');
                    }
                    else if(res['status'] == "warning"){
                        if($("#username").hasClass('is-valid')){
                            $("#username").removeClass('is-valid');
                        }
                        $("#username").addClass('is-invalid');
                        $(".btn-submit").attr("disabled","disabled");
                    }
                });
            });

            $("#email").blur(function(){
                var value = $(this).val();
                var link = '/dashboard/check?email=' + value;
                $.get(link,function(res){
                    if(res['status'] == "success"){
                        if($("#email").hasClass('is-invalid')){
                            $("#email").removeClass('is-invalid');
                        }
                        $("#email").addClass('is-valid');
                    }
                    else if(res['status'] == "warning"){
                        if($("#email").hasClass('is-valid')){
                            $("#email").removeClass('is-valid');
                        }
                        $("#email").addClass('is-invalid');
                        $(".btn-submit").attr("disabled","disabled");
                    }
                });
            });

            $(".deactive-account").click(function(){
                var userid = $(this).data('userid');
                var link = '/dashboard/deactive-account/' + userid;
                $.get(link , function(res){
                    if(res['status'] == 'success'){
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }
                });
            });

            $(".active-account").click(function(){
                var userid = $(this).data('userid');
                var link = '/dashboard/active-account/' + userid;
                $.get(link , function(res){
                    if(res['status'] == 'success'){
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }
                });
            });

            $(".btn-submit").click(function(){
                var name = $("#name").val();
                var username = $("#username").val();
                var email = $("#email").val();
                var phone = $("#phone").val();
                var password = $("#password").val();
                var role = $("#roles").val();
                if(name != ""){
                    if(username != ""){
                        if(email != ""){
                            if(phone != ""){
                                if(password != ""){
                                    if(role != ""){
                                        $(this).html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>");
                                        $(this).attr("disabled","disabled");
                                        $.ajax({
                                            url : "/dashboard/user/submit",
                                            type : "POST",
                                            data : {
                                                "_token": "{{ csrf_token() }}",
                                                name : name,
                                                username : username,
                                                email : email,
                                                phone : phone,
                                                password : password,
                                                role : role,
                                            },
                                            success:function(response){
                                                if(response['status'] == "success"){
                                                    $(".alert-body-success").html(response['message']);
                                                    $(".alert-success").slideDown();
                                                    $(".btn-submit").html("Submit");
                                                    setTimeout(() => {
                                                        location.reload();
                                                    }, 2000);
                                                }
                                            }
                                        });
                                    }
                                    else{
                                        $("#role").addClass('error');
                                        $("#role").focus();
                                    }
                                }
                                else{
                                    $("#password").addClass('error');
                                    $("#password").focus();
                                }
                            }
                            else{
                                $("#phone").addClass('error');
                                $("#phone").focus();
                            }
                        }
                        else{
                            $("#email").addClass('error');
                            $("#email").focus();
                        }
                    }
                    else{
                        $("#username").addClass('error');
                        $("#username").focus();
                    }
                }
                else{
                    $("#name").addClass('error');
                    $("#name").focus();
                }
            });
        });
    </script>
@stop