@if (!empty($rank))
	<table>
		<tr>
			<th>Rank</th>
			<th>User</th>
			<th>Value</th>
		</tr>
		@foreach ($rank as $arr)
		<tr>
			<td>{{$arr[0]}}</td>
			<td>{{$arr[1]}}</td>
			<td>*******</td>
		</tr>
		@endforeach
	</table>
@else
	<p>Rank table will be updated soon!</p>
@endif