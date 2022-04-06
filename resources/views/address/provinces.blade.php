@if(count($result) > 0)
<select id="provCode" name="province"
    class="provCode sans-semi" 
    required>
    <option value="" disabled selected>PROVINCE</option>
    @foreach ($result as $row) 
        <option value="{{$row->provCode}}">
            <b>{{strtoupper($row->provDesc)}}</b>
        </option>   
    @endforeach
</select>
@else
<i>No records found!</i>
@endif