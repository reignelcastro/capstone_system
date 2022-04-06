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
                <div class="col-md-8 pr-5">
                    <button type="button" class="btn w3-border w-50 float-left options" id="beneficiaries" data-value="beneficiaries">
                        Beneficiaries Information
                    </button>
                    <button type="button" class="btn w3-border w-50 float-left options" id="requests" data-value="requests">
                        Request Information
                    </button>
                </div>
                <div class="col-md-4">
                    <label><b>Barangay :</b></label>
                    <select name="barangay" id="barangay" class="w-75 p-2 uppercase">
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
            </div>
            <div class="row mt-3 w-100 p-0 m-0">
                <div class="col-md-12 w3-border">
                    <div class="row">
                        <div class="col-md-12 pt-3">
                            @if(isset($_GET['option']))
                                @php
                                    $option     = $_GET['option'];
                                @endphp

                                @if($option === 'beneficiaries')
                                    @php
                                        $result     = Beneficiary::where('is_archived',true)->orderBy('lastname','ASC')->get();
                                    @endphp

                                    @if(count($result) > 0)
                                        <table class="table my-3" id="dt">
                                            <thead>
                                                <tr>
                                                    <th>NAME</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($result as $row)
                                                    <tr>
                                                        <td>
                                                            {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                                        </td>
                                                        <td class="actions">
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 delete"
                                                                data-action="restore"
                                                                data-beneficiary_id="{{$row->beneficiary_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Restore Record">
                                                                <i class="fa fa-refresh"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 remove"
                                                                data-action="restore"
                                                                data-beneficiary_id="{{$row->beneficiary_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Delete Permanently">
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
                                                No archived beneficiary records.
                                            </p>
                                        </center>
                                    @endif
                                @else
                                @php
                                $result     = DB::table('applications')
                                                ->join('beneficiaries','applications.beneficiary_id','=','beneficiaries.beneficiary_id')
                                                ->join('services','applications.services_id','=','services.services_id')
                                                ->select('lastname','firstname','middlename','suffix','aics_services','applications.application_id','applications.created_at','applications.is_submitted','applications.updated_at')
                                                ->where('applications.is_archived',true)
                                                ->get();
                                    @endphp

                                    @if(count($result) > 0)
                                        <table class="table my-3" id="dt">
                                            <thead>
                                                <tr>
                                                    <th>NAME</th>
                                                    <th>REQUEST</th>
                                                    <th>CREATED AT</th>
                                                    <th>SUBMITTED AT</th>
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
                                                            {{strtoupper($row->aics_services)}}
                                                        </td>
                                                        <td>
                                                            {{date('F j, Y h:i:sa',strtotime($row->created_at))}}
                                                        </td>
                                                        <td>
                                                            @if($row->is_submitted == true)
                                                                {{date('F j, Y h:i:sa',strtotime($row->updated_at))}}
                                                            @endif
                                                        </td>
                                                        <td class="actions">
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 archive"
                                                                data-action="restore"
                                                                data-application_id="{{$row->application_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Restore Record">
                                                                <i class="fa fa-refresh"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 trash"
                                                                data-application_id="{{$row->application_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Delete Permanently">
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
                                                No archived request information.
                                            </p>
                                        </center>
                                    @endif
                                @endif
                            @else
                                @if(isset($_GET['code']))
                                    @php
                                        $result     = Beneficiary::where('is_archived',true)
                                                        ->where('barangay',$_GET['code'])
                                                        ->orderBy('lastname','ASC')->get();
                                    @endphp
                                    @if(count($result) > 0)
                                        <table class="table my-3" id="dt">
                                            <thead>
                                                <tr>
                                                    <th>NAME</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($result as $row)
                                                    <tr>
                                                        <td>
                                                            {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                                        </td>
                                                        <td class="actions">
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 delete"
                                                                data-action="restore"
                                                                data-beneficiary_id="{{$row->beneficiary_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Restore Record">
                                                                <i class="fa fa-refresh"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 remove"
                                                                data-action="restore"
                                                                data-beneficiary_id="{{$row->beneficiary_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Delete Permanently">
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
                                                No archived beneficiary records.
                                            </p>
                                        </center>
                                    @endif
                                @else
                                    @php
                                        $result     = Beneficiary::where('is_archived',true)->orderBy('lastname','ASC')->get();
                                    @endphp

                                    @if(count($result) > 0)
                                        <table class="table my-3" id="dt">
                                            <thead>
                                                <tr>
                                                    <th>NAME</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($result as $row)
                                                    <tr>
                                                        <td>
                                                            {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                                        </td>
                                                        <td class="actions">
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 delete"
                                                                data-action="restore"
                                                                data-beneficiary_id="{{$row->beneficiary_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Restore Record">
                                                                <i class="fa fa-refresh"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" 
                                                                class="green w3-large mx-3 remove"
                                                                data-action="restore"
                                                                data-beneficiary_id="{{$row->beneficiary_id}}"
                                                                data-toggle="popover" 
                                                                data-trigger="hover"
                                                                data-placement="top"   
                                                                data-content="Delete Permanently">
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
                                                No archived beneficiary records.
                                            </p>
                                        </center>
                                    @endif
                                @endif
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
@endsection
