@extends('hanoivip::layouts.app')

@section('title', 'Nạp web coin không được')

@section('content')

<p>{{ $message }}</p>        
<a href="{{ route('webtopup') }}"><button>Nạp lại</button></a>

@endsection
