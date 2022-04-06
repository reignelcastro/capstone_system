@extends('layouts.app')

@section('content')
<?php
    use App\User;
    use App\Beneficiary;
    use App\Application;
    use Illuminate\Support\Facades\DB;


    $location_id    = Auth::user()->location_id;
    $type           = Auth::user()->user_type;
    $id             = Auth::user()->id;
    $u      = User::where('id',$id)->first();


    $values     = DB::select('SELECT * FROM brgy WHERE brgyCode IN(
                    SELECT barangay FROM beneficiaries
                    )');
    $uri =  $_SERVER['REQUEST_URI'];
    $url = parse_url($uri);
    $path   = $url['path'];

    if($path === '/submit-requests')
    {
        $result     = DB::table('applications')
                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                    ->join('services','applications.services_id','=','services.services_id')
                    ->select('lastname','firstname','middlename','suffix','aics_services','services.services_id','applications.*')
                    ->where('applications.is_archived',false)
                    ->get();
    }
    elseif($path === '/print-reports')
    {
        if(isset($_GET['from']) && isset($_GET['to']))
        {
            $from   = $_GET['from'];
            $to     = $_GET['to'];
            $result     = DB::table('applications')
                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                    ->join('services','applications.services_id','=','services.services_id')
                    ->select('lastname','firstname','middlename','suffix','aics_services','services.services_id','applications.*')
                    ->where('applications.is_submitted',true)
                    ->where('applications.is_archived',false)
                    ->whereBetween('applications.updated_at',[$from, $to])
                    ->get();
        }
        else
        {
            $result     = DB::table('applications')
                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                    ->join('services','applications.services_id','=','services.services_id')
                    ->select('lastname','firstname','middlename','suffix','aics_services','services.services_id','applications.*')
                    ->where('applications.is_submitted',true)
                    ->where('applications.is_archived',false)
                    ->get();
        }
    }
    else
    {
        if(isset($_GET['option']) && !isset($_GET['from']) && !isset($_GET['to']))
        {
            $option = $_GET['option'];

            if($option === 'approved')
            {
                $condition     = true;
            }
            else
            {
                $condition     = false;
            }

            $result     = DB::table('applications')
                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                    ->join('services','applications.services_id','=','services.services_id')
                    ->select('lastname','firstname','middlename','suffix','aics_services','services.services_id','applications.*')
                    ->where('applications.is_approved',$condition)
                    ->where('applications.is_archived',false)
                    ->get();
        }
        elseif(isset($_GET['option']) && isset($_GET['from']) && isset($_GET['to']))
        {
            $option = $_GET['option'];

            if($option === 'approved')
            {
                $condition     = true;
            }
            else
            {
                $condition     = false;
            }

            $from   = $_GET['from'];
            $to     = $_GET['to'];
            $result     = DB::table('applications')
                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                    ->join('services','applications.services_id','=','services.services_id')
                    ->select('lastname','firstname','middlename','suffix','aics_services','services.services_id','applications.*')
                    ->where('applications.is_approved',$condition)
                    ->where('applications.is_archived',false)
                    ->whereBetween('applications.updated_at',[$from, $to])
                    ->get();
        }
        else
        {
            $result     = DB::table('applications')
                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                    ->join('services','applications.services_id','=','services.services_id')
                    ->select('lastname','firstname','middlename','suffix','aics_services','services.services_id','applications.*')
                    ->where('applications.is_submitted',true)
                    ->where('applications.is_archived',false)
                    ->get();
        }
    }
    
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
        @if($user_type === 'ADMIN')
            @if($path === '/print-reports' || $path === '/print-requests')
                Print Request
            @else
                Approve Beneficiaries' Requests
            @endif
        @else
            @if($path === '/submit-requests')
                Submit Request
            @else
                Print Reports
            @endif
        @endif
    </div>
</div>
<div class="container p-0">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body p-0">
            <h5 class="title"><b>
                @if($user_type === 'ADMIN')
                    @if($path === '/print-reports' || $path === '/print-requests')
                        <div class="col-md-6 p-0">
                            <button type="button" class="btn w3-border w-50 float-left options" id="approved" data-value="approved">
                                APPROVED
                            </button>
                            <button type="button" class="btn w3-border w-50 float-left options" id="disapproved" data-value="disapproved">
                                DISAPPROVED
                            </button>
                        </div>
                        <br />
                    @else
                        Beneficiaries' Requests
                    @endif
                @else
                    @if($path === '/submit-requests')
                        Submit Request
                    @else
                        Print Reports
                    @endif
                @endif
            </b></h5>
            <section id="errors"></section>
            <div class="row mt-3 w-100 p-0 m-0">
                <div class="col-md-12 w3-border">
                    <div class="row">
                        <div class="col-md-12 pt-3">
                                @if($user_type === 'ADMIN')
                                    <form action="" method="GET">
                                        <div class="row px-3 my-3">
                                            <div class="col-md-3 p-0 m-0">
                                                <b>FROM:</b>
                                                <input type="date" name="from" id="from" class="w-75" required>
                                            </div>
                                            <div class="col-md-3 p-0 m-0">
                                                <b>TO:</b>
                                                <input type="date" name="to" id="to" class="w-75" required>
                                            </div>
                                            <input type="hidden" name="option" id="option" class="w-75">
                                            <div class="col-md-6 p-0 m-0">
                                                <button type="submit" class="btn btn-primary p-0 py-1 px-4"
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        data-placement="top"   
                                                        data-content="Search">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <a href="/print-reports">
                                                    <button type="button" class="btn btn-primary p-0 py-1 px-4"
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        data-placement="top"   
                                                        data-content="Reset Search Inputs">
                                                        <i class="fa fa-refresh"></i>
                                                    </button>
                                                </a>
                                                <button type="button" class="btn btn-primary p-0 py-1 px-4" onclick="printDiv();"
                                                    data-toggle="popover" 
                                                    data-trigger="hover"
                                                    data-placement="top"   
                                                    data-content="Print">
                                                    <i class="fa fa-print"></i>
                                                </button>      
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    @if($path === '/submit-requests')
                                    <p class="text-right my-0 p-0">
                                        <button type="button" class="btn btn-primary px-5 bold submit-requests">
                                            SUBMIT
                                        </button>
                                    </p>
                                    @else
                                    <form action="" method="GET">
                                        <div class="row px-3 my-3">
                                            <div class="col-md-3 p-0 m-0">
                                                <b>FROM:</b>
                                                <input type="date" name="from" id="from" class="w-75" required>
                                            </div>
                                            <div class="col-md-3 p-0 m-0">
                                                <b>TO:</b>
                                                <input type="date" name="to" id="to" class="w-75" required>
                                            </div>
                                            <div class="col-md-6 p-0 m-0">
                                                <button type="submit" class="btn btn-primary p-0 py-1 px-4"
                                                    data-toggle="popover" 
                                                    data-trigger="hover"
                                                    data-placement="top"   
                                                    data-content="Search">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <a href="/print-reports"
                                                    data-toggle="popover" 
                                                    data-trigger="hover"
                                                    data-placement="top"   
                                                    data-content="Reset Search Inputs">
                                                    <button type="button" class="btn btn-primary p-0 py-1 px-4">
                                                        <i class="fa fa-refresh"></i>
                                                    </button>
                                                </a>
                                                <button type="button" class="btn btn-primary p-0 py-1 px-4" onclick="printDiv();"
                                                    data-toggle="popover" 
                                                    data-trigger="hover"
                                                    data-placement="top"   
                                                    data-content="Print">
                                                    <i class="fa fa-print"></i>
                                                </button>      
                                            </div>
                                        </div>
                                    </form>
                                    @endif
                                @endif
                                <div id="printArea">
                                @if(count($result) > 0)
                                <center>
                                @if(isset($_GET['option']))
                                    <h6 class="mt-5"><b>{{strtoupper($_GET['option'])}} REQUESTS</b></h6>
                                @endif
                                <p class="mb-3">
                                    @if(isset($from) && isset($to))
                                        Search from <b>{{date('F d, Y', strtotime($from))}}</b> to <b>{{date('F d, Y', strtotime($to))}}</b> <br />
                                    @endif
                                    <b>{{count($result)}}</b> record/s found.
                                </p>
                                </center>
                                <table class="mb-5 table table-sm table-condensed w3-small" @if($path === '/submit-requests') id="dt" @endif>
                                    <thead>
                                        <tr>
                                            <th>NAME</th>
                                            <th>REQUEST</th>
                                            @if($user_type === 'ADMIN')
                                                @if(isset($_GET['option']) && $_GET['option'] === 'approved')
                                                    <th>DATE APPROVED</th>
                                                @else
                                                    <th>DATE UPDATED</th>
                                                @endif
                                                <th>STATUS</th>
                                            @else
                                                <th>CREATED AT</th>
                                                <th>SUBMITTED AT</th>
                                                <th>BY</th>
                                                @if($path === '/submit-requests')
                                                <th>STATUS</th>
                                                <th class="actions">
                                                    <input type="checkbox" id="check_all" class="pointer">
                                                </th>
                                                <th class="actions">
                                                </th>
                                                @endif
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($result as $row)
                                    @php
                                        $by     = User::where('id',$row->id)->first();
                                        $added_by = strtoupper($by->name);
                                    @endphp
                                        <tr>
                                            <td>
                                                {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                            </td>
                                            <td>
                                                {{strtoupper($row->aics_services)}}
                                            </td>
                                                @if($user_type ==='ADMIN')
                                                    <td>
                                                        @if($row->is_submitted == true)
                                                            {{date('F j, Y h:i:sa',strtotime($row->updated_at))}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($row->is_approved == true)
                                                            APPROVED
                                                        @else
                                                            DISAPPROVED
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>
                                                        {{date('F j, Y h:i:sa',strtotime($row->created_at))}}
                                                    </td>
                                                    <td>
                                                        @if($row->is_submitted == true)
                                                            {{date('F j, Y h:i:sa',strtotime($row->updated_at))}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$added_by}}
                                                    </td>
                                                    @if($path === '/submit-requests')
                                                    <td id="status_{{$row->application_id}}">
                                                        @if($row->is_submitted == true)
                                                            <small class="w3-text-green"><i class="fa fa-check"></i> Submitted</small>
                                                        @else
                                                            <small class="w3-text-red"><i class="fa fa-remove"></i> Not submitted</small>
                                                        @endif
                                                    </td>
                                                    <td class="actions">
                                                        @if($row->is_submitted == false)
                                                            <input type="checkbox" class="pointer mt-2" name="actions" value="{{$row->application_id}}">
                                                        @endif
                                                    </td>
                                                    <td class="actions">
                                                        <a href="javascript:void(0)" 
                                                            class="green w3-large mx-3 view_details"
                                                            data-beneficiary_id="{{$row->beneficiary_id}}"
                                                            data-with_requirements="yes"
                                                            data-services_id="{{$row->services_id}}"
                                                            data-toggle="popover" 
                                                            data-trigger="hover"
                                                            data-placement="top"   
                                                            data-content="View Information and Uploaded Requirements">
                                                            <i class="fa fa-search"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 archive"
                                                                data-action="archive"
                                                                data-application_id="{{$row->application_id}}"
                                                                data-toggle="popover" 
                                                            data-trigger="hover"
                                                            data-placement="top"   
                                                            data-content="Archive Record">
                                                                <i class="fa fa-remove"></i>
                                                    </td>
                                                @endif
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <center>
                                <p>
                                    @if(isset($from) && isset($to))
                                        Search from <b>{{date('F d, Y', strtotime($from))}}</b> to <b>{{date('F d, Y', strtotime($to))}}</b> <br />
                                    @endif
                                    
                                    <b>No result found.</b>
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
</div>
<div class="w3-modal pt-2" id="upload-modal">
    <div class="w3-modal-content w3-animate-zoom card p-1 px-0" style="width:40%;">
        <div class="container p-0">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn green" 
                        onclick="document.getElementById('upload-modal').style.display='none';">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>
            </div>
            <hr class="my-0 mb-4">
            <div class="row p-0 m-0">
                <div class="col-md-12 m-0 mb-1">
                    <div id="upload-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(isset($from) && isset($to))
<script>
    $(function(){
        $('#from').val('{{$from}}');
        $('#to').val('{{$to}}');
    });
</script>
@endif
<script>
function printDiv() 
{
    var div = document.getElementById('printArea');
    var print = window.open('','Print Window');
    var img   = '<center><img src="{{asset('images/header.png')}}" alt="LOGO" style="width:100%;max-width:100%;" class="m-0"></center><br />';
    var prepared_by = 'PREPARED BY: <br /><br /><br /><p><b>{{strtoupper($u->name)}}</b></p><p><small>{{strtoupper($u->user_type)}}</small></p><p><small>{{date("F j, Y h:i:sa")}}</small></p>';
    print.document.open();
    print.document.write('<html><head><link href="{{ asset('css/app.css') }}" rel="stylesheet"><link href="{{ asset('css/w3.css') }}" rel="stylesheet"><link href="{{ asset('fonts/app.css') }}" rel="stylesheet"><link href="{{ asset('awesome/css/font-awesome.min.css') }}" rel="stylesheet"></head><style>@media print{body{margin:0 !important;}}body{padding:0 !important;}.main{font-size:10px !important;}table{font-size:11px !important;}p{line-height:1.2 !important;padding:0;margin:0;}.dataTables_info,.dataTables_length,.dataTables_filter,.dataTables_paginate{display:none !important;}</style><body onload="window.print()" class="p-5">'+img+'<br /><main>'+div.innerHTML+'<br /><br /><br />'+prepared_by+'</main></body></html>');
    print.document.close();
}

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

        $(document).on('click','#check_all', function(){
            var is_checked  = $(this).is(":checked");

            if(is_checked){
                $('input[name="actions"]').prop('checked',true);
            }else{
                $('input[name="actions"]').prop('checked',false);
            }
        });

        $(document).on('click','.submit-requests', function(){
            var id = $('input[name=actions]:checked');

            if(id.length > 0){
            swal({
                title: "Please confirm!",
                text: "Are you sure you want to submit selected request information?",
                buttons: true,
                buttons:['Cancel','YES'],
                dangerMode: true,
                })
                .then((willSubmit) => {
                if (willSubmit) 
                {
                    id.each(function(data){
                        var application_id  = $(this).val();

                        $.ajax({
                            url:'{{route("submit-application")}}',
                            method:'POST',
                            data:{
                                application_id:application_id,
                                _token:_token
                            },
                            dataType:'json',
                            beforeSend:function(){
                                loader();
                            },
                            success:function(response){
                                loaderx();
                                if(response.success)
                                {
                                    $('#status_'+application_id).html('<small class="w3-text-green"><i class="fa fa-check"></i> Submitted</small>');
                                    $('input[name="actions"][value="'+application_id+'"]').hide();
                                    swal("SUCCESS!", "Selected request/s successfully submitted!", "success");
                                    // setInterval(function(){
                                    //     //location.reload();
                                    // },1500);
                                }
                                else if(response.error)
                                {
                                    swal("ERROR!", response.error, "error");
                                }
                            }
                        });
                    });
                }
            });
            }
            else
            {
                swal("ERROR!", "Select a record before submitting.", "error");
            }
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

        $(document).on('click','.options', function(){
            var value = $(this).data('value');
            window.location.href = '/'+path+'?option='+value;
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
        $('#option').val(option);
        $('#'+option).addClass('bg-primary w3-text-white bold');
    });
</script>
@endif
@endsection
