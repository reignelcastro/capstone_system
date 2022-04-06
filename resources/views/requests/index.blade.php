@extends('layouts.app')

@section('content')
<?php
    use App\Beneficiary;
    use App\Application;
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
</style>
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        @if($user_type === 'ADMIN')
            
            @if($path === '/requests')
                Approve Beneficiaries' Requests
            @elseif($path === '/requests-status')
                Manage Status of Beneficiaries' Requests
            @elseif($path === '/send-sms')
                Send SMS to Beneficiaries
            @endif
        @else
            @if($path === '/requests')
                Manage Request Information
            @elseif($path === '/beneficiaries')
                Manage Beneficiaries' Information
            @endif
        @endif
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <h5 class="title"><b>
                @if($user_type === 'ADMIN')
                    @if($path === '/requests')
                        Beneficiaries' Requests
                    @elseif($path === '/requests-status' || $path === '/send-sms')
                        <div class="col-md-6 p-0">
                            <button type="button" class="btn w3-border w-50 float-left options" id="approved" data-value="approved">
                                APPROVED
                            </button>
                            <button type="button" class="btn w3-border w-50 float-left options" id="disapproved" data-value="disapproved">
                                DISAPPROVED
                            </button>
                        </div>
                        <br />
                    @endif
                @else
                    @if($path === '/requests')
                        Beneficiaries' Request Information
                    @elseif($path === '/beneficiaries')
                        Beneficiaries' Information
                    @endif
                @endif
            </b></h5>
            <section id="errors"></section>
            <div class="row mt-3 w-100 p-0 m-0">
                <div class="col-md-12 w3-border px-2">
                    <div class="row py-3">
                        <div class="col-md-6">
                            <label><b>Barangay:</b></label>
                            <select name="barangay" id="barangay" class="w-100 p-2 uppercase">
                                <option value="ALL" selected>SHOW ALL</option>
                                @foreach($values AS $value)
                                    @php
                                        $region     = DB::table('regions')
                                                        ->where('regCode',$value->regCode)->first();
                                        $province  = DB::table('provinces')
                                                        ->where('provCode',$value->provCode)->first();
                                        $cm  = DB::table('cm')
                                                        ->where('citymunCode',$value->citymunCode)->first();
                                    @endphp
                                    <option value="{{$value->brgyCode}}">{{strtoupper($value->brgyDesc)}}, {{strtoupper($cm->citymunDesc)}}, {{strtoupper($province->provDesc)}}, {{strtoupper($region->regDesc)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 text-md-right">
                            @if($user_type !== 'ADMIN')
                            <br />
                            <a href="/beneficiaries/new">
                            <button type="button" class="btn w3-border green mt-2">
                                <span class="fa fa-plus"></span> Add
                            </button>
                            </a>
                            @endif
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            @if($user_type !== 'ADMIN')
                                @if(isset($_GET['code']))
                                    @php
                                        $barangay   = $_GET['code'];
                                        $result = Beneficiary::where('barangay', $barangay)
                                                        ->where('is_archived',false)
                                                        ->get();
                                    @endphp
                                    <p class="my-0 py-0"><center>{{count($result)}} result/s found</center></p>
                                @else
                                    @php
                                        $result = Beneficiary::where('is_archived',false)
                                                            ->orderBy('beneficiary_id','DESC')->get();
                                    @endphp
                                    <p class="my-0 py-0"><center>ALL RECORDS: <b>{{count($result)}} result/s found</b></center></p>
                                @endif

                                @if(count($result) > 0)
                                    @php
                                        $list = 1;
                                    @endphp
                                    <table class="mb-5 table table-sm table-condensed" id="dt">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>NAME</th>
                                                @if($path === '/requests')
                                                <th></th>
                                                @endif
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($result as $row)
                                            <tr>
                                                <td>{{$list++}}</td>
                                                <td>
                                                    {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                                </td>
                                                @if($path === '/requests')
                                                @php
                                                    $applications   = Application::where('beneficiary_id',$row->beneficiary_id)
                                                                                ->where('is_completed',false)
                                                                                ->where('is_archived',false)
                                                                                ->get();
                                                @endphp
                                                <td>
                                                    @if(count($applications) > 0 && count($applications) < 2)
                                                        <small class="w3-text-green"><b>{{count($applications)}}</b> on-process application</small>
                                                    @elseif(count($applications) > 1)
                                                        <small class="w3-text-green"><b>{{count($applications)}}</b> on-process applications</small>
                                                    @else
                                                        <small class="w3-text-red">No active application</small>
                                                    @endif
                                                </td>
                                                @endif
                                                <td class="actions">
                                                <!-- <a href="javascript:void(0)" 
                                                    class="green w3-large mx-3 view_details"
                                                    data-beneficiary_id="{{$row->beneficiary_id}}">
                                                    <i class="fa fa-search"></i>
                                                </a> -->
                                                    <a href="javascript:void(0)" onclick="window.open('/view-details/{{$row->beneficiary_id}}')" 
                                                        class="green w3-large mx-3"
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        data-placement="top"  
                                                        data-content="View Details (PDF Format)">
                                                        <i class="fa fa-file"></i>
                                                    </a>
                                                @if($path === '/requests')
                                                    <a href="javascript:void(0)" 
                                                        class="green w3-large mx-3 upload"
                                                        data-beneficiary_id="{{$row->beneficiary_id}}"
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        data-placement="top"   
                                                        data-content="Upload Requirements">
                                                        <i class="fa fa-upload"></i>
                                                    </a>
                                                @elseif($path === '/beneficiaries')
                                                    <a href="/beneficiaries/new?id={{$row->beneficiary_id}}"
                                                        class="green w3-large"
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        data-placement="top"   
                                                        data-content="Edit Information">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" 
                                                        class="green w3-large mx-3 delete"
                                                        data-action="archive"
                                                        data-toggle="popover" 
                                                        data-trigger="hover"
                                                        data-placement="top"   
                                                        data-content="Archive Record"
                                                        data-beneficiary_id="{{$row->beneficiary_id}}">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            @else
                                @if(isset($_GET['code']) && isset($_GET['option']))
                                    @php
                                        $barangay   = $_GET['code'];
                                        $option   = $_GET['option'];

                                        if($option === 'approved')
                                        {
                                            $status     = true;
                                        }
                                        else
                                        {
                                            $status     = false;
                                        }

                                        if($barangay === "ALL")
                                        {
                                            $result   = DB::table('applications')
                                                    ->join('services','applications.services_id','=','services.services_id')
                                                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                    ->select('applications.*','services.*','beneficiaries.lastname','beneficiaries.firstname','beneficiaries.middlename','beneficiaries.suffix')
                                                    ->where('applications.is_approved', $status)
                                                    ->where('applications.is_submitted',true)
                                                    ->where('applications.is_archived',false)
                                                    ->get();
                                        }
                                        else
                                        {
                                            $result   = DB::table('applications')
                                                    ->join('services','applications.services_id','=','services.services_id')
                                                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                    ->select('applications.*','services.*','beneficiaries.lastname','beneficiaries.firstname','beneficiaries.middlename','beneficiaries.suffix')
                                                    ->where('barangay', $barangay)
                                                    ->where('applications.is_approved', $status)
                                                    ->where('applications.is_submitted',true)
                                                    ->where('applications.is_archived',false)
                                                    ->get();
                                        }
                                    @endphp
                                    <p class="my-0 py-0"><center>{{count($result)}} result/s found</center></p>
                                @elseif(isset($_GET['code']) && !isset($_GET['option']))
                                    @php
                                        $barangay   = $_GET['code'];
                                        $result   = DB::table('applications')
                                                    ->join('services','applications.services_id','=','services.services_id')
                                                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                    ->select('applications.*','services.*','beneficiaries.lastname','beneficiaries.firstname','beneficiaries.middlename','beneficiaries.suffix')
                                                    ->where('barangay', $barangay)
                                                    ->where('applications.is_submitted',true)
                                                    ->where('applications.is_archived',false)
                                                    ->get();
                                    @endphp
                                    <p class="my-0 py-0"><center>{{count($result)}} result/s found</center></p>
                                @else
                                    @php
                                        $result   = DB::table('applications')
                                                    ->join('services','applications.services_id','=','services.services_id')
                                                    ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                    ->select('applications.*','services.*','beneficiaries.lastname','beneficiaries.firstname','beneficiaries.middlename','beneficiaries.suffix')
                                                    ->where('applications.is_submitted',true)
                                                    ->where('applications.is_archived',false)
                                                    ->get();
                                    @endphp
                                    <p class="my-0 py-0"><center>ALL RECORDS: <b>{{count($result)}} result/s found</b></center></p>
                                @endif

                                @if(count($result) > 0)
                                    @php
                                        $list = 1;
                                    @endphp
                                    <table class="mb-5 table table-sm table-condensed" id="dt">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>NAME</th>
                                                <th>REQUEST</th>
                                                <th>DATE 
                                                    @if($path === '/requests-status')
                                                    LAST UPDATED
                                                    @endif
                                                </th>
                                                <th></th>
                                                <th class="text-center">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($result as $row)
                                            <tr>
                                                <td>{{$list++}}</td>
                                                <td>
                                                    {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                                </td>
                                                <td>
                                                    {{strtoupper($row->aics_services)}}
                                                </td>
                                                <td>
                                                    {{strtoupper($row->updated_at)}}
                                                </td>
                                                <td class="text-center">
                                                    @if(is_null($row->is_approved))
                                                        PENDING
                                                    @elseif($row->is_approved == true)
                                                        <b class="fa fa-check w-100 p-1 w3-green"></b>
                                                    @else
                                                        <b class="fa fa-remove w-100 p-1 w3-red"></b>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($path === '/send-sms')
                                                        <a href="javascript:void(0)" 
                                                            class="green w3-large mx-3 send_sms"
                                                            data-beneficiary_id="{{$row->beneficiary_id}}"
                                                            data-services_id="{{$row->services_id}}"
                                                            data-action="@if(isset($_GET['option'])){{$_GET['option']}}@endif"
                                                            data-aics_services="{{strtoupper($row->aics_services)}}">
                                                            <i class="fa fa-envelope"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)" onclick="window.open('/view-details/{{$row->beneficiary_id}}')" 
                                                            class="green w3-large mx-3">
                                                            <i class="fa fa-file"></i>
                                                        </a>
                                                         <a href="javascript:void(0)" 
                                                            class="green w3-large mx-3 view_details"
                                                            data-beneficiary_id="{{$row->beneficiary_id}}"
                                                            data-services_id="{{$row->services_id}}"
                                                            data-with_requirements="yes">
                                                            <i class="fa fa-search"></i>
                                                        </a>
                                                        <!--
                                                        <a href="javascript:void(0)" onclick="window.open('/view-details/{{$row->beneficiary_id}}')" 
                                                            class="green w3-large mx-3">
                                                            <i class="fa fa-search"></i>
                                                        </a> -->

                                                        <a href="javascript:void(0)" 
                                                            class="green w3-large mx-3 action"
                                                            data-action="approve"
                                                            data-application_id="{{$row->application_id}}">
                                                            <i class="fa fa-check"></i>
                                                        </a>

                                                        <a href="javascript:void(0)" 
                                                            class="green w3-large mx-3 action"
                                                            data-action="disapprove"
                                                            data-application_id="{{$row->application_id}}">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            @endif
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

<div class="w3-modal pt-2" id="uploads-modal">
    <div class="w3-modal-content w3-animate-zoom card p-1 px-0" style="width:30%;">
        <div class="container p-0">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn green" 
                        onclick="document.getElementById('uploads-modal').style.display='none';">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>
            </div>
            <hr class="my-0 mb-4">
            <div class="row p-0 m-0">
                <div class="col-md-12 m-0 mb-1">
                    <h6><b>Upload <span class="uppercase uploads_requirement_type"></span> file</b></h6>
                    <form action="" method="POST" id="uploads-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="uploads_beneficiary_id" id="uploads_beneficiary_id" class="w3-input" readonly>
                        <input type="hidden" name="uploads_services_id" id="uploads_services_id" class="w3-input" readonly>
                        <input type="hidden" name="uploads_aics_services" id="uploads_aics_services" class="w3-input" readonly>
                        <input type="hidden" name="uploads_upload_id" id="uploads_upload_id" class="w3-input" readonly>
                        <input type="hidden" name="uploads_requirement_type" id="uploads_requirement_type" class="w3-input" readonly>
                        <input type="file" name="uploaded_file" id="uploaded_file" class="w3-input">
                        <div class="modal-footer p-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Upload file
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="w3-modal pointer" id="image-modal" onclick="document.getElementById('image-modal').style.display='none';">
    <div class="w3-modal-content w3-transparent p-5">
        <div class="row justify-content-center">
        <div class="col-md-8" id="file-container"></div>
        </div>
    </div>
</div>
<script>
function check(_token,beneficiary_id){
    $.ajax({
        url:'{{route("services-check")}}',
        method:'POST',
        data:{
            beneficiary_id:beneficiary_id,
            _token:_token
        },
        dataType:'json',
        success:function(response){
            if(response.success)
            {
                document.getElementById('upload-modal').style.display='block';
                $('#upload-container').html(response.html);
            }
            else if(response.error)
            {
                swal("ERROR!", response.error, "error");
            }
        }
    });
}

function verify(_token,beneficiary_id, services_id, aics_services){
    $.ajax({
        url:'{{route("services-verify")}}',
        method:'POST',
        data:{
            beneficiary_id:beneficiary_id,
            services_id:services_id,
            aics_services:aics_services,
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
                if(response.status === 'new')
                {
                    verify(_token,beneficiary_id, services_id, aics_services);
                }
                else if(response.status === 'existing')
                {
                    $('#upload-container').html(response.html);
                }
            }
            else if(response.error)
            {
                swal("ERROR!", response.error, "error");
            }
        }
    });
}
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        var pathname    = window.location.pathname;
        var arr = pathname.split('/');
        var path = arr[1];
        $(document).on('change','#barangay', function(){
            var value = $(this).val();
            
            if(path === 'send-sms')
            {
                if(value === 'ALL')
                {
                    window.location.href = '/'+path+'?code=ALL&&option=approved';
                }
                else
                {
                    window.location.href = '/'+path+'?code='+value+'&&option=approved';
                }
            }
            else
            {
                if(value === 'ALL')
                {
                    window.location.href = '/'+path;
                }
                else
                {
                    window.location.href = '/'+path+'?code='+value;
                }
            }
        });

        $(document).on('click','.options', function(){
            var value = $(this).data('value');
            var code = $('#barangay').val();
            window.location.href = '/'+path+'?code='+code+'&&option='+value;
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

        $(document).on('click', '.upload', function (e) {
            var beneficiary_id  = $(this).data('beneficiary_id');
            var services_id     = $(this).data('services_id');
            check(_token, beneficiary_id);
        });
        

        $(document).on('click', '.select_service', function () {
            var services_id = $(this).data('services_id');
            var aics_services = $(this).data('aics_services');
            var beneficiary_id = $(this).data('beneficiary_id');
            verify(_token, beneficiary_id, services_id, aics_services);
        });

        $(document).on('click', '.upload_file', function () {
            var upload_id = $(this).data('upload_id');
            var requirement_type = $(this).data('requirement_type');
            var services_id = $(this).data('services_id');
            var aics_services = $(this).data('aics_services');
            var beneficiary_id = $(this).data('beneficiary_id');

            
            $('#uploads_upload_id').val(upload_id);
            $('#uploads_requirement_type').val(requirement_type);
            $('#uploads_services_id').val(services_id);
            $('#uploads_aics_services').val(aics_services);
            $('#uploads_beneficiary_id').val(beneficiary_id);

            $('.uploads_requirement_type').html(requirement_type);
            document.getElementById('uploads-modal').style.display='block';
        });

        $(document).on('submit','#uploads-form',function(e){
            e.preventDefault();
            var data    = new FormData();
            var files   = $('#uploaded_file').prop('files');
            var upload_id = $('#uploads_upload_id').val();
            var requirement_type = $('#uploads_requirement_type').val();

            var services_id = $('#uploads_services_id').val();
            var beneficiary_id = $('#uploads_beneficiary_id').val();
            var aics_services = $('#uploads_aics_services').val();

            data.append('_token',_token);
            data.append('uploaded_file',files[0]);
            data.append('upload_id',upload_id);
            data.append('requirement_type',requirement_type);

            $.ajax({
                url:'{{route("upload-files")}}',
                method:'POST',
                data:data,
                dataType:'json',
                contentType:false,
                cache:false,
                processData:false,
                beforeSend:function(){
                    loader();
                },
                success:function(response){
                    loaderx();
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
                        verify(_token, beneficiary_id, services_id, aics_services);
                        $('#uploads-form')[0].reset();
                        document.getElementById('uploads-modal').style.display='none';
                    }
                }
            });
        });


        $(document).on('click', '.add_to_applications', function () {
            var services_id = $(this).data('services_id');
            var beneficiary_id = $(this).data('beneficiary_id');

            swal({
                title: "Please confirm.",
                text: "Are you sure you want to add this to application?",
                buttons: true,
                buttons:['Cancel','Yes'],
                dangerMode: true,
                })
                .then((isConfirmed) => {
                if (isConfirmed) 
                {
                    $.ajax({
                        url:'{{route("application-save")}}',
                        method:'POST',
                        data:{
                            beneficiary_id:beneficiary_id,
                            services_id:services_id,
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
@endif
@endsection
