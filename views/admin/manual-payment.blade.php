@extends('hanoivip::admin.layouts.admin')

@section('title', 'User get paid by admin')

@section('content')

<form method="post" action="">
{{ csrf_field() }}
<input type="hidden" id="tid" name="tid" value="{{$tid}}"/>

<select id="amount" name="amount">
<option value="">Choose player paid amount</option>
@foreach ($options as $title => $amount)
	<option value="{{$amount}}">{{$title}}</option>
@endforeach
</select>
<button type="submit">OK</button>
@endsection
