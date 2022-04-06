@extends('layouts.app')

@section('content')
<?php
    use App\Beneficiary;
    use App\Application;
    use App\Service;
    use Illuminate\Support\Facades\DB;


    $values     = DB::select('SELECT * FROM brgy WHERE brgyCode IN(
                    SELECT barangay FROM beneficiaries
                    )');
    $uri =  $_SERVER['REQUEST_URI'];
    $url = parse_url($uri);
    $path   = $url['path'];
    $user_type  = Auth::user()->user_type;
?>
<style>
    #dt thead tr th, #dt tbody tr td{
        padding:1px 5px !important;
    }
    #dt_filter{
        float:left;
    }
</style>
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        ARCHIVED
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn w3-border w-50 float-left options" id="requests" data-value="requests">
                        APPROVAL ARCHIVED
                    </button>
                    <button type="button" class="btn w3-border w-50 float-left options" id="services" data-value="services">
                        AICS SERVICES ARCHIVED
                    </button>
                </div>
            </div>
            <div class="row mt-3 w-100 p-0 m-0">
                <div class="col-md-12 w3-border">
                    <div class="row">
                        <div class="col-md-12 pt-3">
                            @if(isset($_GET['option']))
                                @php
                                    $option     = $_GET['option'];
                                @endphp

                                @if($option === 'services')
                                    @php
                                        $result     = Service::where('is_archived',true)->orderBy('services_id','DESC')->get();
                                    @endphp

                                    @if(count($result) > 0)
                                        <table class="table my-3" id="dt">
                                            <thead>
                                                <tr>
                                                    <th>AICS SERVICES</th>
                                                    <th class="actions">ACTIONS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($result as $row)
                                                    <tr>
                                                        <td>
                                                            {{strtoupper($row->aics_services)}}
                                                        </td>
                                                        <td class="actions">
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 delete_forever"
                                                                data-action="restore"
                                                                data-services_id="{{$row->services_id}}">
                                                                <i class="fa fa-refresh"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 delete_forever"
                                                                data-action="delete"
                                                                data-services_id="{{$row->services_id}}">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <center>
                                            <p>
                                                No record found.
                                            </p>
                                        </center>
                                    @endif
                                @elseif($option === 'requests')
                                    <div class="row mb-5">
                                        <div class="col-md-8">
                                            <button type="button" class="btn w3-border w-50 float-left status" id="approved" data-value="approved" data-get="{{$_GET['option']}}">
                                                APPROVED
                                            </button>
                                            <button type="button" class="btn w3-border w-50 float-left status" id="disapproved" data-value="disapproved" data-get="{{$_GET['option']}}">
                                                DISAPPROVED
                                            </button>
                                        </div>
                                    </div>
                                    @php
                                    if(isset($_GET['status']))
                                    {
                                        if($_GET['status'] === 'approved')
                                        {
                                            $result     = DB::table('applications')
                                                ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                ->join('services','applications.services_id','=','services.services_id')
                                                ->select('beneficiaries.beneficiary_id','services.services_id','lastname','firstname','middlename','suffix','aics_services','applications.application_id','applications.created_at','applications.is_submitted','applications.updated_at','applications.is_approved')
                                                ->where('applications.is_archived',true)
                                                ->where('applications.is_approved',true)
                                                ->get();
                                        }
                                        else
                                        {
                                            $result     = DB::table('applications')
                                                ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                ->join('services','applications.services_id','=','services.services_id')
                                                ->select('beneficiaries.beneficiary_id','services.services_id','lastname','firstname','middlename','suffix','aics_services','applications.application_id','applications.created_at','applications.is_submitted','applications.updated_at','applications.is_approved')
                                                ->where('applications.is_archived',true)
                                                ->where('applications.is_approved',false)
                                                ->get();
                                        }
                                        
                                    }
                                    else
                                    {
                                        $result     = DB::table('applications')
                                                ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                ->join('services','applications.services_id','=','services.services_id')
                                                ->select('beneficiaries.beneficiary_id','services.services_id','lastname','firstname','middlename','suffix','aics_services','applications.application_id','applications.created_at','applications.is_submitted','applications.updated_at','applications.is_approved')
                                                ->where('applications.is_archived',true)
                                                ->get();
                                    }
                                    @endphp

                                    @if(count($result) > 0)
                                        <table class="table my-3" id="dt">
                                            <thead>
                                                <tr>
                                                    <th>NAME</th>
                                                    <th>STATUS</th>
                                                    <th class="actions"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($result as $row)
                                                    <tr>
                                                        <td>
                                                            {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                                        </td>
                                                        <td>
                                                            @if($row->is_approved == true)
                                                            APPROVED
                                                            @else
                                                            DISAPPROVED
                                                            @endif
                                                        </td>
                                                        <td class="actions">
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 view_details"
                                                                data-with_requirements="yes"
                                                                data-beneficiary_id="{{$row->beneficiary_id}}"
                                                                data-services_id="{{$row->services_id}}">
                                                                <i class="fa fa-search"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 archive"
                                                                data-action="restore"
                                                                data-application_id="{{$row->application_id}}">
                                                                <i class="fa fa-refresh"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 trash"
                                                                data-application_id="{{$row->application_id}}">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <center>
                                            <p>
                                                No record found.
                                            </p>
                                        </center>
                                    @endif
                                @endif
                            @else
                                <center>
                                    <p>
                                        <b>
                                        ARCHIVED RECORDS
                                        </b>
                                    </p>
                                </center>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        var pathname    = window.location.pathname;
        var arr = pathname.split('/');
        var path = arr[1];

        $(document).on('change','#barangay', function(){
            var value = $(this).val();

            if(value === 'ALL')
            {
                window.location.href = '/'+path;
            }
            else
            {
                window.location.href = '/'+path+'?code='+value;
            }
        });

        $(document).on('click','.options', function(){
            var value = $(this).data('value');
            window.location.href = '/'+path+'?option='+value;
        });

        $(document).on('click','.status', function(){
            var value   = $(this).data('value');
            var get     = $(this).data('get');
            window.location.href = '/'+path+'?option='+get+'&&status='+value;
        });

        $(document).on('click', '.delete', function () {
            var beneficiary_id = $(this).data('beneficiary_id');
            var action         = $(this).data('action');
            swal({
                title: "Please confirm!",
                text: "Are you sure you want to "+action+" this beneficiary's information?",
                icon: "warning",
                buttons: true,
                buttons:['Cancel','Yes'],
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("beneficiary-archive")}}',
                        method:'POST',
                        data:{
                            beneficiary_id:beneficiary_id,
                            _token:_token
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

        $(document).on('click', '.remove', function () {
            var beneficiary_id = $(this).data('beneficiary_id');
            swal({
                title: "Please confirm!",
                text: "Are you sure you want to permanently delete this beneficiary's information?",
                icon: "warning",
                buttons: true,
                buttons:['Cancel','Yes'],
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("beneficiary-delete")}}',
                        method:'POST',
                        data:{
                            beneficiary_id:beneficiary_id,
                            _token:_token
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

        $(document).on('click', '.archive', function () {
            var application_id = $(this).data('application_id');
            var action          = $(this).data('action');
            swal({
                text: "Are you sure you want to "+action+" this request information?",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("application-archive")}}',
                        method:'POST',
                        data:{
                            application_id:application_id,
                            _token:_token
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

        $(document).on('click', '.trash', function () {
            var application_id = $(this).data('application_id');
            swal({
                text: "Are you sure you want to permanently delete this request information?",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("application-trash")}}',
                        method:'POST',
                        data:{
                            application_id:application_id,
                            _token:_token
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

        $(document).on('click', '.delete_forever', function () {
            var services_id = $(this).data('services_id');
            var action      = $(this).data('action');
            swal({
                title: "",
                text: "Are you sure you want to "+action+" this record?",
                icon: "warning",
                buttons: true,
                buttons:['Cancel','Yes'],
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("services-delete")}}',
                        method:'POST',
                        data:{
                            services_id:services_id,
                            action:action,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                swal("SUCCESS!", response.success, "success");
                                loader();
                                setInterval(function(){
                                    window.location.href = "/services";
                                },3000);
                            }
                            else if(response.error)
                            {
                                swal("ERROR!", response.error, "error");
                            }
                            else if(response.errors)
                            {
                                swal("ERROR!", "Data security error. This record is not allowed to be deleted.", "error");
                            }
                        }
                    });   
                }
            });
        });
    });
</script>
@if(isset($_GET['code']))
<script>
    $(function(){
        var code = "{{$_GET['code']}}";
        $('#barangay').val(code);
    });
</script>
@endif

@if(isset($_GET['option']))
<script>
    $(function(){
        var option = "{{$_GET['option']}}";
        $('#'+option).addClass('bg-primary w3-text-white bold');
    });
</script>
@else
<script>
    $(function(){
        $('#beneficiaries').addClass('bg-primary w3-text-white bold');
    });
</script>
@endif

@if(isset($_GET['status']))
<script>
    $(function(){
        var status = "{{$_GET['status']}}";
        $('#'+status).addClass('bg-primary w3-text-white bold');
    });
</script>
@else
<script>
    $(function(){
        $('#beneficiaries').addClass('bg-primary w3-text-white bold');
    });
</script>
@endif
@endsection
