@extends('hanoivip::layouts.app')

@section('title', 'Các phương pháp nạp game được hỗ trợ')

@section('content')

@if (count($methods) > 0)
<h1>{{__('hanoivip::payment.methods.title')}}</h1>
<form method="post" action="{{route('newtopup.choose')}}">
{{ csrf_field() }}
	<input type="hidden" id="order" name="order" value="{{$order}}"/>
	<input type="hidden" id="next" name="next" value="{{$next}}"/>
	@foreach ($methods as $code => $method)
		<input type="radio" id="method" name="method" value="{{$code}}"/>
		<p>{{$method['name']}}</p>
	@endforeach
	<button type="submit">{{__('hanoivip::payment.methods.next')}}</button>
</form>	
@else
	<p>{{__('hanoivip::payment.methods.empty')}}</p>
@endif

@endsection
