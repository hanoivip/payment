@extends('hanoivip::layouts.app')

@section('title', 'Nạp game với epinkasa')

@section('content')

@if (!empty($guide))
	<p>{{$guide}}</p>
@endif

@if (!empty($data))
	<iframe src="{{$data}}" title="Pay with Epinkasa"></iframe>
@else
@endif

@endsection
