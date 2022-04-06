@extends('layouts.app')

@section('content')
<?php
use App\Address;
use App\Log;
use App\Passenger;
use App\Location;
use App\User;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
$arrData = [
    'student_code'  => $row->student_code,
    'id' => $row->passenger_id,
];
$jsonData = json_encode($arrData);

$location_id    = Auth::user()->location_id;
$type           = Auth::user()->user_type;
$id             = Auth::user()->id;

$location  = Location::where('location_id',$location_id)->first();
$u          = User::where('id',$id)->first();
?>
<style>
    .print{
        border:1px solid #007BFF;
    }
    body{
        padding:0;
    }
    .form-group{
        padding:0 !important;
        margin:0 !important;
        line-height:1.2;
    }
</style>
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section class="window-title">
                <i class="fa fa-users"></i> MANAGE PASSENGERS ::
                <small>
                    <a href="/admin/passenger">Passenger's List</a> > View > Logs
                </small>
            </section>
            <div class="card">
                @if(!empty($row))
                <?php
                    $row2           = DB::table('regions')->where('id',$row->region)->first();
                    $row3           = DB::table('provinces')->where('provCode',$row->province)->first();
                    $row4           = DB::table('cm')->where('citymunCode',$row->city_municipality)->first();
                    $row5           = DB::table('brgy')->where('brgyCode',$row->barangay)->first();
                ?>
                <div class="col-md-12 body-container">
                    <div class="overflow-container">
                    <div class="col-md-3 float-left">
                        <center>
                        <section class="p-3">
                            @if($row->profile_picture === 'default_photo.png')
                                <img src="{{asset('images/default_photo.png')}}" height="150" width="150" alt="USER">
                            @else
                            <img src="/storage/user_images/{{$row->profile_picture}}" height="150" class="profile_picture" width="150" alt="USER">
                            @endif
                        </section>
                        </center>
                    </div>
                    <div class="col-md-9 float-left">
                        <section class="header-title">
                            STUDENT'S INFORMATION
                        </section>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                             STUDENT ID :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{strtoupper($row->student_id)}}
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                             FULL NAME :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{strtoupper($row->lastname)}}, 
                                    {{strtoupper($row->firstname)}} 
                                    {{strtoupper($row->middlename)}} 
                                    {{strtoupper($row->suffix)}}
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                             SEX :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{strtoupper($row->sex)}}
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                                CONTACT NUMBER :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{$row->contact_number}}
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                                EMAIL :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{$row->email}}
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                                ADDRESS :
                            </label>
                            <div class="col-md-9">
                                <b class="uppercase">
                                {{$row->address_line}} {{$row5->brgyDesc}} {{$row4->citymunDesc}} {{$row3->provDesc}}
                                </b>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="row px-3"> 
            <div class="col-md-12 window-title my-2 p-1 px-3">
                <i class="fa fa-list"></i> LOG HISTORY
                @if(isset($_GET['date_from']) && isset($_GET['date_to']))
                    <script>
                        var token = '{{$_GET["_token"]}}';
                        var date_from = '{{$_GET["date_from"]}}';
                        var date_to = '{{$_GET["date_to"]}}';
                    </script>
                    <!-- <a href="#" onclick="window.open('/admin/logs/passenger/{{$row->passenger_id}}?_token='+token+'&date_from='+date_from+'&date_to='+date_to+'&type=logs');" 
                        class="print btn pull-right mt-2">
                    <i class="fa fa-print"></i> PRINT
                    </a>     -->
                    <button onclick="printDiv()" 
                        class="print btn pull-right mt-2">
                        <i class="fa fa-print"></i> PRINT
                    </button>
                @else
                    <!-- <a href="#" onclick="window.open('/admin/logs/passenger/{{$row->passenger_id}}?type=logs');" 
                        class="print btn pull-right mt-2">
                    <i class="fa fa-print"></i> PRINT
                    </a> -->
                    <button onclick="printDiv()" 
                        class="print btn pull-right mt-2">
                        <i class="fa fa-print"></i> PRINT
                    </button>
                @endif
            </div>
            </div>
            <div class="card">
                <div class="col-md-12 p-2">
                <section class="w3-border p-3">
                <form action="" method="GET">
                    @csrf
                    <div class="form-group row">
                        <label for="address" class="col-md-2 text-md-right">
                            SEARCH FROM :
                        </label>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control" required>
                        </div>
                        <label for="address" class="col-md-1 text-md-right">
                            TO :
                        </label>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-search"></i>
                            </button>
                            <a href="/admin/passenger/{{$row->passenger_id}}">
                                <button type="button" class="btn btn-info">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
                </section>
                </div>
                    <div class="row p-0 m-0">
                        <div class="col-md-12 p-0 m-0 body-container" id="printArea">
                        <div class="col-md-12 logs-container p-0 m-0">
                        @if(isset($_GET['date_from']) && isset($_GET['date_to']))
                        <?php
                            $date_from    = $_GET['date_from'];
                            $date_to    = $_GET['date_to'];
                            $result   = Log::where('passenger_id',$row->passenger_id)
                                            ->whereBetween('log_date',[$date_from, $date_to])
                                            ->orderBy('updated_at','DESC')
                                            ->get();
                        ?>
                        <div class="alert-info mb-2">
                        <center>
                        <p class="p-0 m-0">
                            <b>
                            {{count($result)}} log result/s found.
                            </b>
                        </p>
                        <p class="p-0 m-0">
                            SEARCHED DATE FROM <b>{{date('F, j Y',strtotime($date_from))}}</b> 
                            to <b>{{date('F, j Y',strtotime($date_to))}}</b>
                        </p>
                        </center>
                        </div>
                        <table class="table table-bordered table-sm table-condensed" id="data-table">
                            <thead>
                                <tr>
                                    <th>CODE</th>
                                    <th>STUDENT ID</th>
                                    <th>NAME</th>
                                    <th>AM IN</th>
                                    <th>AM OUT</th>
                                    <th>PM IN</th>
                                    <th>PM OUT</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($result) > 0)
                            @foreach($result as $row)
                                <?php
                                $rows   = Passenger::where('passenger_id',$row->passenger_id)->first();
                                ?>
                                    <tr>
                                        <td>{{strtoupper($rows->student_code)}}</td>
                                        <td>{{strtoupper($rows->student_id)}}</td>
                                        <td class="uppercase">{{$rows->lastname}} {{$rows->suffix}}, {{$rows->firstname}} {{$rows->middlename}}</td>

                                        @if(!empty($row->am_in))
                                            <td>{{date('h:i:sa',strtotime($row->am_in))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if(!empty($row->am_out))
                                            <td>{{date('h:i:sa',strtotime($row->am_out))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if(!empty($row->pm_in))
                                            <td>{{date('h:i:sa',strtotime($row->pm_in))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if(!empty($row->pm_out))
                                            <td>{{date('h:i:sa',strtotime($row->pm_out))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td>{{date('F d, Y h:i:sa',strtotime($row->updated_at))}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        @else
                        <?php
                            $result   = Log::where('passenger_id',$row->passenger_id)
                                        ->orderBy('updated_at','DESC')
                                        ->get();
                        ?>
                        <div class="alert-info mb-2">
                        <center>
                        <p class="p-0 m-0">
                            <b>
                            {{count($result)}} log result/s found.
                            </b>
                        </p>
                        </center>
                        </div>
                        <table class="table table-bordered table-sm table-condensed" id="data-table">
                            <thead>
                                <tr>
                                    <th>CODE</th>
                                    <th>STUDENT ID</th>
                                    <th>NAME</th>
                                    <th>AM IN</th>
                                    <th>AM OUT</th>
                                    <th>PM IN</th>
                                    <th>PM OUT</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($result) > 0)
                            @foreach($result as $row)
                                <?php
                                $rows   = Passenger::where('passenger_id',$row->passenger_id)->first();
                                ?>
                                    <tr>
                                        <td>{{strtoupper($rows->student_code)}}</td>
                                        <td>{{strtoupper($rows->student_id)}}</td>
                                        <td class="uppercase">{{$rows->lastname}} {{$rows->suffix}}, {{$rows->firstname}} {{$rows->middlename}}</td>

                                        @if(!empty($row->am_in))
                                            <td>{{date('h:i:sa',strtotime($row->am_in))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if(!empty($row->am_out))
                                            <td>{{date('h:i:sa',strtotime($row->am_out))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if(!empty($row->pm_in))
                                            <td>{{date('h:i:sa',strtotime($row->pm_in))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        @if(!empty($row->pm_out))
                                            <td>{{date('h:i:sa',strtotime($row->pm_out))}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td>{{date('F d, Y h:i:sa',strtotime($row->updated_at))}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        @endif
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function printDiv() 
{
    var div = document.getElementById('printArea');
    var print = window.open('','Print Window');
    var img   = '<center><img src="{{asset('images/logo.png')}}" alt="LOGO" height="130" class="m-0" style="margin-bottom:-50px !important;"></center><br />';
    var title = '<center><b class="w3-xxlarge">{{$location->name}}</b></center>';
    var body = '<center><p>{{$location->company_address}}</p><p>{{$location->contact_info}}</p></center>';
    var prepared_by = 'PREPARED BY: <br /><br /><br /><p><b>{{strtoupper($u->firstname)}} {{strtoupper($u->middlename)}} {{strtoupper($u->lastname)}}</b></p><p><small>{{strtoupper($u->user_type)}}</small></p><p><small>{{date("F j, Y h:i:sa")}}</small></p>';
    print.document.open();
    print.document.write('<html><head><link href="{{ asset('css/app.css') }}" rel="stylesheet"><link href="{{ asset('css/w3.css') }}" rel="stylesheet"><link href="{{ asset('fonts/app.css') }}" rel="stylesheet"><link href="{{ asset('awesome/css/font-awesome.min.css') }}" rel="stylesheet"></head><style>.main{font-size:10px !important;}table{font-size:11px !important;}p{line-height:1.2 !important;padding:0;margin:0;}.uppercase{text-transform:uppercase !important;}.dataTables_info,.dataTables_length,.dataTables_filter,.dataTables_paginate{display:none !important;}</style><body onload="window.print()" class="p-5">'+img+'<br />'+title+' '+body+'<br /><br /><main>'+div.innerHTML+'<br /><br /><br />'+prepared_by+'</main></body></html>');
    print.document.close();
}
    $(function(){
        $('#data-table').DataTable({
            "order": [[ 7, "desc" ]]
        });
    });
</script>
@endsection
