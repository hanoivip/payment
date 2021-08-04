@extends('hanoivip::layouts.app')

@section('title', 'Các phương pháp nạp được hỗ trợ')

@section('content')

<p>Kết quả thực hiện: {{$data->getDetail()}}</p>

@if ($data->isPending())
<form method="post" action="">
{{ csrf_field() }}
<input type="hidden" id="trans" name="trans" value="{{$trans->getTransId()}}"/>
	<button type="submit">Refresh</button>
</form>
@endif
@endsection
