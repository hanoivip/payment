@extends('hanoivip::admin.layouts.admin')

@section('title', 'Webtopup history')

@section('content')

<style type="text/css">
	table tr td{
		border: 1px solid;
	}
	table tr th{
		border: 1px solid;
	}
</style>

@if (empty($submits))
<p>Have no submitted card!</p>
@else

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
    <td>
    	@if ($submit->status != 1)
    		<form method="POST" action="{{ route('ecmin.webtopup.retry') }}">
                {{ csrf_field() }}
            <input id="receipt" name="receipt" type="hidden" value="{{$submit->trans}}">
            <button type="submit" class="btn btn-primary">Trả</button>
            </form>
    	@endif
    	@if ($submit->status == 1)
    		<form method="POST" action="{{ route('ecmin.webtopup.check') }}">
                {{ csrf_field() }}
            <input id="receipt" name="receipt" type="hidden" value="{{$submit->trans}}">
            <button type="submit" class="btn btn-primary">Ktra</button>
            </form>
    	@endif
    </td>
</tr>
@endforeach
</table>
@for ($i=0; $i<$total_page; ++$i)
	<a href="{{route('ecmin.webtopup.history', ['page' => $i, 'tid' => $tid])}}">{{$i}}</a>
@endfor

@endif

<form method="POST" action="{{ route('user-detail') }}">
    {{ csrf_field() }}
<input id="tid" name="tid" type="hidden" value="{{$tid}}">
<button type="submit" class="btn btn-primary">Quay lại</button>
</form>


@endsection
