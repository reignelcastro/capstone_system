@extends('layouts.app')

@section('content')
<?php
    use App\Location;
    use App\User;
    use Illuminate\Support\Facades\DB;
    use App\Service;
    use App\Requirement;
    use App\Flowchart;
    use App\Application;

    $location_id    = Auth::user()->location_id;
    $type           = Auth::user()->user_type;
    $id             = Auth::user()->id;
    $location  = Location::where('location_id',$location_id)->first();
    $u      = User::where('id',$id)->first();
?>

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<style>
    p{
        padding:0;
        margin:0;
    }
    .db{
        color:#FFF;
        padding:10px;
    }
    .db b{
        font-size:15px;
    }
    .db h3{
        font-size:25px;
        color:#FFF;
        font-weight:bold;
        border-top:1px solid #FFF;
    }
    .canvasjs-chart-credit{
        display:none !important;
    }
</style>
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        @if(Auth::user()->user_type === "ADMIN")
        Social Welfare Assistance to Crisis Beneficiary’s Request Management System
        @else
            Assistance to Individual in Crisis Situation (AICS) Services
        @endif
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section id="errors"></section>
            @if(Auth::user()->user_type === "ADMIN")
                <div class="row m-0 mb-2">
                    <div class="col-md-12">
                        <form action="" method="GET">
                            <div class="row px-3 my-3 justify-content-center">
                                <div class="col-md-3 p-0 m-0">
                                    <b>FROM:</b>
                                    <input type="date" name="from" id="from" class="w-75" required>
                                </div>
                                <div class="col-md-3 p-0 m-0">
                                    <b>TO:</b>
                                    <input type="date" name="to" id="to" class="w-75" required>
                                </div>
                                <div class="col-md-3 p-0 m-0">
                                    <button type="submit" class="btn btn-primary p-0 py-1 px-4">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="/">
                                        <button type="button" class="btn btn-primary p-0 py-1 px-4">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </a>
                                    <!-- <button type="button" class="btn btn-primary p-0 py-1 px-4" onclick="printDiv();">
                                        <i class="fa fa-print"></i>
                                    </button>       -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @php
                    $dataPoints1 = array();
                    $dataPoints2 = array();
                    if(isset($_GET['from']) && isset($_GET['to']))
                    {
                        $from   = $_GET['from'];
                        $to     = $_GET['to'];
                        $result1   = DB::select("SELECT COUNT(application_id) AS counts, date_submitted FROM 
                                        applications WHERE is_approved = true
                                        AND date_submitted BETWEEN '".$from."' AND '".$to."' GROUP BY date_submitted");
                        $result2   = DB::select("SELECT COUNT(application_id) AS counts, date_submitted FROM 
                                        applications WHERE is_approved = false
                                        AND date_submitted BETWEEN '".$from."' AND '".$to."' GROUP BY date_submitted");

                        $barangays   = Application::whereBetween('date_submitted',[$from,$to])->get();
                        $requests   = Application::where('is_completed',false)->whereBetween('date_submitted',[$from,$to])->get();
                        $approved   = Application::where('is_approved',true)->whereBetween('date_submitted',[$from,$to])->get();
                        $disapproved   = Application::where('is_approved',false)->whereBetween('date_submitted',[$from,$to])->get();
                    }
                    else
                    {
                        $result1   = DB::select("SELECT COUNT(application_id) AS counts, date_submitted FROM applications WHERE is_approved = true GROUP BY date_submitted");
                        $result2   = DB::select("SELECT COUNT(application_id) AS counts, date_submitted FROM applications WHERE is_approved = false GROUP BY date_submitted");

                        $barangays   = Application::all();
                        $requests   = Application::where('is_completed',false)->get();
                        $approved   = Application::where('is_approved',true)->get();
                        $disapproved   = Application::where('is_approved',false)->get();
                    }

                    foreach($result1 as $row1){  
                        array_push($dataPoints1, array("y"=> $row1->counts,"label"=> date('F d, Y', strtotime($row1->date_submitted))));
                    }
                    foreach($result2 as $row2){
                        array_push($dataPoints2, array("y"=> $row2->counts,"label"=> date('F d, Y', strtotime($row2->date_submitted))));
                    }
                @endphp
                <div id="printArea">
                <div class="row px-5 text-center">
                    <div class="col-md-6 db bg-success">
                        <b>NEW REQUESTS</b>
                        <h3>{{count($requests)}}</h3>
                    </div>
                    <div class="col-md-6 db bg-info">
                        <b>TOTAL BARANGAYS</b>
                        <h3>{{count($barangays)}}</h3>
                    </div>
                </div>
                <br />
                <div class="row px-5" style="height:400px;width:100%;max-width:100%;">
                    <div class="col-md-12">
                        <div id="chart"></div>
                    </div>
                </div>
                <br />
                <div class="row px-5 text-center">
                    <div class="col-md-4 db bg-success">
                        <b>APPROVED</b>
                        <h3>{{count($approved)}}</h3>
                    </div>
                    <div class="col-md-4 db bg-danger">
                        <b>DISAPPROVED</b>
                        <h3>{{count($disapproved)}}</h3>
                    </div>
                    <div class="col-md-4 db bg-warning">
                        <b>VALIDATING</b>
                        <h3>{{count($requests)}}</h3>
                    </div>
                </div>
                </div>
            @else
                <h3><b>AICS Services</b></h3>
                @php
                    $result = Service::orderBy('services_id','DESC')->get();
                @endphp

                @foreach($result as $row)
                    @php
                        $result2 = Requirement::where('services_id',$row->services_id)->get();
                    @endphp
                <div class="row w-100 p-0 m-0 mt-2 w3-pale-green w3-border">
                    <div class="col-md-8 p-3">
                        <h5 class="p-0 m-0"><b>{{ucwords($row->aics_services)}}</b></h5>
                        <div class="col-md-12 pl-4 m-0">
                            <p class="p-0 m-0">
                                {!! $row->description !!}
                            </p>
                        </div>
                        @if(count($result2) > 0)
                        <h6 class="p-0 m-0"><b>Documents/Requirements</b></h6>
                        <div class="col-md-12 pl-2 m-0">
                            <p class="p-0 m-0">
                                <ul>
                                    @foreach($result2 as $row2)
                                        <li>{{$row2->requirement_description}}</li>
                                    @endforeach
                                </ul>
                            </p>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-4 p-5">
                        @php
                            $fc     = Flowchart::where('services_id',$row->services_id)->first();
                        @endphp

                        @if(!empty($fc))
                            <img src="storage/flowcharts/{{$fc->instruction}}" class="pointer fc" data-image="storage/flowcharts/{{$fc->instruction}}" alt="FLOWCHART" style="width:100%;max-width:100%;">
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<div class="w3-modal pt-2 pb-5 pointer" id="fc-modal"
    onclick="document.getElementById('fc-modal').style.display='none'">
    <div class="w3-modal-content w3-transparent p-5">
        <div class="row justify-content-center">
        <div class="col-md-8" id="fc-modal-content"></div>
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
    $(document).on('click','.fc', function(){
        var image   = $(this).data('image');
        $('#fc-modal-content').html('<img src="'+image+'" alt="FLOWCHART" style="width:100%;max-width:100%;">');
        document.getElementById('fc-modal').style.display='block';
    });
});
</script>
@if(Auth::user()->user_type === 'ADMIN')
<script>
    $(function(){
        var chart = new CanvasJS.Chart("chart", {
            animationEnabled: true,
            theme: "light2",
            title:{
                text: "AICS Services Requests Trend"
            },
            axisY:{
                includeZero: true
            },
            legend:{
                cursor: "pointer",
                verticalAlign: "center",
                horizontalAlign: "right",
                itemclick: toggleDataSeries
            },
            data: [{
                type: "column",
                name: "Approved",
                indexLabel: "{y}",
                showInLegend: true,
                dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "column",
                name: "Disapproved",
                indexLabel: "{y}",
                showInLegend: true,
                dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();
        
        function toggleDataSeries(e){
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            }
            else{
                e.dataSeries.visible = true;
            }
            chart.render();
        }
    });
</script>
@endif
@endsection
