@extends('hanoivip::layouts.app')

@section('title', 'Các phương pháp nạp được hỗ trợ')

@section('content')

<p>Payment detail: {{$data->getDetail()}}</p>

@if ($data->isPending())
<form method="post" action="{{route('webtopup.query')}}">
    {{ csrf_field() }}
    <input type="hidden" id="trans" name="trans" value="{{$trans}}"/>
    	<button type="submit" class="btn btn-primary">Refresh</button>
</form>
@endif

@if ($data->isSuccess())
<p>Card amount: {{ $data->getAmount() }} {{ $data->getCurrency() }}</p><br/>
<a href="{{ route('recharge') }}" class="btn btn-primary">Buy game item</a><br/>
<a href="{{ route('webtopup') }}" class="btn btn-secondary">Pay more</a><br/>
@endif

@if ($data->isFailure())
<a href="{{ route('webtopup') }}" class="btn btn-primary"><button>Pay again</button></a>
@endif

@endsection
