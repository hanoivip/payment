@extends('hanoivip::admin.layouts.admin')

@section('title', 'Lịch sử tài khoản xu')

@section('content')

<style type="text/css">
	table tr td{
		border: 1px solid;
	}
</style>

@if (empty($mods))
<p>Chưa có hoạt động nào!</p> 
@else

<table>
@foreach ($mods as $mod)
<tr>
    <td>{{$mod->acc_type == 1 ? "chính" : "phụ"}}</td>
    <td>{{$mod->balance}}</td>
    <td>{{$mod->reason}}</td>
    <td>{{$mod->time}}</td>
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
