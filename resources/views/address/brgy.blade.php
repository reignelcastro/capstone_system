@if(count($result) > 0)
<select id="brgyCode" name="barangay"
    class="brgyCode sans-semi" 
    required>
    <option value="" disabled selected>BARANGAY</option>
    @foreach ($result as $row) 
        <option value="{{$row->brgyCode}}">
            <b>{{strtoupper($row->brgyDesc)}}</b>
        </option>   
    @endforeach
</select>
@else
<i>No records found!</i>
@endif