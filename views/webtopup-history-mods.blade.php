<h1>Lịch sử chuyển xu</h1>
@if (!empty($mods))
	<table>
		<th>Loại</th>
    	<th>Số xu</th>
    	<th>Lý do</th>
    	<th>Thời gian</th>
        @foreach ($mods as $mod)
        <tr>
            <td class="status{{$mod->balance > 0 ? 0 : 1}}">{{$mod->acc_type == 1 ? "Chính" : "Phụ"}}</td>
            <td>{{$mod->balance}}</td>
            <td>{{$mod->reason}}</td>
            <td>{{$mod->time}}</td>
        </tr>
        @endforeach
    </table>
@else
	<p>Chưa chuyển lần nào!</p>
@endif
