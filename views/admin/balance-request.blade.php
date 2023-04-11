@extends('hanoivip::admin.layouts.admin')

@section('title', 'Request to add balance')

@section('content')

<form method="POST" action="{{ route('ecmin.balance.request') }}">
    {{ csrf_field() }}
	<input id="tid" name="tid" type="hidden" value="{{$tid}}">
	Reason <input id="reason" name="reason" type="text" value=""><br/>
	Amount <input id="amount" name="amount" type="text" value=""><br/>
	<button type="submit" class="btn btn-primary">OK</button>
</form>

@endsection
