@extends('layouts.app')
@section('content')
<style>
    .e-404 .container{
        margin:20px 0;
        border:1px solid #ccc;
    }
    .e-404 .container{
        padding:20px;
        background:#fff;
        border-radius:5px;
    }
    .e-404 h1{
        font-size:50px;
        font-family:cooper;
    }
    .e-404 h1,.e-404 h3{
        font-weight:bold;
    }
    .e-404 a{
        width:200px;
        margin:5px 0;
        font-weight:bold !important;
        border-radius:20px !important;
    }
    #sidebar{
        display:none !important;
    }
    main{
        padding:0 !important;
    }
    body{
        background:red;
    }
</style>
<div class="row justify-content-center">
    <div class="col-md-6 e-404">
        <div class="container">
            <center>
                <img src="{{asset('images/error404.jpg')}}" style="width:50%;" class="mb-3" alt="ERROR404">
                <b>
                    <h2><b>UNAUTHORIZED ACCESS!<b></h2>
                    <h5>It seems like your account was deactivated.</h5>
                </b>
            
            <h6>Deactivated accounts are being prevented to access the page.</h6>
            </center>
        </div>
    </div>
</div>
@endsection