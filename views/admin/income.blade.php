@extends('hanoivip::admin.layouts.admin')

@section('title', 'Thống kê doanh số')

@section('content')

<a href="{{route('ecmin.income.today')}}">Today income</a>

<a href="{{route('ecmin.income.thisMonth')}}">This month income</a>

<form method="post" action="{{route('ecmin.income.byTime')}}">
	{{csrf_field()}}
	<input type="date" name="start_time" id="start_time"/>
	<input type="date" name="end_time" id="end_time"/>
	<button type="submit">OK</button>
</form>

@endsection
