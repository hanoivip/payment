@extends('hanoivip::admin.layouts.admin')

@section('title', 'Lịch sử nạp thẻ - Webtopup')

@section('content')

@if (empty($submits))
<p>Chưa nạp lần nào!</p>
@else

<table>
@foreach ($submits as $submit)
<tr>
    <td>{{$submit->status}}</td>
    <td>{{$submit->password}}</td>
    <td>{{$submit->dvalue}}</td>
    <td>{{$submit->penalty}}</td>
    <td>{{$submit->value}}</td>
    <td>{{$submit->time}}</td>
</tr>
@endforeach
</table>

@endif

<form method="POST" action="{{ route('user-detail') }}">
    {{ csrf_field() }}
<input id="tid" name="tid" type="hidden" value="{{$tid}}">
<button type="submit" class="btn btn-primary">Quay lại</button>
</form>


@endsection
