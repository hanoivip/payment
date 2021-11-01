@extends('hanoivip::layouts.app')

@section('title', 'Nạp web coin thành công')

@section('content')

<p>{{__('hanoivip::webtopup.success')}}</p>

<a href="{{ route('recharge') }}"><button>Chuyển xu</button></a>

<a href="{{ route('webtopup') }}"><button>Nạp nữa</button></a>

@endsection
