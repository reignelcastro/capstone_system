@extends('layouts.front')

@section('content')

<style>
    body{
        background:#188038 !important;
    }
    .hidden{
        display:none !important;
    }
    .card{
        border-radius:30px !important;
    }
    #header-logo{
        width:150px;
        margin-bottom:20px;
    }
    label{
        font-weight:bold;
        font-size:11px;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 px-4 pt-5 p-0">
            <div class="card">
                <div class="card-body">
                    <p>
                        <center>
                        <img src="{{asset('images/dswd.jpg')}}" id="header-logo" class="w3-border" alt="LOGO">
                        </center>
                    </p>
                    <form method="POST" action="{{ route('login') }}" id="form">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>USERNAME:</label>
                                <div class="input-group">
                                <input id="email" 
                                    type="text" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    name="email"
                                    placeholder="USERNAME" 
                                    value="{{ old('email') }}" 
                                    required autocomplete="email" autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>PASSWORD:</label>
                                <div class="input-group">
                                <input id="password" 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password"
                                    placeholder="PASSWORD" 
                                    required autocomplete="current-password">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row hidden">
                            <div class="col-md-12 p-0 m-0">
                                <div class="form-check">
                                <input type="checkbox" id="show_password" style="cursor:pointer;"> Show Password
                                </div>
                            </div>
                        </div>

                        <div class="form-group row hidden">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success px-5 py-2">
                                    <b>{{ __('LOG IN') }}</b>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
    
      $(document).on('change','#show_password',function()
      {
        if ($(this).is(':checked')) 
        {
            $('#password').attr('type',"text");
        }
        else
        {
            $('#password').attr('type',"password");
        }
      });

    });
  </script>
@endsection
