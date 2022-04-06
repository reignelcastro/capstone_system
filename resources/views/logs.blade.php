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
<style>
    body{
        background:#FFF;
        color:#555;
    }
    .form-group{
        margin:0;
        padding:0;
        font-size:15px;
        line-height:1.5;
    }
    .header-title{
        padding:0 10px;
        margin:6px 0;
        font-size:18px;
        font-weight:bold;
        border:1px solid #ddd;
        border-radius:5px;
        text-align:center;
    }
    .main-body{
        border:1px solid #ddd;
        padding:0;
        border-radius:5px;
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
    .col-md-9 b{
        font-weight:bold;
        color:#000 !important;
    }
    .uppercase{
        text-transform:uppercase !important;
    }
    .img{
        border:1px solid #ddd;
        border-radius:10px;
    }
    .bottom p{
        line-height:1.2;
    }
    .bottom{
        margin-bottom:100px;
    }
</style>
<div class="row">
<?php
    use App\Address;
    use App\Location;
    use App\Log;
    use App\Passenger;
    use App\User;
    use Illuminate\Support\Facades\DB;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    $arrData = [
        'student_code'  => $row->student_code,
        'id' => $row->passenger_id,
    ];
    $jsonData = json_encode($arrData);

    $location   = Location::where('location_id',$row->location_id)->first();

    $u          = User::where('id',Auth::user()->id)->first();
?>
<div class="col-md-12 p-0 mt-1">
<div class="row justify-content-center">
    <div class="container main-body py-2">
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
            @if(!empty($row))
            <?php
                $row2           = DB::table('regions')->where('id',$row->region)->first();
                $row3           = DB::table('provinces')->where('provCode',$row->province)->first();
                $row4           = DB::table('cm')->where('citymunCode',$row->city_municipality)->first();
                $row5           = DB::table('brgy')->where('brgyCode',$row->barangay)->first();
            ?>
            <div class="col-md-12 body-container">
                <div class="overflow-container">
                <div class="col-md-3 float-left img">
                    @if(isset($_GET['type']) && $_GET['type'] === 'logs')
                        <center>
                        <section class="p-3">
                            @if($row->profile_picture === 'default_photo.png')
                                <img src="{{asset('images/default_photo.png')}}" height="150" width="200" alt="USER">
                            @else
                            <img src="/storage/user_images/{{$row->profile_picture}}" height="150" class="profile_picture" width="150" alt="USER">
                            @endif
                        </section>
                        </center>
                    @else
                        <center>
                        <section class="p-3">
                            {{QrCode::size(150)->generate($jsonData)}}
                        </section>
                        <section class="p-3">
                            @if($row->profile_picture === 'default_photo.png')
                                <img src="{{asset('images/default_photo.png')}}" height="150" width="200" alt="USER">
                            @else
                            <img src="/storage/user_images/{{$row->profile_picture}}" height="150" class="profile_picture" width="150" alt="USER">
                            @endif
                        </section>
                        </center>
                    @endif
                </div>
                <div class="col-md-9 float-left">
                    <section class="header-title">
                        STUDENT'S INFORMATION
                    </section>
                    <div class="form-group row mt-3">
                        <div for="address" class="col-md-3 text-md-right">
                         STUDENT CODE :
                        </div>
                        <div class="col-md-9">
                            <b>
                                {{strtoupper($row->student_code)}}
                            </b>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                         STUDENT ID # :
                        </div>
                        <div class="col-md-9">
                            <b>
                                {{strtoupper($row->student_id)}}
                            </b>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                         STUDENT NAME :
                        </div>
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
                        <div for="address" class="col-md-3 text-md-right">
                         SEX :
                        </div>
                        <div class="col-md-9">
                            <b>
                                {{strtoupper($row->sex)}}
                            </b>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                            CONTACT # :
                        </div>
                        <div class="col-md-9">
                            <b>
                                {{$row->contact_number}}
                            </b>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                            EMAIL ADDRESS :
                        </div>
                        <div class="col-md-9">
                            <b>
                                {{$row->email}}
                            </b>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                            MAILING ADDRESS :
                        </div>
                        <div class="col-md-9">
                            <b class="uppercase">
                            {{$row->address_line}} {{$row5->brgyDesc}} {{$row4->citymunDesc}} {{$row3->provDesc}}
                            </b>
                        </div>
                    </div>
                    @if(isset($_GET['type']) && $_GET['type'] === 'logs')
                    @else
                    <section class="header-title">
                        GUARDIAN INFORMATION
                    </section>

                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                           GUARDIAN NAME :
                        </div>
                        <div class="col-md-9">
                            <b>
                                {{strtoupper($row->guardian_name)}}
                            </b>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                            GUARDIAN NUMBER :
                        </div>
                        <div class="col-md-9">
                            <b>
                                (+63){{strtoupper($row->guardian_number)}}
                            </b>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="address" class="col-md-3 text-md-right">
                            GUARDIAN ADDRESS :
                        </div>
                        <div class="col-md-9">
                            <b>
                                {{strtoupper($row->guardian_address)}}
                            </b>
                        </div>
                    </div>
                    @endif
                </div>
                </div>
            </div>
            @endif
            
    </div>
            <div class="container logs-container mt-2 p-0">
                @if(isset($_GET['date_from']) && isset($_GET['date_to']))
                        <?php
                            $date_from    = $_GET['date_from'];
                            $date_to    = $_GET['date_to'];
                            $result   = Log::where('passenger_id',$row->passenger_id)->whereBetween('log_date',[$date_from, $date_to])->get();
                        ?>
                        <div class="alert-default mb-2">
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
                    $result   = Log::where('passenger_id',$row->passenger_id)->get();
                ?>
                <div class="alert-default mb-2">
                <center>
                    <p>
                        <b>{{count($result)}}</b>
                        log result/s found.
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
                <div class="container bottom">
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