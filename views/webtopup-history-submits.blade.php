<h1>Lịch sử nạp thẻ</h1>
@if (!empty($submits))
    <table>
    <tr>
    	<th>Trạng thái</th>
    	<th>Mã thẻ</th>
    	<th>Gtri khai</th>
    	<th>Phạt</th>
    	<th>Gtri nhận</th>
    	<th>Tgian</th>
    </tr>
    @foreach ($submits as $submit)
    <tr>
    	@switch($submit->status)
    		@case(0)
    			<td class="status{{$submit->status}}">Đúng</td>
    			@break
    		@case(1)
    			<td class="status{{$submit->status}}">Sai</td>
    			@break
    		@case(2)
    			<td class="status{{$submit->status}}">Trễ</td>
    			@break
    		@case(3)
    			<td class="status{{$submit->status}}">Đúng(phạt)</td>
    			@break
    	@endswitch
        <td>{{$submit->password}}</td>
        <td>{{$submit->dvalue}}</td>
        <td>{{$submit->penalty}}%</td>
        <td>{{$submit->value}}</td>
        <td>{{$submit->time}}</td>
    </tr>
    @endforeach
    </table>
@else
	<p>Chưa nạp lần nào!</p>
@endif