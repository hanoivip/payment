@extends('hanoivip::layouts.app')

@section('title', 'Nạp game với điểm web')

@section('content')

@if (!empty($guide))
	<p>{{$guide}}</p>
@endif

@if (!empty($data))
<form method="post" action="{{route('newtopup.do')}}">
{{ csrf_field() }}
<input type="hidden" id="trans" name="trans" value="{{$trans}}"/>
	@foreach ($data as $info)
		<p>Loại tk: {{$info->type}}, số dư: {{$info->balance}}</p>
	@endforeach
	<button type="submit">{{__('hanoivip.payment::payment.methods.next')}}</button>
</form>
@else
	<p>{{__('hanoivip.payment::payment.credit.no-point')}}</p>
@endif

@endsection
