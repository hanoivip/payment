@extends('hanoivip::layouts.app')

@section('title', 'Nạp web coin - trễ')

@section('content')

<p>{{__('hanoivip::webtopup.pending')}}</p>

<a href="{{ route('newrecharge.refresh', ['trans' => $trans]) }}"><button>Cập nhật</button></a>

<a href="{{ route('webtopup') }}"><button>Chuyển nữa</button></a>

@endsection
