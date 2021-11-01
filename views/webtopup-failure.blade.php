@extends('hanoivip::layouts.app')

@section('title', 'Nạp web coin không được')

@section('content')

<p>{{ $message }}</p>        
<a href="{{ route('webtopup') }}"><button>Chuyển lại</button></a>

@endsection
