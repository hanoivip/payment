@extends('hanoivip::layouts.app')

@section('title', 'Các phương pháp nạp được hỗ trợ')

@section('content')

<p>{{ $message }}</p>        
<a href="{{ route('newrecharge') }}"><button>Chuyển lại</button></a>


@endsection
