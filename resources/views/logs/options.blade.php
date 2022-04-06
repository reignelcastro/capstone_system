@extends('layouts.app')
@section('content')
<style>
    .col-md-3{
        float:left !important;
        margin-top:10px;
    }
    .options-body{
        border:1px solid #ccc;
        text-align:center;
        padding:10px;
        margin-top:5px;
        border-radius:20px;
        padding:15px 0;
        border-top:5px solid #007BFF;
    }
    .options-body:hover{
        background:#007BFF !important;
        color:#FFF;
    }
    .options-body p{
        padding:0;
        margin:0;
        font-size:30px;
        line-height:1;
    }
    .options-body b{
        font-size:50px;
        padding:0;
        margin:0;
        line-height:1;
        font-family:cooper;
    }
</style>
<div class="col-md-12 p-0">
    <div class="row">
        <div class="col-md-3">
            <a href="/admin/scan/am-in">
                <div class="container options-body">
                    <b>AM</b>
                    <p>IN</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/scan/am-out">
                <div class="container options-body">
                    <b>AM</b>
                    <p>OUT</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/scan/pm-in">
                <div class="container options-body">
                    <b>PM</b>
                    <p>IN</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/scan/pm-out">
                <div class="container options-body">
                    <b>PM</b>
                    <p>OUT</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
