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
                <i class="fa fa-users"></i> MANAGE PASSENGERS
            </section>
            <section id="errors"></section>
            <div class="card">
                <section class="add-btn p-3 text-right">
                    <a href="/admin/passenger/add">
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
                                    <th>STUDENT ID #</th>
                                    <th>NAME</th>
                                    <th>ADDRESS</th>
                                    <th>GUARDIAN NAME</th>
                                    <th>CONTACT #</th>
                                    <th>ADDRESS</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i = 0;
                                ?>
                                @foreach($result as $row)
                                <?php
                                    
                                    $row2           = DB::table('regions')->where('id',$row->region)->first();
                                    $row3           = DB::table('provinces')->where('provCode',$row->province)->first();
                                    $row4           = DB::table('cm')->where('citymunCode',$row->city_municipality)->first();
                                    $row5           = DB::table('brgy')->where('brgyCode',$row->barangay)->first();
                                ?>
                                    <tr>
                                        <td>
                                           {{$i+=1}} 
                                        </td>
                                        <td>{{$row->student_id}}</td>
                                        <td>{{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}</td>
                                        <td class="uppercase">{{$row->address_line}} {{$row5->brgyDesc}} {{$row4->citymunDesc}} {{$row3->provDesc}}</td>
                                        <td class="uppercase">{{$row->guardian_name}}</td>
                                        <td class="uppercase">(+63){{$row->guardian_number}}</td>
                                        <td class="uppercase">{{$row->guardian_address}}</td>
                                        <td class="actions">
                                            <a href="/admin/passenger/{{$row->passenger_id}}/logs">
                                                <i class="btn btn-success fa fa-list"
                                                data-toggle="popover" 
                                                    data-content="LOGS"
                                                    data-trigger="hover"
                                                    data-placement="top"></i>
                                            </a>
                                            <a href="/admin/passenger/{{$row->passenger_id}}">
                                                <i class="btn btn-info fa fa-eye"
                                                    data-toggle="popover" 
                                                    data-content="VIEW INFO & LOGS"
                                                    data-trigger="hover"
                                                    data-placement="top"></i>
                                            </a>
                                            <a href="/admin/passenger/{{$row->passenger_id}}/edit">
                                                <i class="btn btn-primary fa fa-edit"
                                                data-toggle="popover" 
                                                    data-content="EDIT"
                                                    data-trigger="hover"
                                                    data-placement="top"></i>
                                            </a>
                                            <i class="btn btn-danger fa fa-trash delete"
                                                data-passenger_id="{{$row->passenger_id}}"
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
            var passenger_id = $(this).data('passenger_id');
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
                        url:'{{route("passenger-delete")}}',
                        method:'POST',
                        data:{
                            passenger_id:passenger_id,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                swal("SUCCESS!", response.success, "success");
                                //$('.swal-button').addClass('reload');
                                setInterval(function(){location.reload();},3000);
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

        $(document).on('click', '.reload',function(){
            location.reload();
        });

        $('#data-table').DataTable({
            "order": [[ 0, "desc" ]]
        });
    });
</script>
@endsection
