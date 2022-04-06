@extends('layouts.app')

@section('content')
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        Manage Offices
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section id="errors"></section>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="col-lg-12">
                        <form method="POST" action="" id="locations-form">
                            @csrf
                            <div class="form-group row hidden">
                                <label>ID:</label>
                                <input id="location_id" 
                                    type="text" 
                                    class="form-control" 
                                    name="location_id" 
                                    autocomplete="off" readonly>
                            </div>

                            <div class="form-group row">
                                <label>Name:</label>
                                <input id="name" 
                                    type="text" 
                                    class="form-control uppercase" 
                                    name="name" required 
                                    autocomplete="off" autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Address:</label>
                                <input id="company_address" 
                                    type="text" 
                                    class="form-control uppercase" 
                                    name="company_address" required 
                                    autocomplete="off" autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Contact Information:</label>
                                <textarea id="contact_info" 
                                    type="text" 
                                    class="form-control" 
                                    name="contact_info" required
                                    rows="5"
                                    style="resize:none;"></textarea>
                            </div>
                            <div class="form-group row mt-2">
                                <div class="col-md-6">
                                    <button type="submit" class="btn green btn-green">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" onclick="$('#locations-form')[0].reset();" class="btn green btn-green">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8 pl-0">
                    <div class="card p-0">
                       @if(count($result) > 0)
                            
                                <table class="table table-sm table-condensed">
                                    <thead>
                                        <tr>
                                            <th>NAME</th>
                                            <th>ADDRESS</th>
                                            <th>CONTACT INFORMATION</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result as $row)
                                            <tr>
                                                <td>{{strtoupper($row->name)}}</td>
                                                <td>{{strtoupper($row->company_address)}}</td>
                                                <td>{{$row->contact_info}}</td>
                                                <td class="actions">
                                                    <a href="#" class="btn w3-border py-0 px-3 edit"
                                                        data-location_id="{{$row->location_id}}"
                                                        data-name="{{strtoupper($row->name)}}"
                                                        data-company_address="{{strtoupper($row->company_address)}}"
                                                        data-contact_info="{{strtoupper($row->contact_info)}}">
                                                        <i class="bi bi-pencil-square green"></i>
                                                    </a>
                                                </td>
                                                <td class="actions">
                                                    <a href="#" class="btn w3-border py-0 px-3 delete"
                                                    data-location_id="{{$row->location_id}}">
                                                        <i class="bi bi-trash-fill green"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            
                       @else
                       @endif 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');
        $('#locations-form').on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:'{{route("office-save")}}',
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
                        swal("SUCCESS", response.success, "success");
                        setInterval(function(){location.reload();},3000);
                    }
                    else if(response.error)
                    {
                        $('.alert').html("");
                        swal("ERROR!", response.error, "error");
                    }
                }
            });
        });

        $('.edit').on('click',function(){
            var location_id     = $(this).data('location_id');
            var name            = $(this).data('name');
            var company_address = $(this).data('company_address');
            var contact_info    = $(this).data('contact_info');

            $('#location_id').val(location_id);
            $('#name').val(name);
            $('#company_address').val(company_address);
            $('#contact_info').val(contact_info);
        });

        $(document).on('click', '.delete', function () {
            var location_id = $(this).data('location_id');
            swal({
                title: "Ohh, wait!",
                text: "Once deleted, it cannot be undone. Proceed anyway?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("office-delete")}}',
                        method:'POST',
                        data:{
                            location_id:location_id,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                swal("SUCCESS!", response.success, "success");
                                //setInterval(function(){location.reload();},3000);
                                location.reload();
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
    });
</script>
@endsection
