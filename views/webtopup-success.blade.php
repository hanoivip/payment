@extends('hanoivip::layouts.app')

@section('title', 'Nạp web coin thành công')

@section('content')

<p>{{__('hanoivip::webtopup.success')}}</p>
<a href="{{ route('webtopup') }}"><button>Chuyển nữa</button></a>

@endsection
