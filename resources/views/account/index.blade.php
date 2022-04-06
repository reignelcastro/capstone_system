@extends('layouts.app')

@section('content')
<style>
    .form-group.row{
        margin:10px 0 !important;
    }
</style>
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        Manage Personal Information
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section id="errors"></section>
            <div class="col-md-12">
                <div class="card-body">
                <center>
                    <h5 class="m-0 mb-4">
                        <b>
                            Manage Account
                        </b>
                    </h5>
                </center>
                <form method="POST" action="" id="account-form">
                        @csrf
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Username</label>

                            <div class="col-md-6">
                                <input id="email" 
                                type="text" 
                                class="form-control" 
                                name="email"
                                value="{{ Auth::user()->email }}" 
                                required>
                            </div>
                        </div>

                        <div class="form-group row hidden">
                            <label for="contact_number" class="col-md-4 col-form-label text-md-right">{{ __('Contact Number') }}</label>

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
                                    value="{{ Auth::user()->contact_number }}" 
                                    required autocomplete="contact_number">
                            </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="old_password" class="col-md-4 col-form-label text-md-right">{{ __('Old Password') }}</label>

                            <div class="col-md-6">
                                <input id="old_password" 
                                type="password" class="form-control password" 
                                name="old_password"
                                placeholder="Old Password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" 
                                type="password" class="form-control password" 
                                name="password"
                                placeholder="New Password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" 
                                type="password" class="form-control password" 
                                placeholder="Confirm your password."
                                name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row hidden">
                        <label class="col-md-4 col-form-label text-md-right"></label>
                            <div class="col-md-6 p-0 my-2">
                                <div class="form-check">
                                <input type="checkbox" id="show_password" style="cursor:pointer;"> Show Password
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0 mt-3">
                            <div class="col-md-10 text-md-right">
                                <button type="button" id="submit" class="btn btn-primary account-submit">
                                    Submit
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

<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');

        $('#submit').on('click',function(){
            
            $('#account-form').submit();
            
            // swal("For verification purposes, type your current password:", {
            //     content: "input",
            // })
            // .then((value) => {
            //     var password    = value;
            
            //     $.ajax({
            //         url:'{{route("account-verify")}}',
            //         method:'POST',
            //         data:{
            //             password:password,
            //             _token:_token
            //         },
            //         dataType:'json',
            //         beforeSend:function(){
            //             $('.alert').html("");
            //         },
            //         success:function(response){
            //             if(response.errors)
            //             {
            //                 swal("VERIFICATION REQUIRED!","You must input current password correctly to verify.", "error");
            //             }
            //             else if(response.success)
            //             {
            //                 $('.alert').html("");
            //                 $('#account-form').submit();
            //             }
            //             else if(response.error)
            //             {
            //                 $('.alert').html("");
            //                 swal("ERROR!", response.error, "error");
                            
            //             }
            //         }
            //     });
            // });

            // $('.swal-content__input').attr('type','password');
        });
        $('#account-form').on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:'{{route("account-update")}}',
                method:'POST',
                data:new FormData(this),
                contentType:false,
                cache:false,
                processData:false,
                dataType:'json',
                beforeSend:function(){
                    $('.alert').html("");
                    $('.account-submit').attr('disabled',true);
                    $('.account-submit').html("Processing...");
                },
                success:function(response){
                    $('.account-submit').attr('disabled',false);
                    $('.account-submit').html("Submit");
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
                        $('.alert').html("");
                        swal("ERROR!", response.error, "error");
                        
                    }
                }
            });
        });

        $(document).on('click', '.reload',function(){
            location.reload();
        });

        $(document).on('change','#show_password',function()
        {
            if ($(this).is(':checked')) 
            {
                $('.password').attr('type',"text");
            }
            else
            {
                $('.password').attr('type',"password");
            }
        });
    });
</script>
@endsection
