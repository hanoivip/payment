<h1>Lịch sử nạp thẻ</h1>
@if (!empty($submits))
    <table>
    <tr>
    	<th>Status</th>
    	<th>Card password</th>
    	<th>User choosen</th>
    	<th>User penalty</th>
    	<th>Real amount</th>
    	<th>Time</th>
    </tr>
    @foreach ($submits as $submit)
    <tr>
    	@switch($submit->status)
    		@case(0)
    			<td>Valid</td>
    			@break
    		@case(1)
    			<td>Invalid</td>
    			@break
    		@case(2)
    			<td>Delay</td>
    			@break
    		@case(3)
    			<td>Valid (pen)</td>
    			@break
    	@endswitch
        <td>{{$submit->password}}</td>
        <td>{{$submit->dvalue}}</td>
        <td>{{$submit->penalty}}</td>
        <td>{{$submit->value}}</td>
        <td>{{$submit->time}}</td>
    </tr>
    @endforeach
    </table>
@else
	<p>Chưa nạp lần nào!</p>
@endif