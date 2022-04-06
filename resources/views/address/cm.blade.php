@if(count($result) > 0)
<select id="citymunCode" name="city_municipality"
    class="citymunCode sans-semi" 
    required>
    <option value="" disabled selected>CITY/MUNICIPALITY</option>
    @foreach ($result as $row) 
        <option value="{{$row->citymunCode}}">
            <b>{{strtoupper($row->citymunDesc)}}</b>
        </option>   
    @endforeach
</select>
@else
<i>No records found!</i>
@endif