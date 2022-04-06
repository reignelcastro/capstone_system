@extends('layouts.app')

@section('content')
<style>
    .cke_editable p{
        padding:0 !important;
        margin:0 !important;
        line-height:1;
    }
    #cke_16, #cke_17,#cke_19{
        display:none !important;
    }
</style>
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        Manage AICS Services
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section id="errors"></section>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="col-lg-12">
                        <form method="POST" action="" id="services-form">
                            @csrf
                            <div class="form-group row hidden">
                                <label>ID:</label>
                                <input id="services_id" 
                                    type="text" 
                                    class="form-control" 
                                    name="services_id" 
                                    autocomplete="off" readonly>
                            </div>

                            <div class="form-group row">
                                <label>AICS Services:</label>
                                <input id="aics_services" 
                                    type="text" 
                                    class="form-control uppercase" 
                                    name="aics_services" required 
                                    autocomplete="off" autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Description:</label>
                                <textarea id="description" 
                                    type="text" 
                                    class="form-control" 
                                    name="description" required
                                    rows="3"
                                    style="resize:none;"></textarea>
                            </div>

                            <div class="form-group row mt-2 text-center">
                                <button type="button" class="btn btn-success p-0 px-3 add_requirements">
                                    <i class="fa fa-plus"></i> Add Requirements
                                </button>
                            </div>

                            <div class="form-group row">
                                <label>Requirements:</label>
                                <div class="input-group">
                                    <input id="requirement_description" 
                                        type="text" 
                                        class="form-control uppercase" 
                                        name="requirement_description" required 
                                        autocomplete="off" autofocus>
                                    <div class="input-group-append">
                                        <select name="requirement_type" id="requirement_type" class="form-control">
                                            <option value="IMAGE">IMAGE</option>
                                            <option value="PDF">PDF</option>
                                            <option value="DOCX">WORD (.docx)</option>
                                            <option value="XLSX">EXCEL (.xlsx)</option>
                                        </select>
                                        <button type="button" class="btn btn-success" id="save_requirement">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 mt-2" id="requirements-container">
                            </div>

                            <div class="form-group row mt-2">
                                <div class="col-md-6">
                                    <button type="button" class="btn green btn-green" id="done">
                                        <span class="bi bi-folder-plus"></span>
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn green btn-green" onclick="location.reload();">
                                        <span class="fa fa-refresh"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8 pl-0">
                    <h5 class="my-2"><b>AICS Services</b></h5>
                    <div class="card px-2 py-1 pb-5">
                    <div class="row">
                        <div class="col-md-6">
                            <select name="services" id="services" class="form-control my-2">
                                <option value="" disabled selected>AICS Services</option>
                                @foreach($result as $row)
                                    <option value="{{$row->services_id}}">
                                        {{strtoupper($row->aics_services)}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 pt-5 pr-5">
                            <div id="actions-container" class="text-md-right w3-xlarge"></div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <p class="px-4 my-0"><b>DESCRIPTION</b></p>
                        <div class="col-md-12" id="description-container">
                            <div class="card py-5">
                                Detailed description of the offered service.
                            </div>
                        </div>

                        <p class="px-4 my-0 mt-4"><b>REQUIREMENTS</b></p>
                        <div class="col-md-12" id="requirements2-container">
                            <div class="card py-5">
                                List of required documents, etc.
                            </div>
                        </div>

                        <p class="px-4 my-0 mt-4"><b>FLOWCHART</b></p>
                        <div class="col-md-12">
                            <div class="card p-0">
                            <form action="" method="POST" id="flowchart-form" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group p-2">
                                <input type="hidden" name="fc_services_id" id="fc_services_id" class="form-control" readonly>
                                <input type="file" name="flowchart_file" id="flowchart_file" class="form-control">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-upload"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="m-2 p-2 card" id="flowchart-container"></div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.16.2/basic/ckeditor.js"></script>
<script>
function disable(){
    $('#requirement_description').attr('disabled',true);
    $('#requirement_type').attr('disabled',true);
    $('#save_requirement').attr('disabled', true);
    $('#done').attr('disabled', true);
}
function enable(){
    $('#requirement_description').attr('disabled',false);
    $('#requirement_type').attr('disabled',false);
    $('#save_requirement').attr('disabled',false);
    $('#done').attr('disabled', false);
}

function load(_token, services_id, source){
    $.ajax({
        url:'{{route("requirements-load")}}',
        method:'POST',
        data:{
            services_id:services_id,
            source:source,
            _token:_token
        },
        dataType:'json',
        success:function(response){
            if(response.success)
            {
                if(source === 'form')
                {
                    $('#requirements-container').html(response.html);
                }
                else
                {
                    $('#description-container').html(response.description);
                    $('#requirements2-container').html(response.html);
                    $('#flowchart-container').html(response.flowchart);
                    $('#fc_services_id').val(response.services_id);
                    $('#actions-container').html(response.actions);
                }
            }
        }
    });
}

function save(_token, source){
    var services_id      = $('#services_id').val();
    var aics_services   = $('#aics_services').val();
    var description     = CKEDITOR.instances["description"].getData();
    $.ajax({
        url:'{{route("services-store")}}',
        method:'POST',
        data:{
            services_id:services_id,
            aics_services:aics_services,
            description:description,
            _token:_token
        },
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
                $('#errors').html('');
                if(source == 'edit')
                {
                    swal("SUCCESS!", "Service record has been saved!", "success");
                    loader();
                    setInterval(function(){
                        window.location.href = '/services?id='+services_id;
                    },3000);
                }
                else
                {
                    $('#services_id').val(response.services_id);
                    enable();
                    $('#done').attr('data-edit','yes');
                }
            }
            else if(response.error)
            {
                $('#errors').html('');
                swal("ERROR!", response.error, "error");
            }
        }
    });
}
    $(function(){
        disable();
        var _token = $('meta[name="csrf-token"]').attr('content');
        CKEDITOR.replace('description');

        $('.add_requirements').on('click',function(){
            save(_token, 'save');
        });

        $('#save_requirement').on('click',function(){
            var services_id             = $('#services_id').val();
            var requirement_description = $('#requirement_description').val();
            var requirement_type        = $('#requirement_type').val();

            $.ajax({
                url:'{{route("services-save")}}',
                method:'POST',
                data:{
                    services_id:services_id,
                    requirement_description:requirement_description,
                    requirement_type:requirement_type,
                    _token:_token
                },
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
                        $('#errors').html('');
                        load(_token, services_id, 'form');
                        enable();
                        $('#requirement_description').val("");
                        $('#requirement_type').val("IMAGE");
                        $('#requirement_description').trigger('focus');
                    }
                    else if(response.error)
                    {
                        $('#errors').html('');
                        swal("ERROR!", response.error, "error");
                    }
                }
            });
        });

        $('#done').on('click',function(){
            var attr    = $(this).attr('data-edit');

            if(attr === 'yes')
            {
                save(_token, 'edit');
            }
            else
            {
                swal("SUCCESS!", "Service record has been saved!", "success");
                loader();
                setInterval(function(){
                    var services_id     = $('#services_id').val();
                    window.location.href = '/services?id='+services_id;
                    //location.reload();
                },3000);
            }
        });

        $(document).on('click','.edit',function(){
            var services_id = $(this).data('services_id');
            var aics_services = $(this).data('aics_services');
            var description = $(this).data('description');
            $('#services_id').val(services_id);
            $('#aics_services').val(aics_services);
            CKEDITOR.instances["description"].setData(description);
            enable();
            $('.add_requirements').hide();
            load(_token, services_id, 'form');
            $('#done').attr('data-edit','yes');
        });

        $(document).on('click', '.delete', function () {
            var services_id = $(this).data('services_id');
            var action      = $(this).data('action');
            swal({
                title: "",
                text: "Are you sure you want to "+action+" this record?",
                icon: "warning",
                buttons: true,
                buttons:['Cancel','Yes'],
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("services-delete")}}',
                        method:'POST',
                        data:{
                            services_id:services_id,
                            action:action,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                swal("SUCCESS!", response.success, "success");
                                loader();
                                setInterval(function(){
                                    window.location.href = "/services";
                                },3000);
                            }
                            else if(response.error)
                            {
                                swal("ERROR!", response.error, "error");
                            }
                            else if(response.errors)
                            {
                                swal("ERROR!", "Data security error. This record is not allowed to be deleted.", "error");
                            }
                        }
                    });   
                }
            });
        });

        $(document).on('click', '.remove', function () {
            var table          = $(this).data('table');
            
            if(table === 'requirements')
            {
                var id = $(this).data('requirement_id');
            }
            else
            {
                var id = $(this).data('flowchart_id');
            }
            var services_id     = $(this).data('services_id');
            swal({
                title: "Ohh, wait!",
                text: "Once deleted, it cannot be undone. Proceed anyway?",
                icon: "warning",
                buttons: true,
                buttons:['Cancel','Yes'],
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("requirement-remove")}}',
                        method:'POST',
                        data:{
                            id:id,
                            table:table,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                swal("SUCCESS!", response.success, "success");
                                if(table === 'requirements')
                                {
                                    $('#req_'+id).remove();
                                }
                                else
                                {
                                    $('#fc_'+id).remove();
                                    load(_token, services_id, 'get');
                                }
                            }
                            else if(response.error)
                            {
                                swal("ERROR!", response.error, "error");
                            }
                            else if(response.errors)
                            {
                                swal("ERROR!", "Data security error. This record is not allowed to be deleted.", "error");
                            }
                        }
                    });   
                }
            });
        });

        $(document).on('change', '#services',function(){
            var services_id = $(this).val();
            window.location.href = '/services?id='+services_id;
        });

        // $(document).on('click','.flowchart',function(){
            
        //     swal("FLOWCHART DATA: Instruction", {
        //         content: "input",
        //     })
        //     .then((value) => {
        //         var instruction = value;
        //         var services_id = $(this).data('services_id');
                
        //         if(instruction !== null)
        //         {
        //             $.ajax({
        //                 url:'{{route("flowchart-save")}}',
        //                 method:'POST',
        //                 data:{
        //                     instruction:instruction,
        //                     services_id:services_id,
        //                     _token:_token
        //                 },
        //                 dataType:'json',
        //                 success:function(response){
        //                     if(response.errors)
        //                     {
        //                         var message = "";
        //                         for(var i = 0; i < response.errors.length; i++)
        //                         {
        //                             message += '<p class="p-0 m-0">'+ response.errors[i]+'</p>';
        //                         }
        //                         $('#errors').html('<div class="alert alert-danger">'+message+'</div>');
        //                     }
        //                     else if(response.success)
        //                     {
        //                         $('#errors').html('');
        //                         load(_token, services_id, 'get');
        //                     }
        //                     else if(response.error)
        //                     {
        //                         $('#errors').html('');
        //                         swal("ERROR!", response.error, "error");
        //                     }
        //                 }
        //             });
        //         }
        //     });
        // });

        $(document).on('submit','#flowchart-form',function(e){
            e.preventDefault();
            var data    = new FormData();
            var files   = $('#flowchart_file').prop('files');
            var services_id     = $('#fc_services_id').val();
            data.append('_token',_token);
            data.append('services_id',services_id);
            data.append('flowchart_file',files[0]);

            $.ajax({
                url:'{{route("flowchart-file-upload")}}',
                method:'POST',
                data:data,
                dataType:'json',
                contentType:false,
                cache:false,
                processData:false,
                success:function(response){
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
                        loader();
                        setInterval(function(){
                            location.reload();
                        },3000);
                    }
                }
            });
        });
    });
</script>
<?php
if(isset($_GET['id']))
{
    ?>
        <script>
            var t   = $('meta[name="csrf-token"]').attr('content');
            $('#services').val("{{$_GET['id']}}");
            load(t, "{{$_GET['id']}}", "get");
        </script>
    <?php
}
?>
@endsection
