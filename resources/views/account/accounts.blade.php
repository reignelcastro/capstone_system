@extends('layouts.app')

@section('content')
<?php
    use App\User;
    use App\Location;

    if(isset($_GET['location_id']))
    {
        $result     = User::where('email','<>','mswd_admin')
                        ->where('location_id',$_GET['location_id'])
                        ->orderBy('id','DESC')
                        ->get();
    }
    else
    {
        $result     = User::where('email','<>','mswd_admin')->orderBy('id','DESC')->get();
    }
?>
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        Manage User Accounts
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section id="errors"></section>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="col-lg-12">
                        <form method="POST" action="" id="accounts-form">
                            @csrf
                            <div class="form-group row">
                                <label>Name:</label>
                                <input id="name" 
                                    type="text" 
                                    class="form-control uppercase" 
                                    name="name" required 
                                    autocomplete="off" autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Position:</label>
                                <input id="position" 
                                    type="text" 
                                    class="form-control uppercase" 
                                    name="position" required 
                                    autocomplete="off" autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Role:</label>
                                <select id="user_type" 
                                    type="text" 
                                    class="form-control" 
                                    name="user_type" required>
                                    <option value="ADMIN">SYSTEM ADMINISTRATOR</option>
                                    <option value="CLERK" selected>BARANGAY USER</option>
                                </select>
                            </div>

                            <div class="form-group row">
                                <label>Contact #:</label>
                                <input id="contact_number" 
                                    type="text" 
                                    class="form-control uppercase" 
                                    name="contact_number" required 
                                    autocomplete="off" autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Office</label>
                                <select id="location_id" name="location_id"
                                        class="form-control sans-semi" 
                                        required>
                                        <option value="" disabled selected>OFFICE</option>
                                    <?php
                                        $results     = Location::where('location_id','<>',1)
                                                            ->orderBy('location_id','DESC')
                                                            ->get();
                                        if(count($results) > 0)
                                        {
                                            foreach ($results as $rows) 
                                            {
                                                ?>
                                                <option value="{{$rows->location_id}}">
                                                    <b>{{strtoupper($rows->name)}}</b>
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

                            <div class="form-group row mt-5">
                                <label>Username:</label>
                                <input id="username" 
                                    type="password" 
                                    class="form-control"
                                    placeholder="AUTO-GENERATE" 
                                    name="username" required 
                                    autocomplete="off" readonly>
                            </div>
                            <div class="form-group row">
                                <label>Password:</label>
                                <input id="password" 
                                    type="password" 
                                    class="form-control"
                                    placeholder="AUTO-GENERATE" 
                                    name="password" required 
                                    autocomplete="off" readonly>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn green btn-green">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" onclick="$('#accounts-form')[0].reset();" class="btn green btn-green">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8 pl-0">
                    <h5 class="my-2"><b>Barangay Users' Accounts</b></h5>
                    <div class="card px-2 py-1">
                    <form action="" method="GET">
                        <?php
                            $results2   = Location::where('location_id','<>',1)
                                            ->orderBy('location_id','DESC')
                                            ->get();
                        ?>
                        <select name="location" id="location" class="form-control w-50 my-2">
                            <option value="ALL">ALL OFFICES</option>
                            @foreach($results2 as $rows2)
                                <option value="{{$rows2->location_id}}">
                                    {{strtoupper($rows2->name)}}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <div class="card p-0">
                       @if(count($result) > 0)
                       <center>
                       <p class="m-0 my-2">{{count($result)}} result/s found.</p>
                       </center>
                        <table class="table table-sm table-condensed">
                            <thead>
                                <tr>
                                    <th>NAME</th>
                                    <th>POSITION</th>
                                    <th>ROLE</th>
                                    <th>CONTACT #</th>
                                    <th>USERNAME</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result as $row)
                                    <tr>
                                        <td>{{strtoupper($row->name)}}</td>
                                        <td>{{strtoupper($row->position)}}</td>
                                        <td>{{strtoupper($row->user_type)}}</td>
                                        <td>+63{{$row->contact_number}}</td>
                                        <td>{{$row->email}}</td>
                                        <td>
                                            <center>
                                            @if($row->is_deactivated == true)
                                                <p class="bg-danger p-0 m-0">Deactivated</p>
                                            @else
                                                <p class="bg-success p-0 m-0">Active</p>
                                            @endif
                                            </center> 
                                        </td>
                                        <td class="actions">
                                            <a href="#" class="btn w3-border py-0 px-3 update"
                                                    data-id="{{$row->id}}"
                                                    data-attr="{{$row->is_deactivated}}">
                                                @if($row->is_deactivated == true)
                                                    <i class="bi bi-check2-square green"></i>
                                                @else
                                                    <i class="bi bi-person-x-fill w3-text-red"></i>
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                       @else
                       <center>
                           <p class="m-0 py-3">
                            <i>No record found in the database.</i>
                           </p>
                       </center>
                       @endif 
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($_GET['location_id']))
{
    ?>
        <script>
            $('#location').val("{{$_GET['location_id']}}");
        </script>
    <?php
}
?>
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');

        $('#accounts-form').on('submit',function(e){
            e.preventDefault();
            var rand    = Math.floor((Math.random() * 1000000) + 1);
            $('#username').val(rand);
            $('#password').val(rand);
            $.ajax({
                url:'{{route("user-account-save")}}',
                method:'POST',
                data:new FormData(this),
                contentType:false,
                cache:false,
                processData:false,
                dataType:'json',
                success:function(response){
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
        $(document).on('click', '.update', function () {
            var id = $(this).data('id');
            var attr = $(this).data('attr');
            var text;
            if(attr == 0)
            {
                text = "Are you sure you want to deactivate this account?";
            }
            else
            {
                text = "This account was seems to be deactived. Are you sure you want to reactivate this?";
            }
            swal({
                title: "Ohh, wait!",
                text: text,
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("user-account-update")}}',
                        method:'POST',
                        data:{
                            id:id,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                swal("SUCCESS!", response.success, "success");
                                setInterval(function(){location.reload();},3000);
                                //location.reload();
                            }
                            else if(response.error)
                            {
                                swal("ERROR!", response.error, "error");
                            }
                        }
                    });   
                }
            });
        });

        $(document).on('change', '#location',function(){
            var location_id = $(this).val();
            if(location_id !== 'ALL')
            {
                window.location.href = '/user-accounts?location_id='+location_id;
            }
            else
            {
                window.location.href = '/user-accounts';
            }
        });
    });
</script>
@endsection
