@extends('layouts.app')

@section('content')
<?php
use App\Address;
use App\Log;
use App\Passenger;
use Illuminate\Support\Facades\DB;
?>
<style>
    .form-group{
        padding:0 !important;
        margin:0 !important;
        line-height:1.2 !important;
    }
</style>
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section class="window-title">
                <i class="fa fa-users"></i> MANAGE USERS ::
                <small>
                    <a href="/admin/register/user">User List</a> > View
                </small>
            </section>
            <div class="card">
                
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
                    <div class="col-md-2 float-left pt-4">
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
                    <div class="col-md-3 float-left pt-5">
                        <center>
                        <section class="p-3">
                            @if(!empty($row->license))
                                <img src="/storage/user_images/{{$row->license}}" height="150" width="240" alt="LICENSE">
                            @endif
                        </section>
                        </center>
                    </div>
                    <div class="col-md-7 float-left p-0">
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
                            <div class="col-md-9 w3-xlarge">
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
        </div>
    </div>
</div>
@endsection
