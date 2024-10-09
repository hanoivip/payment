@extends('hanoivip::layouts.app')

@section('title', 'Lịch sử nạp thẻ webtopup')

@push('scripts')
    <script src="/js/history.js"></script>
@endpush

@section('content')

<style>
table, th, td {
    border: 1px solid black;
}
.status0 {
    background-color: green;
}
.status1 {
    background-color: red;
}
.status2 {
    background-color: yellow;
}
.status3 {
    background-color: cyan;
}
</style>

<div id="history-recharges">
	@include('hanoivip::webtopup-history-mods', ['mods' => $mods])
</div>
@for ($i=0; $i<$total_mods; ++$i)
	<a class="webtopup-history-page" data-action="{{ route('api.history.recharge') }}" data-page="{{$i}}" data-update-id="history-recharges">{{$i}}</a>
@endfor


@endsection
