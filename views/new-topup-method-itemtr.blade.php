@extends('hanoivip::layouts.app')

@section('title', 'Nạp game với itemtr')

@section('content')

@if (!empty($guide))
	<p>{{$guide}}</p>
@endif

@if (!empty($data))
<script type="text/javascript">
setTimeout(function(){
	var iframe = document.getElementById('itemtr_payment_iframe');
	var iframeDocument = iframe.contentDocument.contentDocument.title;
	console.log(iframeDocument);
}, 1000);
</script>
<iframe src="{{$data['paymentUrl']}}" id="itemtr_payment_iframe" title="Pay with Itemtr"></iframe>
@else
@endif

@endsection
