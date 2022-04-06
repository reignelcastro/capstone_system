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

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<?php
use App\Address;
use App\Location;
use App\Log;
use App\Passenger;
use App\User;
use Illuminate\Support\Facades\DB;

$location   = Location::where('location_id',$row->location_id)->first();
$u          = User::where('id',Auth::user()->id)->first();
?>
<style>
    .form-group{
        padding:0 !important;
        margin:0 !important;
        line-height:1.2 !important;
    }
    .header-title{
        color:#555;
        font-weight:bold;
        padding:0 10px;
        border:1px solid #ddd;
        margin:10px 0;
        font-size:15px;
    }
    .uppercase{
        text-transform:uppercase !important;
    }
    .location-container{
        text-align:center;
        font-family:'Sans Semi' !important;
        margin:20px 0;
    }
    .location-container p{
        padding:0;
        margin:0;
        line-height:1.2;
    }
    .location-container b{
        font-size:40px;
    }
    .bottom p{
        line-height:1.2;
    }
    .bottom{
        margin-bottom:100px;
    }
</style>
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <div class="col-md-12 location-container">
                <p>
                    <b>{{$location->name}}</b>
                </p>
                <p>
                    {{$location->company_address}}
                </p>
                <p>
                    {{$location->contact_info}}
                </p>
            </div>
            <div class="card mt-3 p-3">
                @if(!empty($row))
                <?php
                    $row1           = Address::where('address_id',$row->address)->first();
                    $row2           = DB::table('regions')->where('id',$row1->region)->first();
                    $row3           = DB::table('provinces')->where('provCode',$row1->province)->first();
                    $row4           = DB::table('cm')->where('citymunCode',$row1->city_municipality)->first();
                    $row5           = DB::table('brgy')->where('brgyCode',$row1->barangay)->first();
                ?>
                <div class="col-md-12 body-container p-0">
                    <div class="overflow-container p-0">
                    <div class="col-md-3 float-left pt-4">
                        <center>
                        <section class="p-3">
                            @if($row->profile_picture === 'default_photo.png')
                                <img src="{{asset('images/default_photo.png')}}" height="180" width="180" alt="USER">
                            @else
                            <img src="/storage/user_images/{{$row->profile_picture}}" height="180" class="profile_picture" width="180" alt="USER">
                            @endif
                        </section>
                        </center>
                    </div>
                    <div class="col-md-9 float-left p-0">
                    <div class="form-group row mt-3">
                            <label for="address" class="col-md-3 text-md-right">
                                TYPE :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{strtoupper($row->user_type)}}
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
                        <section class="header-title">
                            Contact Information &amp; Mailing Address
                        </section>  
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                                CONTACT NUMBER :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    (+63){{$row->contact_number}}
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
                                {{$row1->address_line}} {{$row5->brgyDesc}} {{$row4->citymunDesc}} {{$row3->provDesc}}
                                </b>
                            </div>
                        </div>
                        <section class="header-title">
                            Contact Person <i>(in case of emergency)</i>
                        </section>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                                NAME :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{strtoupper($row->contact_person_name)}}
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                                CONTACT INFO :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{strtoupper($row->contact_person_number)}}
                                </b>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 text-md-right">
                                ADDRESS :
                            </label>
                            <div class="col-md-9">
                                <b>
                                    {{strtoupper($row->contact_person_address)}}
                                </b>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card mt-4">
                    <div class="row p-0 m-0">
                        <div class="col-md-12 p-0 m-0 body-container">
                        <div class="col-md-12 logs-container p-0 m-0">
                        @if(isset($_GET['date_from']) && isset($_GET['date_to']))
                        <?php
                            $date_from    = $_GET['date_from'];
                            $date_to    = $_GET['date_to'];
                            $result   = Log::where('id',$row->id)->whereBetween('log_date',[$date_from, $date_to])
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
                            $today    = date('Y-m-d');
                            $result   = Log::where('id',$row->id)->where('log_date',$today)
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
                            LOGS FOR TODAY : <b>{{date('F, j Y',strtotime($today))}}</b>
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
            <div class="col-md-12 bottom">
                <br /> 
                PREPARED BY: 
                <br /> <br />
                <p class="p-0 m-0">
                    <b>
                        {{strtoupper($u->firstname)}} {{strtoupper($u->middlename)}} {{strtoupper($u->lastname)}} {{strtoupper($u->suffix)}}
                    </b>
                </p>
                <p class="p-0 m-0">
                    <small>{{strtoupper($u->user_type)}}</small>
                </p>
                <p class="p-0 m-0">
                    <small>{{date('F j, Y @ h:i:sa')}}</small>
                </p>
                </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        window.print();
    });
</script>
</body>
</html>
