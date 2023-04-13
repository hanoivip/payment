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

<div id="history-submits">
    <h1>Lịch sử nạp thẻ</h1>
    @if (!empty($submits))
    <table>
    <tr>
    	<th>Status</th>
    	<th>Card password</th>
    	<th>User choosen</th>
    	<th>User penalty</th>
    	<th>Real amount</th>
    	<th>Time</th>
    </tr>
    @foreach ($submits as $submit)
    <tr>
    	@switch($submit->status)
    		@case(0)
    			<td>Valid</td>
    			@break
    		@case(1)
    			<td>Invalid</td>
    			@break
    		@case(2)
    			<td>Delay</td>
    			@break
    		@case(3)
    			<td>Valid (pen)</td>
    			@break
    	@endswitch
        <td>{{$submit->password}}</td>
        <td>{{$submit->dvalue}}</td>
        <td>{{$submit->penalty}}</td>
        <td>{{$submit->value}}</td>
        <td>{{$submit->time}}</td>
    </tr>
    @endforeach
    </table>
    @for ($i=0; $i<$total_submits; ++$i)
    	<a class="webtopup-history-page" data-action="{{ route('api.history.topup') }}" data-page="{{$i}}">{{$i}}</a>
    @endfor
    
    @else
    	<p>Chưa nạp lần nào!</p>
    @endif
</div>

<div id="history-recharges">
    <h1>Lịch sử chuyển xu</h1>
    @if (!empty($submits))
    	<table>
    		<th>TK</th>
        	<th>Số xu</th>
        	<th>Lý do</th>
        	<th>Thời gian</th>
            @foreach ($mods as $mod)
            <tr>
                <td>{{$mod->acc_type == 1 ? "chính" : "phụ"}}</td>
                <td>{{$mod->balance}}</td>
                <td>{{$mod->reason}}</td>
                <td>{{$mod->time}}</td>
            </tr>
            @endforeach
        </table>
    @else
    	<p>Chưa chuyển lần nào!</p>
    @endif
    @for ($i=0; $i<$total_mods; ++$i)
    	<a class="webtopup-history-page" data-action="{{ route('api.history.recharge') }}" data-page="{{$i}}">{{$i}}</a>
    @endfor
</div>

@endsection
