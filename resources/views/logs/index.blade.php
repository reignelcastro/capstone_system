@extends('layouts.app')

@section('content')
<?php
    use Jenssegers\Agent\Agent;
    $agent = new Agent();

?>
<style type="text/css">
    video{
        /* position:absolute;
        left:0;
        bottom:0;
        padding:0 !important;
        margin:0 !important; 
        z-index:1000 !important;
        height:300px;
        width:300px; */
        width:100%;
        border-radius:20px;
    }
    .video-container{
        text-align:center;
        border:1px solid #ddd;
        padding:5px;
        border-radius:20px;
    }
    .logs-container{
        border:1px solid #ddd;
        padding:2px;
        border-radius:5px;
    }
    #logs-form .w3-modal-content{
        width:40%;
        padding:20px;
        border-radius:10px;
        font-size:11px;
        border:2px solid #007BFF;
    }
    @media only screen and (max-width: 720px) {
        .logs-container{
            overflow-x:scroll;
            overflow-y:hidden;
        }
    }
</style>
<div class="col-md-12 p-0 main-container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section class="window-title">
                <a href="/admin/scan"><i class="fa fa-home w3-xxlarge"></i></a> :: <i class="fa fa-qrcode"></i> QR Scanner
            </section>
        </div>
    </div>
    <section id="errors"></section>
    <div class="row m-0 p-0">
        <div class="col-md-12 alert-info p-1 m-0 my-1">
            <center>
                <b>This will display logs for today's date {{date('F j, Y')}}. <br />Latest scans will be displayed first.</b>
            </center>
        </div>
        <div class="col-md-2 video-container">
            <i class="fa fa-qrcode"></i>
            <b>SCAN QR CODE</b>
            <video id="preview"></video>
            <form action="" method="POST" id="qr-form">
                @csrf
                <div class="row w3-hide">
                <div class="col-md-12">
                    <div class="col-md-9 p-0 float-left">
                        <input type="hidden" id="passenger_id" placeholder="ID" class="form-control" name="passenger_id" readonly>
                        <input type="text" id="student_code" placeholder="CODE" class="form-control" name="student_code" readonly>
                        <input type="hidden" id="type" placeholder="TYPE" class="form-control" name="type" value="{{$option}}" readonly>
                    </div>
                    <div class="col-md-3 p-0 float-left">
                        <button type="submit" id="qr-submit" class="btn btn-sm btn-info"
                            style="height:100% !important;width:100% !important;">
                            <i class="fa fa-send"></i>
                        </button>
                    </div>
                </div>
                </div>
            </form>
            <b>
            @if($option === 'am-in')
                TIME IN : AM
            @elseif($option === 'am-out')
                TIME OUT : AM
            @elseif($option === 'pm-in')
                TIME IN : PM
            @elseif($option === 'pm-out')
                TIME OUT : PM
            @endif
            </b>
            <br />
            <b>LOGS : <span id="counts2-container"></span></b>
        </div>
        <div class="col-md-10 p-1">
            <div class="container logs-container p-0">
                <table class="table table-bordered table-sm table-condensed">
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
                    <tbody id="logs-container">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="w3-modal modal-alert pt-2" 
    id="logs-form" style="z-index:800 !important;cursor:pointer;"
    onclick="document.getElementById('logs-form').style.display='none'">
    <div class="w3-modal-content w3-animate-zoom">
        <div class="modal-body p-2 logs-body">
        </div>
    </div>
</div>
<script src="{{ asset('scan/instascan.min.js') }}"></script>

<script type="text/javascript">
// function save(){
//     var type   = '{{$option}}';
//     $.ajax({
//         url:'{{route("scanner-save")}}',
//         method:'POST',
//         data:{
//             _token:_token,
//             passenger_id:passenger_id,
//             student_code:student_code,
//             type:type
//         },
//         dataType:'json',
//         beforeSend:function(){
//             $('#errors').html("");
//             document.getElementById('logs-form').style.display='none';
//         },
//         success:function(response){
//             if(response.errors)
//             {
//                 swal("ERROR!","QR Code content not recognized!", "error");
//             }
//             else if(response.success)
//             {
//                 $('#errors').html("");
//                 document.getElementById('logs-form').style.display='block';
//                 $('.logs-body').html(response.html);
//                 var log_id  = response.last_id;
//                 $('#logs_'+log_id).hide();
//                 $('#logs-container').prepend(response.html2);
//                 count(_token, type);

//             }
//             else if(response.error)
//             {
//                 swal("ERROR!", response.error, "error");
                
//             }
//         },
//         error: function (xhr, status, error) {
//             var error = JSON.parse(xhr.responseText);
//             swal("ERROR!","INTERNAL SERVER ERROR: Invalid/Unverified phone number detected. Page will automatically reload.", "error");
//             $('.main-container').html('<div class="alert alert-danger">Due to internal server error, the system will automatically reload the page to avoid unnecessary invalid processing. Please wait.</div>');
//             setInterval(function(){location.reload();},4000);
//         }
//     });
// }
function count(_token, type){
    $.ajax({
        url:'{{route("scanner-count")}}',
        method:'POST',
        data:{
            _token:_token,
            type:type
        },
        dataType:'json',
        success:function(response){
            if(response.count2)
            {
                $('#counts2-container').html(response.count2);
            }
            else if(response.error)
            {
                swal("ERROR!", response.error, "error");
            }
        },
        error: function (response) {
            alert(response);
        }
    });
}
function load(_token){
    $.ajax({
        url:'{{route("scanner-load")}}',
        method:'POST',
        data:{
            _token:_token
        },
        dataType:'json',
        success:function(response){
            if(response.html)
            {
                $('#logs-container').prepend(response.html);
            }
            else if(response.error)
            {
                swal("ERROR!", response.error, "error");
            }
        },
        error: function (response) {
            alert(response);
        }
    });
}
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        var type    = '{{$option}}';
        load(_token);
        count(_token, type);

          let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
          scanner.addListener('scan', function (content) {
            try {
                var data     = JSON.parse(content);
                var student_code    = data.student_code;
                var passenger_id    = data.id;
                $('#passenger_id').val(passenger_id);
                $('#student_code').val(student_code);
                $('#qr-submit').trigger('click');
            } catch (e) {
                swal('ERROR!','CANNOT BE RECOGNIZED! QR Code is in invalid format','error');
                console.clear();
            }
          });

          Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
              if('{{$agent->isMobile()}}')
              {
                  //alert("MOBILE!")
                  scanner.start(cameras[1]);
              }
              else
              {
                scanner.start(cameras[0]);
              }
              
            } else {
              console.error('No cameras found.');
            }
          }).catch(function (e) {
            swal('ERROR',e,'error');
          });

        $(document).on('submit','#qr-form',function(e){
            e.preventDefault();
            $.ajax({
                url:'{{route("scanner-save")}}',
                method:'POST',
                data:new FormData(this),
                contentType:false,
                processData:false,
                cache:false,
                dataType:'json',
                beforeSend:function(){
                    $('#errors').html("");
                    document.getElementById('logs-form').style.display='none';
                    $('#qr-submit').html('<i class="fa fa-refresh"></i>');
                    $('#qr-submit').attr('disabled',true);
                },
                success:function(response){
                    $('#qr-submit').html('<i class="fa fa-send"></i>');
                    $('#qr-submit').attr('disabled',false);
                    $('#qr-form')[0].reset();
                    if(response.errors)
                    {
                        swal("ERROR!","QR Code content not recognized!", "error");
                    }
                    else if(response.success)
                    {
                        $('#errors').html("");
                        document.getElementById('logs-form').style.display='block';
                        $('.logs-body').html(response.html);
                        var log_id  = response.last_id;
                        $('#logs_'+log_id).hide();
                        $('#logs-container').prepend(response.html2);
                        count(_token, type);

                    }
                    else if(response.error)
                    {
                        swal("ERROR!", response.error, "error");
                        
                    }
                },
                error: function (xhr, status, error) {
                    var error = JSON.parse(xhr.responseText);
                    swal("ERROR!","INTERNAL SERVER ERROR: Invalid/Unverified phone number detected. Page will automatically reload.", "error");
                    $('.main-container').html('<div class="alert alert-danger">Due to internal server error, the system will automatically reload the page to avoid unnecessary invalid processing. Please wait.</div>');
                    setInterval(function(){location.reload();},4000);
                }
            });
        });
    });
</script>
@endsection
