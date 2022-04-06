@extends('layouts.app')

@section('content')
<?php
use App\Address;
use Illuminate\Support\Facades\DB;
$row2           = Address::where('address_id',$row->address)->first();
?>
<div class="col-md-12 p-0">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
        <section class="window-title">
                <i class="fa fa-users"></i> MANAGE USERS ::
                <small>
                    <a href="/admin/register/user">User List</a> > Edit
                </small>
            </section>
            <section id="errors"></section>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" id="form">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{$row->id}}" readonly>
                        <div class="form-group row mb-4">
                            <div class="col-md-6">
                                @if(!empty($row->license))
                                    <img src="/storage/user_images/{{$row->license}}" height="250" width="350" alt="LICENSE">
                                @endif
                                <button type="button" class="btn btn-sm btn-info p-0 px-2 ml-4 mt-3"
                                    onclick="document.getElementById('license-form').style.display='block'">
                                    <b><i class="fa fa-edit"></i> Change</b>
                                </button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type" class="col-md-2 col-form-label text-md-right">{{ __('ACCOUNT TYPE') }}</label>

                            <div class="col-md-6">
                                <select id="user_type" name="user_type"
                                        class="form-control user_type sans-semi" 
                                        required>
                                        <option value="" disabled selected>TYPE</option>
                                        <option value="DRIVER">DRIVER</option>
                                        <option value="CONDUCTOR">CONDUCTOR</option>
                                </select>
                            </div>
                        </div>

                        <section class="header-title">
                            <i class="fa fa-user"></i> Personal Information
                        </section>
                        <div class="form-group row">
                            <label for="firstname" class="col-md-2 col-form-label text-md-right">{{ __('First Name') }}</label>

                            <div class="col-md-6">
                                <input id="firstname" 
                                type="text" 
                                class="form-control" 
                                name="firstname"
                                placeholder="First Name" 
                                value="{{$row->firstname}}" required 
                                autocomplete="firstname" autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="middlename" class="col-md-2 col-form-label text-md-right">{{ __('Middle Name') }}</label>

                            <div class="col-md-6">
                                <input id="middlename" 
                                type="text" 
                                class="form-control" 
                                name="middlename" 
                                placeholder="Middle Name"
                                value="{{$row->middlename}}" required 
                                autocomplete="middlename" autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lastname" class="col-md-2 col-form-label text-md-right">{{ __('Last Name') }}</label>

                            <div class="col-md-6">
                                <input id="lastname" 
                                type="text" 
                                class="form-control" 
                                name="lastname" 
                                placeholder="Last Name"
                                value="{{$row->lastname}}" required 
                                autocomplete="lastname" autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="suffix" class="col-md-2 col-form-label text-md-right">{{ __('Suffix Name') }}</label>

                            <div class="col-md-6">
                                <input id="suffix" 
                                type="text" 
                                class="form-control" 
                                name="suffix" 
                                placeholder="Suffix Name"
                                value="{{$row->suffix}}" 
                                autocomplete="suffix" autofocus>
                            </div>
                        </div>

                        <section class="header-title">
                            <i class="fa fa-envelope"></i> Contact Details
                        </section>
                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" 
                                type="email" 
                                class="form-control" 
                                name="email" 
                                placeholder="Active and valid email address."
                                value="{{$row->email}}" 
                                required autocomplete="email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="contact_number" class="col-md-2 col-form-label text-md-right">{{ __('Contact Number') }}</label>

                            <div class="col-md-6">
                            <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id=""><b>+63</b></span>
                            </div>
                                <input id="contact_number" 
                                    type="contact_number" 
                                    class="form-control" 
                                    name="contact_number" 
                                    placeholder="Active 11-digit cellphone number (935 123 4567)"
                                    value="{{$row->contact_number}}" 
                                    required autocomplete="contact_number">
                            </div>
                            </div>
                        </div>
                        <section class="header-title">
                            <i class="fa fa-flag"></i> Complete Address
                        </section>
                        <div class="form-group row">
                            <label for="region" class="col-md-2 col-form-label text-md-right">{{ __('REGION') }}</label>
                            <div class="col-md-5">
                                <select id="regCode" name="region"
                                        class="form-control regCode sans-semi" 
                                        required>
                                        <option value="" disabled selected>REGION</option>
                                    <?php
                                        $results     = DB::table('regions')
                                                            ->orderBy('regDesc','ASC')
                                                            ->get();
                                        if(count($results) > 0)
                                        {
                                            foreach ($results as $rows) 
                                            {
                                                ?>
                                                <option value="{{$rows->regCode}}">
                                                    <b>{{strtoupper($rows->regDesc)}}</b>
                                                </option>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <center><h2>NO RECORDS FOUND.</h2></center>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="province" class="col-md-2 col-form-label text-md-right">{{ __('PROVINCE') }}</label>
                            <div class="col-md-5">
                                <span class="prov-container">
                                    <input id="provCode" type="text"
                                            placeholder="SELECT REGION FIRST" 
                                            class="form-control province"
                                            autocomplete="off" 
                                            readonly>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city_municipality" class="col-md-2 col-form-label text-md-right">{{ __('CITY / MUNICIPALITY') }}</label>
                            <div class="col-md-5">
                                <span class="cm-container">
                                    <input id="citymunCode" type="text" 
                                            placeholder="SELECT PROVINCE FIRST" 
                                            class="form-control citymunCode" 
                                            autocomplete="off" readonly>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="barangay" class="col-md-2 col-form-label text-md-right">{{ __('BARANGAY') }}</label>
                            <div class="col-md-5">
                                <span class="brgy-container">
                                    <input id="brgyCode" type="text"
                                            placeholder="BARANGAY" 
                                            class="form-control brgyCode" 
                                            name="brgyCode"
                                            autocomplete="off" 
                                            readonly>
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address_line" class="col-md-2 col-form-label text-md-right">{{ __('ADDRESS LINE') }}</label>
                            <div class="col-md-5">
                                <input id="address_line" type="text"
                                    placeholder="STREET/ HOUSE #/ PUROK " 
                                    class="form-control address_line uppercase" 
                                    name="address_line"
                                    value="{{$row2->address_line}}"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="zip_code" class="col-md-2 col-form-label text-md-right">{{ __('ZIP CODE') }}</label>
                            <div class="col-md-5">
                                <input id="zip_code" type="text"
                                    placeholder="ZIP CODE" 
                                    class="form-control zip_code uppercase" 
                                    name="zip_code"
                                    value="{{$row2->zip_code}}"
                                    autocomplete="off">
                            </div>
                        </div>
                        <section class="header-title">
                            <i class="fa fa-car"></i> Vehicle Information
                        </section>
                        <div class="form-group row">
                            <label for="plate_number" class="col-md-2 col-form-label text-md-right">{{ __('PLATE #') }}</label>
                            <div class="col-md-5">
                                <input id="plate_number" type="text"
                                    placeholder="VEHICLE'S PLATE #" 
                                    class="form-control plate_number uppercase" 
                                    name="plate_number"
                                    value="{{$row->plate_number}}"
                                    autocomplete="off">
                            </div>
                        </div>
                        <section class="header-title">
                            <i class="fa fa-phone"></i> Contact Person in case of emergency
                        </section>
                        <div class="form-group row">
                            <label for="contact_person_name" class="col-md-2 col-form-label text-md-right">{{ __('FULL NAME') }}</label>
                            <div class="col-md-5">
                                <input id="contact_person_name" type="text"
                                    placeholder="CONTACT PERSON'S NAME" 
                                    class="form-control contact_person_name uppercase" 
                                    name="contact_person_name"
                                    value="{{$row->contact_person_name}}"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="contact_person_number" class="col-md-2 col-form-label text-md-right">{{ __('CONTACT NUMBER') }}</label>
                            <div class="col-md-5">
                                <input id="contact_person_number" type="text"
                                    placeholder="CONTACT INFORMATION" 
                                    class="form-control contact_person_number uppercase" 
                                    name="contact_person_number"
                                    value="{{$row->contact_person_number}}"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="contact_person_address" class="col-md-2 col-form-label text-md-right">{{ __('MAILING ADDRESS') }}</label>
                            <div class="col-md-5">
                                <input id="contact_person_address" type="text"
                                    placeholder="COMPLETE ADDRESS" 
                                    class="form-control contact_person_address uppercase" 
                                    name="contact_person_address"
                                    value="{{$row->contact_person_address}}"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row mb-0 mt-3">
                            <div class="col-md-7 text-md-right">
                                <button type="submit" class="btn btn-primary submit">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="w3-modal modal-alert" 
    id="license-form" style="z-index:800 !important;">
    <div class="w3-modal-content w3-animate-top">
        <div class="col-md-12">
            <h5 class="col-md-12 header-title transform pointer pt-4"
                onclick="document.getElementById('license-form').style.display='none'">
                <b>
                    <i class="fa fa-image"></i>
                    Change License
                </b>
                <i class="fa fa-times pull-right"></i>
            </h5>
        </div>
        <div class="modal-body p-2">
            <form action="" method="POST" id="license-form" enctype="multipart/form-data">
                @csrf
                <input type="file" name="license" id="license" class="w3-input">
                <div class="modal-footer p-0">
                    <button type="submit" class="btn btn-primary upload-button">
                        Upload License
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
<div class="row w3-hide">
    <div class="col-md-12 w3-hide">
        <input type="hidden" id="img_loader" value="<i class='fa fa-refresh'></i>" height='30' width='40'>" readonly>
        <input type="hidden" id="r-1" value="{{route('provinces')}}" readonly>
        <input type="hidden" id="r-2" value="{{route('cm')}}" readonly>
        <input type="hidden" id="r-3" value="{{route('brgy')}}" readonly>
    </div>
</div>
@if(!empty($row))
<input type="hidden" id="x-status" value="exists" readonly>
<input type="hidden" id="x-regCode" value="{{$row2->region}}" readonly>
<input type="hidden" id="x-provCode" value="{{$row2->province}}" readonly>
<input type="hidden" id="x-citymunCode" value="{{$row2->city_municipality}}" readonly>
<input type="hidden" id="x-brgyCode" value="{{$row2->barangay}}" readonly>

<script>
    $(function(){
        $('#user_type').val('{{$row->user_type}}');
    });
</script>
@else
<input type="hidden" id="x-status" value="null" readonly>
@endif
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        $('#form').on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:'{{route("registration-update")}}',
                method:'POST',
                data:new FormData(this),
                contentType:false,
                cache:false,
                processData:false,
                dataType:'json',
                beforeSend:function(){
                    $('.alert').html("");
                    $('.submit').attr('disabled',true);
                    $('.submit').html("Processing...");
                },
                success:function(response){
                    $('.submit').attr('disabled',false);
                    $('.submit').html("Save");
                    if(response.errors)
                    {
                        var message = "";
                        for(var i = 0; i < response.errors.length; i++)
                        {
                            message += '<p class="p-0 m-0">'+ response.errors[i]+'</p>';
                        }
                        $('#errors').html('<div class="alert alert-danger">'+message+'</div>');
                        //swal("ERROR!", message,'error');
                    }
                    else if(response.success)
                    {
                        $('.alert').html("");
                        swal("SUCCESS!", response.success, "success");
                    }
                    else if(response.error)
                    {
                        swal("ERROR!", response.error, "error");
                    }
                }
            });
        });

        $(document).on('click', '.swal-button',function(){
            window.open('/admin/register/user','_self');
        });

        $(document).on('submit','#license-form',function(e){
            e.preventDefault();
            var data    = new FormData();
            var id      = $('#id').val();
            var files   = $('#license').prop('files');
            data.append('_token',_token);
            data.append('license',files[0]);
            data.append('id',id);
            $.ajax({
                url:'{{route("registration-license-update")}}',
                method:'POST',
                data:data,
                dataType:'json',
                contentType:false,
                cache:false,
                processData:false,
                beforeSend:function(){
                    $('.upload-button').attr('disabled',true);
                    $('.upload-button').html("Uploading...");
                },
                success:function(response){
                    $('.upload-button').attr('disabled',false);
                    $('.upload-button').html("Upload Image");
                    document.getElementById('license-form').style.display='none';
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
                        document.getElementById('license-form').style.display='none';
                        $('.swal-button').addClass('reload');
                    }
                }
            });
        });
    });
</script>
@endsection
