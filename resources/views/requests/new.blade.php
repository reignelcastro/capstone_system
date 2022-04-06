@extends('layouts.app')

@section('content')
@php
    use App\Beneficiary;

    if(isset($_GET['id']))
    {
        $row    = Beneficiary::where('beneficiary_id',$_GET['id'])->first();
    }
@endphp
<style>
    input[type="text"], select{
        height:30px;
        border:1px solid #aaa;
        border-radius:5px;
        font-family:'Sans Semi' !important;
    }
    select{
        width:230px;
    }
    input{
        text-transform:uppercase !important;
    }
    input[disabled], input[readonly]{
        background:#ccc;
    }
</style>
<div class="row p-0 m-0">
    <div class="col-md-12 header-top-title">
        Manage Request Information
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 main-body p-2">
            <h5><b>
                @if(isset($_GET['id']))
                    Edit Beneficiary's Information
                @else
                    Add New Request
                @endif
            </b></h5>
            <section id="errors"></section>
            <div class="row mt-3 w-100 p-0 m-0 w3-text-black">
                <form action="" method="POST" id="form">
                    @csrf
                    <input type="hidden" name="beneficiary_id" id="beneficiary_id"
                        value="@if(!empty($row)){{$row->beneficiary_id}}@endif"
                        readonly>
                    <input type="hidden" name="action" id="action" value="next" readonly>
                <div class="col-md-12 w3-border">
                    <div class="row">
                        <div class="col-md-10">
                            Name:<br />
                            <input type="text" placeholder="LAST NAME" id="lastname" name="lastname" value="@if(!empty($row)){{$row->lastname}}@endif" required> <b>,</b>
                            <input type="text" placeholder="FIRST NAME" id="firstname" name="firstname" value="@if(!empty($row)){{$row->firstname}}@endif" required>
                            <input type="text" placeholder="MIDDLE NAME" id="middlename" name="middlename" value="@if(!empty($row)){{$row->middlename}}@endif" required>
                            <input type="text" placeholder="SUFFIX" id="suffix" name="suffix" value="@if(!empty($row)){{$row->suffix}}@endif">
                        </div>
                        <div class="col-md-2">
                            Sex:<br />
                            <input type="radio" name="gender" value="MALE" required> <b class="mr-3">Male</b>
                            <input type="radio" name="gender" value="FEMALE"> <b>Female</b>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            Address:<br />
                            <select id="regCode" name="region" class="regCode sans-semi" required>
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
                            <span class="prov-container">
                                <input id="provCode" type="text" 
                                    class="province"
                                    placeholder="PROVINCE"
                                    autocomplete="off"
                                    required 
                                    readonly>
                            </span>
                            <span class="cm-container">
                                <input id="citymunCode" type="text" 
                                    class="citymunCode"
                                    placeholder="CITY / MUNICIPALITY" 
                                    autocomplete="off" required readonly>
                            </span>
                            <span class="brgy-container">
                                <input id="brgyCode" type="text" 
                                        class="brgyCode" 
                                        name="brgyCode"
                                        placeholder="BARANGAY"
                                        autocomplete="off"
                                        required 
                                        readonly>
                            </span>
                            <input id="street" type="text"
                                placeholder="STREET" 
                                class="street uppercase my-1 w-50 m-0" 
                                name="street"
                                value="@if(!empty($row)){{$row->street}}@endif"
                                autocomplete="off">
                            <input id="address_line" type="text"
                                placeholder="HOUSE #/ PUROK" 
                                class="address_line uppercase my-1 w-25 m-0"
                                name="address_line"
                                value="@if(!empty($row)){{$row->address_line}}@endif"
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="col-md-4">
                            TYPE OF ID:
                            <input type="text"  value="@if(!empty($row)){{$row->type_of_id}}@endif" name="type_of_id" id="type_of_id" class="w-100">
                        </div>
                        <div class="col-md-4">
                            ID Number:
                            <input type="text" value="@if(!empty($row)){{$row->id_number}}@endif" name="id_number" id="id_number" class="w-100">
                        </div>
                        <div class="col-md-4">
                            Date of Birth:
                            <input type="date" value="@if(!empty($row)){{$row->birthdate}}@endif" name="birthdate" id="birthdate" class="w-100">
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="col-md-4">
                            Occupation:
                            <input type="text" value="@if(!empty($row)){{$row->occupation}}@endif" name="occupation" id="occupation" class="w-100">
                        </div>
                        <div class="col-md-4">
                            Monthly Income:
                            <input type="text" value="@if(!empty($row)){{$row->monthly_income}}@endif" name="monthly_income" id="monthly_income" class="w-100">
                        </div>
                        <div class="col-md-4">
                            Contact #:
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    +63
                                </div>
                                <input type="text" value="@if(!empty($row)){{$row->contact_number}}@endif" name="contact_number" id="contact_number" class="w-75 ml-2">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="col-md-6">
                            Workplace and Address:
                            <input type="text" value="@if(!empty($row)){{$row->workplace_and_address}}@endif" name="workplace_and_address" id="workplace_and_address" class="w-100">
                        </div>
                        <div class="col-md-3">
                            Sector:
                            <select name="sector" id="sector">
                                <option value="" disabled selected>SECTOR</option>
                                <option value="SENIOR CITIZEN">A - SENIOR CITIZEN</option>
                                <option value="PREGRANT WOMAN">B - PREGRANT WOMAN</option>
                                <option value="BREASTFEEDING MOTHER">C - BREASTFEEDING MOTHER</option>
                                <option value="PWD">D - PWD</option>
                                <option value="SOLO PARENT">E - SOLO PARENT</option>
                                <option value="INDIGENT">F - INDIGENT</option>
                                <option value="N/A">N/A</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            Health Condition:
                            <select name="health_condition" id="health_condition">
                                <option value="" disabled selected>HEALTH CONDITION</option>
                                <option value="HEART PROBLEM">A - HEART PROBLEM</option>
                                <option value="HYPERTENSION">B - HYPERTENSION</option>
                                <option value="LUNG PROBLEM">C - LUNG PROBLEM</option>
                                <option value="DIABETES">D - DIABETES</option>
                                <option value="CANCER">E - CANCER</option>
                                <option value="N/A">N/A</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-1 my-3">
                        <div class="col-md-12">
                            <input type="radio" name="beneficiary_type" class="beneficiary_type ml-3"
                                value="UCT BENEFICIARY"> <b>UCT BENEFICIARY</b>
                            <input type="radio" name="beneficiary_type" class="beneficiary_type ml-3"
                                value="4PS BENEFICIARY"> <b>4PS BENEFICIARY</b>
                            <input type="radio" name="beneficiary_type" class="beneficiary_type ml-3"
                                value="INDIGENOUS PEOPLE"> <b>INDIGENOUS PEOPLE</b>
                            <input type="text" class="uppercase" name="ip_group" id="ip_group">
                            <input type="radio" name="beneficiary_type" class="beneficiary_type ml-3"
                                value="OTHERS"> <b>OTHERS</b>
                            <input type="text" class="uppercase" name="beneficiary_type_others" id="beneficiary_type_others">
                        </div>
                    </div>
                    <div class="row mt-1 my-3">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary next-button px-5">
                                <b>NEXT</b>
                            </button>
                        </div>
                    </div>
                    </form>
                    
                    <div class="row my-5 dependent-table">
                        <div class="col-md-12">
                            
                            <form action="" method="POST" id="dependent-form">
                                @csrf
                            <input type="hidden" name="beneficiary" id="beneficiary"
                                value="@if(!empty($row)){{$row->beneficiary_id}}@endif"
                                readonly>
                            <table class="w-100 table-bordered w3-small">
                                <thead>
                                    <tr>
                                        <th class="text-center">Member of the family</th>
                                        <th class="text-center">Relation to the head</th>
                                        <th class="text-center">Date of birth</th>
                                        <th class="text-center">Sex</th>
                                        <th class="text-center">Occupation</th>
                                        <th class="text-center">Sector</th>
                                        <th class="text-center">Health Condition</th>
                                        <th colspan="2">
                                            <input type="hidden" name="dependent_id" id="dependent_id" class="w-100" readonly>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="dependents-body">
                                    
                                </tbody>
                                <tfoot>
                                    <tr id="dependents-input-form">
                                        <td>
                                            <input type="text" name="member_name" id="member_name" class="w-100">
                                        </td>
                                        <td>
                                            <input type="text" name="member_relation" id="member_relation" class="w-100">
                                        </td>
                                        <td>
                                            <input type="date" name="date_of_birth" id="date_of_birth" style="width:150px;">
                                        </td>
                                        <td>
                                            <select name="sex" id="sex" class="w-100">
                                                <option value="MALE">M</option>
                                                <option value="FEMALE">F</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="member_occupation" id="member_occupation" class="w-100">
                                        </td>
                                        <td>
                                            <select name="member_sector" id="member_sector" style="width:150px;">
                                                <option value="" disabled selected>SECTOR</option>
                                                <option value="SENIOR CITIZEN">A - SENIOR CITIZEN</option>
                                                <option value="PREGRANT WOMAN">B - PREGRANT WOMAN</option>
                                                <option value="BREASTFEEDING MOTHER">C - BREASTFEEDING MOTHER</option>
                                                <option value="PWD">D - PWD</option>
                                                <option value="SOLO PARENT">E - SOLO PARENT</option>
                                                <option value="INDIGENT">F - INDIGENT</option>
                                                <option value="N/A">N/A</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="member_health_condition" id="member_health_condition" style="width:150px;">
                                                <option value="" disabled selected>HEALTH CONDITION</option>
                                                <option value="HEART PROBLEM">A - HEART PROBLEM</option>
                                                <option value="HYPERTENSION">B - HYPERTENSION</option>
                                                <option value="LUNG PROBLEM">C - LUNG PROBLEM</option>
                                                <option value="DIABETES">D - DIABETES</option>
                                                <option value="CANCER">E - CANCER</option>
                                                <option value="N/A">N/A</option>
                                            </select>
                                        </td>
                                        <td colspan="2" class="text-center">
                                            <button type="submit" class="btn btn-primary p-1 px-3">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="w3-small green" id="mode">
                                            <b class="ml-3"><i class="fa fa-arrow-right w3-medium"></i> Adding new record.</b>
                                        </td>
                                        <td colspan="2" class="text-center">
                                            <button type="button" class="btn btn-primary reset-button p-1 px-3">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            </form>
                            <div class="row mt-5">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-primary save-button px-5">
                                        <b>SAVE</b>
                                    </button>
                                </div>
                            </div>
                        </div>
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
<input type="hidden" id="x-regCode" value="{{$row->region}}" readonly>
<input type="hidden" id="x-provCode" value="{{$row->province}}" readonly>
<input type="hidden" id="x-citymunCode" value="{{$row->city_municipality}}" readonly>
<input type="hidden" id="x-brgyCode" value="{{$row->barangay}}" readonly>

<script type="text/javascript">
    $(function(){
        $('input[name=gender][value="{{$row->gender}}"]').prop("checked",true);
        $('#sector').val('{{$row->sector}}');
        $('#health_condition').val('{{$row->health_condition}}');
        $('input[name=beneficiary_type][value="{{$row->beneficiary_type}}"]').prop("checked",true);
    });
</script>
@else
<input type="hidden" id="x-status" value="null" readonly>
@endif
<script>
function load(_token, beneficiary_id){
    $.ajax({
        url:'{{route("dependents-load")}}',
        method:'POST',
        data:{
            beneficiary_id:beneficiary_id,
            _token:_token
        },
        dataType:'json',
        success:function(response){
            if(response.success)
            {
                $('#dependents-body').html(response.html);
            }
            else if(response.error)
            {
                $('.alert').html("");
                swal("ERROR!", response.error, "error");
                
            }
        }
    });
}

    $(function(){
        var _token = $('meta[name="csrf-token"]').attr('content');

        $('.dependent-table').hide();
        $('#ip_group').attr('disabled',true);
        $('#beneficiary_type_others').attr('disabled',true);

        $('.beneficiary_type').on('click', function(){
            var value   = $(this).val();
            if(value === 'INDIGENOUS PEOPLE')
            {
                $('#ip_group').attr('disabled',false);
                $('#beneficiary_type_others').attr('disabled',true);
            }
            else if(value === 'OTHERS')
            {
                $('#ip_group').attr('disabled',true);
                $('#beneficiary_type_others').attr('disabled',false);
            }
            else
            {
                $('#ip_group').attr('disabled',true);
                $('#beneficiary_type_others').attr('disabled',true);
            }
        });

        $('#form').on('submit',function(e){
            var action  = $('#action').val();
            e.preventDefault();
            $.ajax({
                url:'{{route("requests-store")}}',
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
                        if(action === 'save')
                        {
                            swal("SUCCESS!", response.success, "success");
                            loader();
                            setInterval(function(){
                                location.reload();
                            }, 1500);
                        }
                        else
                        {
                            $('#beneficiary').val(response.beneficiary_id);
                            $('#beneficiary_id').val(response.beneficiary_id);
                            $('.dependent-table').show();
                            $('.next-button').hide();
                            $('#action').val('save');
                        }
                    }
                    else if(response.error)
                    {
                        $('.alert').html("");
                        swal("ERROR!", response.error, "error");
                        
                    }
                }
            });
        });

        $(document).on('click', '.save-button', function () {
            $('#form').trigger('submit');
        });

        $(document).on('click', '.reset-button', function () {
            $('#dependent-form')[0].reset();
            $('#mode').html('<b class="ml-3"><i class="fa fa-arrow-right w3-medium"></i> Adding new record.</b>');
            $('#dependents-input-form').removeClass('bg-warning');
            $('.records').removeClass('bg-warning');
            $('#member_name').trigger('focus');
        });

        $('#dependent-form').on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:'{{route("beneficiary-save")}}',
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
                        load(_token,response.beneficiary_id);
                        $('#beneficiary').val(response.beneficiary_id);
                        $('.reset-button').trigger('click');
                    }
                    else if(response.error)
                    {
                        $('.alert').html("");
                        swal("ERROR!", response.error, "error");
                        
                    }
                }
            });
        });

        $(document).on('click','.member_edit',function(e){
            $('.records').removeClass('bg-warning');
            var dependent_id            = $(this).data('dependent_id');
            var member_name             = $(this).data('member_name');
            var member_relation         = $(this).data('member_relation');
            var date_of_birth           = $(this).data('date_of_birth');
            var sex                     = $(this).data('sex');
            var member_occupation       = $(this).data('member_occupation');
            var member_sector           = $(this).data('member_sector');
            var member_health_condition = $(this).data('member_health_condition');

            $('#dependent_id').val(dependent_id);
            $('#member_name').val(member_name);
            $('#member_relation').val(member_relation);
            $('#date_of_birth').val(date_of_birth);
            $('#sex').val(sex);
            $('#member_occupation').val(member_occupation);
            $('#member_sector').val(member_sector);
            $('#member_health_condition').val(member_health_condition);
            $('#mode').html('<b class="ml-3"><i class="fa fa-arrow-right w3-medium"></i> Editting a record</b>');
            $('#dependents-input-form').addClass('bg-warning');
            $('#record_'+dependent_id).addClass('bg-warning');
        });

        $(document).on('click', '.member_delete', function () {
            $('.reset-button').trigger('click');
            var dependent_id = $(this).data('dependent_id');
            var beneficiary_id = $(this).data('beneficiary_id');
            swal({
                title: "Ohh wait!",
                text: "Once deleted, it cannot be undone. Proceed anyway?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) 
                {
                    $.ajax({
                        url:'{{route("dependent-remove")}}',
                        method:'POST',
                        data:{
                            dependent_id:dependent_id,
                            _token:_token
                        },
                        dataType:'json',
                        success:function(response){
                            if(response.success)
                            {
                                load(_token,beneficiary_id);
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
@if(!empty($row))
<script>
    $(function(){
        var val   = '{{$row->beneficiary_type}}';
        var b_id  = '{{$row->beneficiary_id}}';
        var _t = $('meta[name="csrf-token"]').attr('content');
        
        $('#ip_group').val('{{$row->ip_group}}');
        $('#beneficiary_type_others').val('{{$row->beneficiary_type_others}}');
        if(val === 'INDIGENOUS PEOPLE')
        {
            $('#ip_group').attr('disabled',false);
            $('#beneficiary_type_others').attr('disabled',true);
        }
        else if(val === 'OTHERS')
        {
            $('#ip_group').attr('disabled',true);
            $('#beneficiary_type_others').attr('disabled',false);
        }
        else
        {
            $('#ip_group').attr('disabled',true);
            $('#beneficiary_type_others').attr('disabled',true);
        }

        $('#beneficiary').val(b_id);
        $('#beneficiary_id').val(b_id);
        $('.dependent-table').show();
        $('.next-button').hide();
        $('#action').val('save');
        load(_t,b_id);
    });
</script>
@endif
@endsection
