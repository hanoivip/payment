@if (!empty($balances) && !$balances->isEmpty())
    <p>Tài khoản:</p>
    @foreach ($balances as $bal)
        <div>
        	<p>TK {{ __('hanoivip.payment::balance.types.' . $bal->balance_type) }} 
        		<strong id="recharge-balance">{{$bal->balance}}</strong>đ 
        	</p>
        </div>
    @endforeach
@else
	<p>Bạn vẫn chưa có xu nào!</p>
@endif