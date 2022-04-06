@extends('layouts.app')

@section('content')

<div class="col-md-12 p-0">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section class="window-title">
                <i class="fa fa-users"></i> MANAGE PASSENGERS ::
                <small>
                    <a href="/admin/passenger">Passenger's List</a> > Edit
                </small>
            </section>
            <section id="errors"></section>
            <div class="card">
                <div class="card-body">
                    <div class="container alert-info p-3">
                        <b>NOTE : </b> Fields marked with (<span class="required">*</span>) are required!
                    </div>
                    <hr>
                    <form method="POST" action="" id="form">
                        @csrf
                        <input type="hidden" id="passenger_id" name="passenger_id" value="{{$row->passenger_id}}" readonly>

                        <div class="form-group row mb-4">
                            <div class="col-md-6">
                                @if($row->profile_picture !== 'default_photo.png')
                                    <img src="/storage/user_images/{{$row->profile_picture}}" height="200" width="200" alt="LICENSE">
                                @else
                                    <img src="{{asset('images/default_photo.png')}}" height="200" width="200" alt="USER">
                                @endif
                                <button type="button" class="btn btn-sm btn-info p-0 px-2 ml-4 mt-3"
                                    onclick="document.getElementById('profile-form').style.display='block'">
                                    <b><i class="fa fa-edit"></i> Change</b>
                                </button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="student_id" class="col-md-2 col-form-label text-md-right">{{ __('STUDENT ID #') }}</label>

                            <div class="col-md-6">
                                <input id="student_id" 
                                type="text" 
                                class="form-control" 
                                name="student_id"
                                placeholder="STUDENT ID #" 
                                value="{{$row->student_id}}" 
                                autocomplete="student_id" autofocus>
                            </div>
                        </div>

                        <section class="header-title">
                            <i class="fa fa-user"></i> Personal Information
                        </section>
                        <div class="form-group row">
                            <label for="firstname" class="col-md-2 col-form-label text-md-right">
                                {{ __('First Name') }} <span class="required">*</span>
                            </label>

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
                            <label for="middlename" class="col-md-2 col-form-label text-md-right">{{ __('Middle Name') }} <span class="required">*</span></label>

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
                            <label for="lastname" class="col-md-2 col-form-label text-md-right">{{ __('Last Name') }}  <span class="required">*</span></label>

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

                        <div class="form-group row mt-4">
                            <label for="sex" class="col-md-2 col-form-label text-md-right">{{ __('Sex') }} <span class="required">*</span></label>

                            <div class="col-md-6">
                                <select id="sex" 
                                type="text" 
                                class="form-control" 
                                name="sex" autofocus>
                                    <option value="" disabled selected>SELECT</option>
                                    <option value="MALE">MALE</option>
                                    <option value="FEMALE">FEMALE</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <label for="email" class="col-md-2 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" 
                                type="email" 
                                class="form-control" 
                                name="email" 
                                placeholder="Email Address"
                                value="{{$row->email}}" 
                                autocomplete="email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="contact_number" class="col-md-2 col-form-label text-md-right">{{ __('Contact Number') }}</label>

                            <div class="col-md-6">
                                <input id="contact_number" 
                                    type="contact_number" 
                                    class="form-control" 
                                    name="contact_number" 
                                    placeholder="(0935 123 4567)"
                                    value="{{$row->contact_number}}" 
                                    autocomplete="contact_number">
                            </div>
                        </div>
                        <section class="header-title">
                            <i class="fa fa-flag"></i> Complete Address
                        </section>
                        <div class="form-group row">
                            <label for="region" class="col-md-2 col-form-label text-md-right">{{ __('REGION') }} <span class="required">*</span></label>
                            <div class="col-md-5">
                                <select id="regCode" name="region"
                                        class="form-control regCode sans-semi" 
                                        required>
                                        <option value="" disabled selected>REGION</option>
                                    <?php
                                        use Illuminate\Support\Facades\DB;

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
                            <label for="province" class="col-md-2 col-form-label text-md-right">{{ __('PROVINCE') }} <span class="required">*</span></label>
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
                            <label for="city_municipality" class="col-md-2 col-form-label text-md-right">{{ __('CITY / MUNICIPALITY') }} <span class="required">*</span></label>
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
                            <label for="barangay" class="col-md-2 col-form-label text-md-right">{{ __('BARANGAY') }} <span class="required">*</span></label>
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
                                    value="{{$row->address_line}}"
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
                                    value="{{$row->zip_code}}"
                                    autocomplete="off">
                            </div>
                        </div>

                        <section class="header-title">
                            <i class="fa fa-user"></i> GUARDIAN'S INFORMATION
                        </section>
                        <div class="form-group row">
                            <label for="guardian_name" class="col-md-2 col-form-label text-md-right">{{ __('GUARDIAN NAME') }} <span class="required">*</span></label>
                            <div class="col-md-5">
                                <input id="guardian_name" type="text"
                                    placeholder="GUARDIAN'S NAME" 
                                    class="form-control guardian_name uppercase" 
                                    name="guardian_name"
                                    autocomplete="off"
                                    value="{{$row->guardian_name}}"
                                    required>
                            </div>
                        </div>

                        <div class="form-group row my-3">
                            <label for="guardian_number" class="col-md-2 col-form-label text-md-right">GUARDIAN'S NUMBER <span class="required">*</span></label>
                            <div class="col-md-5">
                                <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <b>+63</b></span>
                                </div>
                                <input id="guardian_number" 
                                    type="guardian_number" 
                                    class="form-control mb-2" 
                                    name="guardian_number" 
                                    placeholder="Active 10-digit cellphone number (935 123 4567)"
                                    value="{{$row->guardian_number}}" 
                                    required autocomplete="guardian_number">
                                </div>
                                <section class="container alert-warning">
                                <i>
                                    <b>
                                        NOTE: Make sure this is valid 10-digit cellphone number (automatically prefixed with +63).
                                    </b> <br />
                                    This will be used in sending automated SMS alert to the guardian.
                                    The guardian won't receive any SMS if this field is not active and valid.
                                </i>
                                </section>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="guardian_address" class="col-md-2 col-form-label text-md-right">GUARDIAN'S ADDRESS <span class="required">*</span></label>
                            <div class="col-md-5">
                                <input id="guardian_address" type="text"
                                    placeholder="COMPLETE ADDRESS" 
                                    class="form-control guardian_address uppercase" 
                                    name="guardian_address"
                                    autocomplete="off"
                                    value="{{$row->guardian_address}}"
                                    required>
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
    id="profile-form" style="z-index:800 !important;">
    <div class="w3-modal-content w3-animate-top">
        <div class="col-md-12">
            <h5 class="col-md-12 header-title transform pointer pt-4"
                onclick="document.getElementById('profile-form').style.display='none'">
                <b>
                    <i class="fa fa-image"></i>
                    Change Profile Picture
                </b>
                <i class="fa fa-times pull-right"></i>
            </h5>
        </div>
        <div class="modal-body p-2">
            <form action="" method="POST" id="profile-form" enctype="multipart/form-data">
                @csrf
                <input type="file" name="profile_picture" id="profile_picture" class="w3-input">
                <div class="modal-footer p-0">
                    <button type="submit" class="btn btn-primary upload-button">
                        Upload Image
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
<input type="hidden" id="x-regCode" value="{{$row->region}}" readonly>
<input type="hidden" id="x-provCode" value="{{$row->province}}" readonly>
<input type="hidden" id="x-citymunCode" value="{{$row->city_municipality}}" readonly>
<input type="hidden" id="x-brgyCode" value="{{$row->barangay}}" readonly>

<script>
    $(function(){
        $('#sex').val('{{$row->sex}}');
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
                url:'{{route("passenger-update")}}',
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
                        $('.swal-button').addClass('reload');
                    }
                    else if(response.error)
                    {
                        swal("ERROR!", response.error, "error");
                    }
                }
            });
        });

        $(document).on('submit','#profile-form',function(e){
            e.preventDefault();
            var data    = new FormData();
            var passenger_id = $('#passenger_id').val();
            var files       = $('#profile_picture').prop('files');
            data.append('_token',_token);
            data.append('profile_picture',files[0]);
            data.append('passenger_id',passenger_id);
            $.ajax({
                url:'{{route("passenger-profile-update")}}',
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
                    document.getElementById('profile-form').style.display='none';
                    if(response.errors)
                    {
                        var message ='';
                        for(var i = 0; i < response.errors.length; i++)
                        {
                            message += response.errors[i];
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
                        document.getElementById('profile-form').style.display='none';
                        $('.swal-button').addClass('reload');
                    }
                }
            });
        });

        $(document).on('click', '.reload',function(){
            location.reload();
        });
    });
</script>
@endsection
