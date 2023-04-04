@extends('hanoivip::admin.layouts.admin')

@section('title', 'Kết quả theo tháng')

@section('content')

<form method="post" action="{{route('ecmin.stats.bymonth')}}">
	{{csrf_field()}}
	<input name="month" id="month" placeholder="Ex: 202301"/>
	<button type="submit">OK</button>
</form>

Income: {{$sum}}

@endsection
