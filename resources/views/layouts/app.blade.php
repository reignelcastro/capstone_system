<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('sidebar/css/bootstrap.min.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/w3.css') }}" rel="stylesheet">
    <link href="{{ asset('fonts/app.css') }}" rel="stylesheet">
    <link href="{{ asset('awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sidebar/css/style.css') }}">

    <link href="{{ asset('dt/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="{{ asset('dt/jquery.dataTables.min.js') }}" defer></script>

    <script src="{{ asset('js/swal.min.js') }}"></script>
    <script>
        function loader(){
            document.getElementById('loader').style.display='block';
        }
        function loaderx(){
            document.getElementById('loader').style.display='none';
        }
        function view_details(_token, beneficiary_id,services_id, with_requirements){
            $.ajax({
                url:'{{route("details-view")}}',
                method:'POST',
                data:{
                    beneficiary_id:beneficiary_id,
                    services_id:services_id,
                    with_requirements:with_requirements,
                    _token:_token
                },
                dataType:'json',
                success:function(response){
                    if(response.success)
                    {
                        $('#details-container').html(response.html);
                    }
                    else if(response.error)
                    {
                        swal("ERROR!", response.error, "error");
                    }
                }
            });
        }
        $(function(){
            var x = $('meta[name="csrf-token"]').attr('content');
            $('[data-toggle="popover"]').popover();
            $('#dt').DataTable({
                'paging'      : false,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : false,
                'autoWidth'   : false,
                'select'      : true,
                "order": [[ 0, "desc" ]]
            });
            $(document).on('click','.alert', function(){
                $(this).hide();
            });

            $(document).on('click','.view_details', function(){
                var beneficiary_id  = $(this).data('beneficiary_id');
                var with_requirements = $(this).data('with_requirements');
                var services_id  = $(this).data('services_id');
                view_details(x, beneficiary_id,services_id, with_requirements);
            });
            $(document).on('click', '.download', function (e) {
                var upload_id  = $(this).data('upload_id');
                var type       = $(this).data('type');
                var file_name  = $(this).data('file_name');

                // if(type === 'image')
                // {
                //     document.getElementById('image-modal').style.display='block';
                //     $('#file-container').html('<img src="storage/requirements/'+file_name+'" style="width:100%;max-width:100%;">');
                // }
                // else
                // {
                    e.preventDefault();
                    window.open('storage/requirements/'+file_name);
                //}
            });

            $(document).on('click','.action', function(){
                var application_id  = $(this).data('application_id');
                var action          = $(this).data('action');
                swal({
                    title: "",
                    text: "Are you sure you want to "+action+" this request?",
                    icon: "",
                    buttons: true,
                    buttons:['Cancel','Yes'],
                    dangerMode: true,
                    })
                    .then((willSubmit) => {
                    if (willSubmit) 
                    {
                        $.ajax({
                            url:'{{route("update-application")}}',
                            method:'POST',
                            data:{
                                application_id:application_id,
                                action:action,
                                _token:x
                            },
                            dataType:'json',
                            success:function(response){
                                if(response.success)
                                {
                                    swal("SUCCESS!", response.success, "success");
                                    loader();
                                    setInterval(function(){
                                        location.reload();
                                    },1500);
                                }
                                else if(response.error)
                                {
                                    swal("ERROR!", response.error, "error");
                                }
                            }
                        });  
                    }
                });
            });

            $(document).on('click','.send_sms', function(){
                var beneficiary_id  = $(this).data('beneficiary_id');
                var services_id     = $(this).data('services_id');
                var aics_services   = $(this).data('aics_services');
                var action          = $(this).data('action');
                swal({
                    title: "",
                    text: "Your request for "+aics_services+" has been "+action+". \n\n Thank you for your requesting in our services. \n\n -MSWD",
                    icon: "",
                    buttons: true,
                    buttons:['Cancel','Confirm'],
                    dangerMode: true,
                    })
                    .then((willSubmit) => {
                    if (willSubmit) 
                    {
                        $.ajax({
                            url:'{{route("send-sms")}}',
                            method:'POST',
                            data:{
                                beneficiary_id:beneficiary_id,
                                services_id:services_id,
                                aics_services:aics_services,
                                action:action,
                                _token:x
                            },
                            dataType:'json',
                            success:function(response){
                                if(response.success)
                                {
                                    swal("SUCCESS!", response.success, "success");
                                    loader();
                                    setInterval(function(){
                                        location.reload();
                                    },1500);
                                }
                                else if(response.error)
                                {
                                    swal("ERROR!", response.error, "error");
                                }
                            }
                        });  
                    }
                });
            });
        });
    </script>
</head>
<style>
    body{
        font-family:"Sans Semi" !important;
        background:url('{{asset("images/images/municipal.jpg")}}');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        background-blend-mode:overlay;
    }
    #content, .w3-table{
        font-family:"Sans Light" !important;
    }
    .form-group.row{
        padding:0;
        margin:1px;
    }
    .form-group.row .form-control{
        padding:0 10px;
        margin:0 !important;
    }
    .alert{
        cursor:pointer;
    }
    .pointer{
        cursor:pointer !important;
    }
    .uppercase{
        text-transform:uppercase !important;
    }
    .hidden{
        display:none !important;
    }
    .form-control{
        font-family:"Sans Semi" !important;
        color:#000;
    }
    .add{
        border:1px solid #007BFF;
    }
    .required{
        color:red;
    }
    .body-container{
        border:1px solid #ccc;
        padding:20px !important;
        border-radius:5px;
    }
    .active-window{
        border:1px solid #FFF;
        border-radius:21px;
        color:#aaa !important;
        font-weight:bold;
        border-right:10px solid #FFF;
    }

    table thead tr th{
        border-top:none !important;
    }
    #sidebar{
        position:fixed;
        z-index:1 !important;
        height:100%;
    }
    .w3-modal{
        z-index:10 !important;
    }
    main{
        padding-left:270px;
        background:rgb(255,255,255,0.8);
    }
    #sidebar, #sidebarCollapse{
        background:#188038 !important;
    }
    .bg-green{
        background:#188038 !important;
    }
    .green{
        color:#188038 !important;
    }
    .swal-button--confirm{
        background:#10448C !important;
        color:#FFF;
    }
    .swal-button--cancel{
        background:#EA4335 !important;
        color:#FFF;
    }
    .green:hover{
        color:blue !important;
    }
    .btn-green{
        font-size:40px;
    }
    label{
        color:#000 !important;
    }

    #sidebar li a:hover{
        border:2px solid #000;
        border-radius:20px;
        color:#000 !important;
        background:#FFF;
        font-weight:bold;
    }
    #sidebar li a{
        font-size:13px;
    }
    #header-logo{
        border-radius:50%;
        margin-bottom:10px;
    }

    a.logout-btn{
        border:2px solid #000;
        border-radius:20px;
        background:#FFF;
        font-weight:bold;
    }
    .header-top-title{
        background:#011075;
        padding:5px;
        text-align:center;
        color:#FFF;
        font-size:15px;
        font-weight:bold;
    }
    .main-body{
        padding:10px;
    }
    .card{
        padding:10px;
        border-radius:15px;
    }
    .actions{
        padding:0;
        margin:0;
        text-align:right !important;
    }
    .alert-danger{
        background:#EA4335 !important;
        color:#FFF;
    }
    .bold{
        font-weight:bold !important;
    }

    

    @media screen and (min-width:768px){
        #sidebarCollapse, .nav-top{
            display:none !important;
        }
    }
    @media screen and (max-width:768px){
        #header-logo{
            display:none !important;
        }
        #sidebarCollapse, .nav-top, .footer{
            display:block !important;
        }
        .dropdown-item.logout-btn{
            padding:0;
        }
        main{
            padding-left:0 !important;
        }
        .col-md-12, .card{
            overflow-x:scroll;
            overflow-y:hidden;
        }
        .active-window{
            border:none;
            font-weight:bold;
        }
        #sidebar{
            position:relative;
        }
        #sidebar a,#sidebar span, #sidebar i{
            font-size:11px !important;
        }
    }

    .popover-body{
        font-weight:bold;
        text-align:center;
    }
</style>
<body>
<div id="details-container"></div>
    <div id="app" class="p-0 m-0">
        <nav class="navbar nav-top navbar-expand-md navbar-light bg-white shadow-sm m-0 p-0">
            <div class="col-md-12 w-100 pr-3 m-0">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav-left list-unstyled m-0 p-0 w-75 float-left py-2">
                        <li class="p-0 m-0 float-left">
                            <button type="button" id="sidebarCollapse" class="btn btn-success">
                                <i class="fa fa-bars"></i>
                            </button>
                        </li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="list-unstyled p-0 m-0 w-25 float-left text-right">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @endguest
                    </ul>
            </div>
        </nav>
    </div>
    <div class="wrapper d-flex align-items-stretch mt-0 pt-0">
		<nav id="sidebar">
            <ul class="list-unstyled components mt-3 mb-5">
                <li>
                    <center>
                    <img src="{{asset('images/images/bantug.jpg')}}" id="header-logo" height="110" width="110" class="w3-border" alt="LOGO">
                    </center>
                </li>
                @if(Auth::user()->user_type === "ADMIN")
                <li class="dashboard">
                    <a href="/">
                        <span class="bi bi-house-door-fill"></span> <b class="text">Home</b>
                    </a>
                </li>
                <li class="locations">
                    <a href="/offices">
                        <span class="bi bi-pin-map-fill"></span> <b class="text">Offices</b>
                    </a>
                </li>
                <li class="accounts">
                    <a href="/user-accounts">
                        <span class="bi bi-person-circle"></span> <b class="text">User Accounts</b>
                    </a>
                </li>
                <li class="services">
                    <a href="/services">
                        <span class="bi bi-folder-check"></span> <b class="text">AICS Services</b>
                    </a>
                </li>
                <li class="requests">
                    <a href="/requests">
                        <span class="bi bi-person-check-fill"></span> <b class="text">Approve Beneficiaries</b>
                    </a>
                </li>
                <li class="requests-status">
                    <a href="/requests-status">
                        <span class="bi bi-file-earmark-bar-graph-fill"></span> <b class="text">Request Status</b>
                    </a>
                </li>
                <li class="send-sms">
                    <a href="/send-sms?code=ALL&&option=approved">
                        <span class="bi bi-chat-left-text-fill"></span> <b class="text">Send SMS</b>
                    </a>
                </li>
                <li class="print-requests">
                    <a href="/print-requests?option=approved">
                        <span class="bi bi-printer-fill"></span> <b class="text">Print Request</b>
                    </a>
                </li>
                <li class="archives">
                    <a href="/archived">
                        <span class="fa fa-archive"></span> <b class="text">Archived</b>
                    </a>
                </li>
                @else
                <li class="dashboard">
                    <a href="/">
                        <span class="bi bi-house-door-fill"></span> <b class="text">Home</b>
                    </a>
                </li>
                <li class="my-account">
                    <a href="/my-account">
                        <span class="fa fa-user"></span> <b class="text">Personal Information</b>
                    </a>
                </li>
                <li class="beneficiaries">
                    <a href="/beneficiaries">
                        <span class="fa fa-users"></span> <b class="text">Beneficiaries Information</b>
                    </a>
                </li>
                <li class="requests">
                    <a href="/requests">
                        <span class="bi bi-card-checklist"></span> <b class="text">Request Information</b>
                    </a>
                </li>
                <li class="submit-request">
                    <a href="/submit-requests">
                        <span class="fa fa-send"></span> <b class="text">Submit Requests</b>
                    </a>
                </li>
                <li class="print-reports">
                    <a href="/print-reports">
                        <span class="fa fa-print"></span> <b class="text">Print Reports</b>
                    </a>
                </li>
                <li class="archives">
                    <a href="/archives">
                        <span class="fa fa-archive"></span> <b class="text">Archived</b>
                    </a>
                </li>
                @endif
            </ul>
            <div>
                <center>
                <a class="dropdown-item logout-btn mb-1" href="/my-account">
                    <i class="fa fa-user"></i> My Account
                </a>
                <a class="dropdown-item logout-btn" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
                </center>
            </div>
    	</nav>

        <!-- Page Content  -->
      <div id="content">
          <div class="row">
              <div class="col-md-12" style="background:#FFF;">
                <center>
              <img src="{{asset('images/images/header_angadanan.jpg')}}" alt="ANGADANAN"
              style="width:60%; height:200px;margin-left:300px;">
              </center>
              </div>
          </div>
        <main>
            @include('sweetalert::alert')
            @yield('content')
        </main>
      </div>
		</div>
<!--- * IMG MODAL-->
<div class="w3-modal modal-alert" 
    id="image-form" style="z-index:800 !important;">
    <div class="w3-modal-content w3-animate-top">
        <div class="col-md-12">
            <h5 class="col-md-12 header-title transform pointer pt-4"
                onclick="document.getElementById('image-form').style.display='none'">
                <b>
                    <i class="fa fa-image"></i>
                    Upload Image
                </b>
                <i class="fa fa-times pull-right"></i>
            </h5>
        </div>
        <div class="modal-body p-2">
            <form action="" method="POST" id="image-form" enctype="multipart/form-data">
                @csrf
                <input type="file" name="user_image" id="user_image" class="w3-input">
                <div class="modal-footer p-0">
                    <button type="submit" class="btn btn-primary upload-button">
                        <i class="fa fa-save"></i> Upload Image
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<div class="w3-modal modal-alert" id="loader" style="z-index:1000 !important;">
<center>
    <i class="fa fa-refresh w3-spin green" style="font-size:100px;text-shadow:2px 1px #FFF;"></i>
</center>
</div>
<!--- * IMG MODAL-->
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        $(document).on('submit','#image-form',function(e){
            e.preventDefault();
            var data    = new FormData();
            var files   = $('#user_image').prop('files');
            data.append('_token',_token);
            data.append('user_image',files[0]);

            $.ajax({
                url:'{{route("account-image-upload")}}',
                method:'POST',
                data:data,
                dataType:'json',
                contentType:false,
                cache:false,
                processData:false,
                beforeSend:function(){
                    $('.upload-button').attr('disabled',true);
                    $('.upload-button').html("Uploading...");
                },
                success:function(response){
                    $('.upload-button').attr('disabled',false);
                    $('.upload-button').html("Upload Image");
                    if(response.errors)
                    {
                        var message ='';
                        for(var i = 0; i < response.errors.length; i++)
                        {
                            message += response.errors[i];
                            //alerts('error', message);
                        }
                        swal("ERROR!", message, "error");
                    }
                    else if(response.error)
                    {
                        swal("ERROR!", response.error, "error");
                    }
                    else if(response.success)
                    {
                        swal("SUCCESS!", response.success, "success");
                        document.getElementById('image-form').style.display='none';
                        $('.swal-button').addClass('reload');
                    }
                }
            });
        });

        $(document).on('click', '.reload',function(){
            location.reload();
        });

        var pathname    = window.location.pathname;
        var arr = pathname.split('/');
        var path = arr[1];
        
        if(path === '')
        {
            $('li.dashboard').addClass('active-window');
        }
        else if(path === 'user-accounts')
        {
            $('li.accounts').addClass('active-window');
        }
        else if(path === 'offices')
        {
            $('li.locations').addClass('active-window');
        }
        else if(path === 'services')
        {
            $('li.services').addClass('active-window');
        }
        else if(path === 'my-account')
        {
            $('li.my-account').addClass('active-window');
        }
        else if(path === 'beneficiaries')
        {
            $('li.beneficiaries').addClass('active-window');
        }
        else if(path === 'requests')
        {
            $('li.requests').addClass('active-window');
        }
        else if(path === 'requests-status')
        {
            $('li.requests-status').addClass('active-window');
        }
        else if(path === 'send-sms')
        {
            $('li.send-sms').addClass('active-window');
        }
        else if(path === 'submit-requests')
        {
            $('li.submit-request').addClass('active-window');
        }
        else if(path === 'print-reports')
        {
            $('li.print-reports').addClass('active-window');
        }
        else if(path === 'print-requests')
        {
            $('li.print-requests').addClass('active-window');
        }
        else if(path === 'archives' || path === 'archived')
        {
            $('li.archives').addClass('active-window');
        }
    });
</script>
<script src="{{ asset('sidebar/js/main.js') }}"></script>
</body>
</html>
