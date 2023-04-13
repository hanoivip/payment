<h1>Lịch sử chuyển xu</h1>
@if (!empty($mods))
	<table>
		<th>TK</th>
    	<th>Số xu</th>
    	<th>Lý do</th>
    	<th>Thời gian</th>
        @foreach ($mods as $mod)
        <tr>
            <td>{{$mod->acc_type == 1 ? "chính" : "phụ"}}</td>
            <td>{{$mod->balance}}</td>
            <td>{{$mod->reason}}</td>
            <td>{{$mod->time}}</td>
        </tr>
        @endforeach
    </table>
@else
	<p>Chưa chuyển lần nào!</p>
@endif
