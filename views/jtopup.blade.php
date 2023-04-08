@extends('hanoivip::layouts.app')

@section('title', 'Payment history')

@section('content')

<script type="text/javascript">
	var topup_langs = {!! $lang !!};
 	console.log(topup_langs);
</script>
<div id="my-payment"></div>

@endsection