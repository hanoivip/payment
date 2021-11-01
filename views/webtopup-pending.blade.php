@extends('hanoivip::layouts.app')

@section('title', 'Nạp web coin - trễ')

@section('content')

<p>{{__('hanoivip::webtopup.pending')}}</p>

<a href="{{ route('webtopup.query', ['trans' => $trans]) }}"><button>Cập nhật</button></a>

<a href="{{ route('webtopup') }}"><button>Nạp nữa</button></a>

@endsection
