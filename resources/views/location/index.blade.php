@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 main-body">
            <section class="window-title">
                <i class="fa fa-wrench"></i> LOCATION SETTINGS
            </section>
            <section id="errors"></section>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" id="form">
                        @csrf
                        <input id="location_id" 
                                type="hidden" 
                                class="form-control" 
                                name="location_id"
                                placeholder="ID" 
                                value="{{ $row->location_id }}" 
                                required 
                                readonly>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Company / Location Name') }}</label>

                            <div class="col-md-7">
                                <input id="name" 
                                type="text" 
                                class="form-control" 
                                name="name"
                                placeholder="Company / Location Name." 
                                value="{{ $row->name }}" 
                                required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="company_address" class="col-md-4 col-form-label text-md-right">{{ __('Company Address') }}</label>

                            <div class="col-md-7">
                                <input id="company_address" 
                                type="text" 
                                class="form-control" 
                                name="company_address" 
                                placeholder="Company Address"
                                value="{{ $row->company_address }}" 
                                required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="contact_info" class="col-md-4 col-form-label text-md-right">{{ __('Contact Information') }}</label>

                            <div class="col-md-7">
                                <textarea id="contact_info" 
                                type="text" 
                                class="form-control" 
                                name="contact_info" 
                                placeholder="List down the contact information of your company / institution." 
                                required
                                style="resize:none"
                                rows="4">{{$row->contact_info}}</textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0 mt-3">
                            <div class="col-md-11 text-md-right">
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

<script>
    $(function(){
        $('#form').on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:'{{route("location-save")}}',
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
                }
            });
        });
    });
</script>
@endsection
