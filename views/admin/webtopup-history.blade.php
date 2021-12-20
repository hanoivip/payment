@extends('hanoivip::admin.layouts.admin')

@section('title', 'Lịch sử nạp thẻ - Webtopup')

@section('content')

<style type="text/css">
	table tr td{
		border: 1px solid;
	}=
</style>

@if (empty($submits))
<p>Chưa nạp lần nào!</p>
@else

<table>
@foreach ($submits as $submit)
<tr>
	@switch($submit->status)
		@case(0)
			<td>Thành công</td>
			@break
		@case(1)
			<td>Lỗi</td>
			@break
		@case(2)
			<td>Trễ</td>
			@break
		@case(3)
			<td>Sai m.giá</td>
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
