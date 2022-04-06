@if(isset($row))
<div class="w3-modal pt-2" id="view-details-modal" style="display:block;">
    <div class="w3-modal-content w3-animate-zoom card p-1 px-0" style="width:50%;">
        <div class="container p-0">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn green" 
                        onclick="document.getElementById('view-details-modal').style.display='none';">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>
            </div>
            <hr class="my-0 mb-4">
            <div class="row p-0 m-0">
                <div class="col-md-12 m-0 mb-1">
                    @if($with_requirements === 'yes')
                    <h4 class="p-0 my-0"><b>REQUEST</b></h4>
                    <hr class="p-0 my-0">
                    <div class="col-md-12 pl-5">
                    <table class="w3-table table-condensed table-sm">
                        <tr>
                            <td class="pl-1">NAME</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">ADDRESS</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->brgyDesc)}}, {{strtoupper($row->citymunDesc)}} {{strtoupper($row->provDesc)}} {{strtoupper($row->regDesc)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">REQUEST</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($request->aics_services)}}
                                </b>
                            </td>
                        </tr>
                    </table>
                    </div>

                    
                        @if(count($result) > 0)
                            <h4 class="mt-4 p-0 my-0"><b>DOCUMENTS / REQUIREMENTS</b></h4>
                            <hr class="p-0 my-0">
                                <center>
                                @foreach($result as $rows)
                                <a href="javascript:void(0)" class="w3-hover-blue pointer p-0 my-0 download"
                                        data-upload_id="{{$rows->upload_id}}"
                                        data-type="{{strtolower($rows->requirement_type)}}"
                                        data-file_name="{{$rows->uploaded_file}}">
                                    <p class="w3-hover-blue pointer p-0 my-0">
                                    {{$rows->uploaded_file}}
                                    </p>
                                </a>
                                @endforeach
                                </center>
                        @endif
                    <p class="text-right mt-5">
                        <button type="button" class="btn btn-primary px-4" 
                            onclick="document.getElementById('view-details-modal').style.display='none';">
                            OK
                        </button>
                    </p>
                    @else
                    <table class="w3-table table-condensed table-sm w3-small">
                        <tr>
                            <td class="pl-1">NAME</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->lastname)}}, {{strtoupper($row->firstname)}} {{strtoupper($row->middlename)}} {{strtoupper($row->suffix)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">SEX</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->gender)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">DATE OF BIRTH</td>
                            <td>
                                <b class="pl-4">
                                : {{date('F d, Y', strtotime($row->birthdate))}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">CONTACT #</td>
                            <td>
                                <b class="pl-4">
                                : (+63) {{strtoupper($row->contact_number)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">ADDRESS</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->brgyDesc)}}, {{strtoupper($row->citymunDesc)}} {{strtoupper($row->provDesc)}} {{strtoupper($row->regDesc)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">TYPE</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->type_of_id)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">ID #</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->id_number)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">OCCUPATION</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->occupation)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">MONTHLY INCOME</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->monthly_income)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">WORKPLACE &amp; ADDRESS</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->workplace_and_address)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">SECTOR</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->sector)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">HEALTH CONDITION</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->health_condition)}}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-1">BENEFICIARY TYPE</td>
                            <td>
                                <b class="pl-4">
                                : {{strtoupper($row->beneficiary_type)}} ({{strtoupper($row->ip_group)}})
                                </b>
                            </td>
                        </tr>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif