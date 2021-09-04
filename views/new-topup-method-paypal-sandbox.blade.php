@extends('hanoivip::layouts.app')

@section('title', 'Nạp game với paypal')

@section('content')

@if (!empty($guide))
	<p>{{$guide}}</p>
@endif

@if (!empty($data))
	<iframe src="{{$data['checkoutUrl']}}" title="Paypal Checkout" width="80%" height="80%">></iframe>
@else
@endif

@endsection
