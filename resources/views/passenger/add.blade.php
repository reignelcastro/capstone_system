@extends('layouts.app')

@section('content')

<div class="col-md-12 p-0">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section class="window-title">
                <i class="fa fa-users"></i> MANAGE PASSENGERS ::
                <small>
                    <a href="/admin/passenger">Passenger's List</a> > Add New
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
                        <div class="form-group row">
                            <label for="student_id" class="col-md-2 col-form-label text-md-right">{{ __('STUDENT ID #') }}</label>

                            <div class="col-md-6">
                                <input id="student_id" 
                                type="text" 
                                class="form-control" 
                                name="student_id"
                                placeholder="STUDENT ID #" 
                                value="" 
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
                                value="" required 
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
                                value="" required 
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
                                value="" required 
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
                                value="" 
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
                                value="" 
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
                                    value="" 
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
                                    value="" 
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
                                    required>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <label for="profile_picture" class="col-md-2 col-form-label text-md-right">{{ __('PROFILE PICTURE') }}</label>
                            <div class="col-md-5">
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control">
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
<div class="row w3-hide">
    <div class="col-md-12 w3-hide">
        <input type="hidden" id="img_loader" value="<i class='fa fa-refresh'></i>" height='30' width='40'>" readonly>
        <input type="hidden" id="r-1" value="{{route('provinces')}}" readonly>
        <input type="hidden" id="r-2" value="{{route('cm')}}" readonly>
        <input type="hidden" id="r-3" value="{{route('brgy')}}" readonly>
    </div>
</div>
<input type="hidden" id="x-status" value="null" readonly>
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        $('#form').on('submit',function(e){
            e.preventDefault();
            var data    = new FormData();

            var files   = $('#profile_picture').prop('files');
            data.append('_token',_token);
            data.append('profile_picture',files[0]);

            data.append('student_id', $('#student_id').val());
            data.append('firstname', $('#firstname').val());
            data.append('middlename', $('#middlename').val());
            data.append('lastname', $('#lastname').val());
            data.append('suffix', $('#suffix').val());
            data.append('sex', $('#sex').val());
            data.append('email', $('#email').val());
            data.append('contact_number', $('#contact_number').val());
            data.append('region', $('#regCode').val());
            data.append('province', $('#provCode').val());
            data.append('city_municipality', $('#citymunCode').val());
            data.append('barangay', $('#brgyCode').val());
            data.append('address_line', $('#address_line').val());
            data.append('zip_code', $('#zip_code').val());
            data.append('guardian_name', $('#guardian_name').val());
            data.append('guardian_number', $('#guardian_number').val());
            data.append('guardian_address', $('#guardian_address').val());

            $.ajax({
                url:'{{route("passenger-store")}}',
                method:'POST',
                data:data,
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
                        setInterval(function(){
                            location.reload();
                        },3000);
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
    });
</script>
@endsection
