@extends('layouts.app')

@section('content')
<?php
use App\Address;
use Illuminate\Support\Facades\DB;
?>
<div class="col-md-12 p-0">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body">
            <section class="window-title">
                <i class="fa fa-users"></i> MANAGE USERS
            </section>
            <section id="errors"></section>
            <div class="card">
                <section class="add-btn p-3 text-right">
                    <a href="/admin/register/user/add">
                        <button type="button" class="btn add btn-default">
                            <i class="fa fa-plus"></i> Add New
                        </button>
                    </a>
                </section>

                @if(count($result) > 0)
                    <div class="col-md-12 p-2 body-container">
                        <table class="table" id="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>CONTACT #</th>
                                    <th>ADDRESS</th>
                                    <th>USER TYPE</th>
                                    <th>LICENSE</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i = 0;
                                ?>
                                @foreach($result as $row)
                                <?php
                                    $row1           = Address::where('address_id',$row->address)->first();
                                    $row2           = DB::table('regions')->where('id',$row1->region)->first();
                                    $row3           = DB::table('provinces')->where('provCode',$row1->province)->first();
                                    $row4           = DB::table('cm')->where('citymunCode',$row1->city_municipality)->first();
                                    $row5           = DB::table('brgy')->where('brgyCode',$row1->barangay)->first();
                                ?>
                                    <tr>
                                        <td>{{$i+=1}}</td>
                                        <td>{{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}</td>
                                        <td>{{$row->email}}</td>
                                        <td>(+63){{$row->contact_number}}</td>
                                        <td class="uppercase">{{$row1->address_line}} {{$row5->brgyDesc}} {{$row4->citymunDesc}} {{$row3->provDesc}}</td>
                                        <td>{{$row->user_type}}</td>
                                        <td>
                                            <center>
                                            @if(!empty($row->license))
                                                <i class="fa fa-check-square-o w3-large w3-text-green"></i>
                                            @endif
                                            </center>
                                        </td>
                                        <td class="actions">
                                            <a href="/admin/register/user/{{$row->id}}/logs">
                                                <i class="btn btn-success fa fa-list"
                                                    data-toggle="popover" 
                                                    data-content="LOGS"
                                                    data-trigger="hover"
                                                    data-placement="top"></i>
                                            </a>
                                            <a href="/admin/register/user/{{$row->id}}">
                                                <i class="btn btn-info fa fa-eye"
                                                    data-toggle="popover" 
                                                    data-content="VIEW DATA & LOGS"
                                                    data-trigger="hover"
                                                    data-placement="top"></i>
                                            </a>
                                            <a href="/admin/register/user/{{$row->id}}/edit">
                                                <i class="btn btn-primary fa fa-edit"
                                                data-toggle="popover" 
                                                    data-content="EDIT"
                                                    data-trigger="hover"
                                                    data-placement="top"></i>
                                            </a>
                                            <i class="btn btn-danger fa fa-trash delete"
                                                data-id="{{$row->id}}"
                                                data-toggle="popover" 
                                                data-content="DELETE"
                                                data-trigger="hover"
                                                data-placement="top"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                <div class="row justify-content-center">
                    <div class="col-md-6 alert alert-warning">
                        <center>
                            <b>
                                <i>
                                    <i class="fa fa-files-o"></i> No record found.
                                </i>
                            </b>
                        </center>
                    </div>
                </div>
                @endif
                    
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');

        $(document).on('click', '.delete', function () {
            var id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "Once deleted, it cannot be undone. Proceed anyway?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("registration-delete")}}',
                        method:'POST',
                        data:{
                            id:id,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                setInterval(function(){
                                    location.reload();
                                },3000);

                                swal("SUCCESS!", response.success, "success");
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

        $('#data-table').DataTable({
            "order": [[ 0, "desc" ]]
        });
    });
</script>
@endsection
