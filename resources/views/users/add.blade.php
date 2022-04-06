@extends('layouts.app')

@section('content')

<div class="col-md-12 p-0">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section class="window-title">
                <i class="fa fa-users"></i> MANAGE USERS ::
                <small>
                    <a href="/admin/register/user">User List</a> > Add New
                </small>
            </section>
            <section id="errors"></section>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" id="form">
                        @csrf

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
                                value="" required 
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
                                value="" required 
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
                                value="" 
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
                                        value="" 
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
                            <i class="fa fa-car"></i> Vehicle Information
                        </section>
                        <div class="form-group row">
                            <label for="plate_number" class="col-md-2 col-form-label text-md-right">{{ __('PLATE #') }}</label>
                            <div class="col-md-5">
                                <input id="plate_number" type="text"
                                    placeholder="VEHICLE'S PLATE #" 
                                    class="form-control plate_number uppercase" 
                                    name="plate_number"
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
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <label for="license" class="col-md-2 col-form-label text-md-right">{{ __('UPLOAD LICENSE') }}</label>
                            <div class="col-md-5">
                                <input type="file" name="license" id="license" class="form-control">
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
@if(!empty($row))
<input type="hidden" id="x-status" value="exists" readonly>
<input type="hidden" id="x-regCode" value="{{$row2->regCode}}" readonly>
<input type="hidden" id="x-provCode" value="{{$row2->provCode}}" readonly>
<input type="hidden" id="x-citymunCode" value="{{$row2->citymunCode}}" readonly>
<input type="hidden" id="x-brgyCode" value="{{$row2->brgyCode}}" readonly>
@else
<input type="hidden" id="x-status" value="null" readonly>
@endif
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        $('#form').on('submit',function(e){
            e.preventDefault();
            var data    = new FormData();
            var files   = $('#license').prop('files');
            data.append('_token',_token);
            data.append('license',files[0]);

            data.append('user_type', $('#user_type').val());
            data.append('firstname', $('#firstname').val());
            data.append('middlename', $('#middlename').val());
            data.append('lastname', $('#lastname').val());
            data.append('suffix', $('#suffix').val());
            data.append('email', $('#email').val());
            data.append('contact_number', $('#contact_number').val());
            data.append('region', $('#regCode').val());
            data.append('province', $('#provCode').val());
            data.append('city_municipality', $('#citymunCode').val());
            data.append('barangay', $('#brgyCode').val());
            data.append('address_line', $('#address_line').val());
            data.append('zip_code', $('#zip_code').val());
            data.append('plate_number', $('#plate_number').val());
            data.append('contact_person_name', $('#contact_person_name').val());
            data.append('contact_person_number', $('#contact_person_number').val());
            data.append('contact_person_address', $('#contact_person_address').val());
            $.ajax({
                url:'{{route("registration-store")}}',
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
                        $('.alert').html("");
                        swal("SUCCESS!", response.success, "success");
                        //$('.swal-button').addClass('.reload');
                        setInterval(function(){location.reload();},3000);
                    }
                    else if(response.error)
                    {
                        swal("ERROR!", response.error, "error");
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
