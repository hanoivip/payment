@extends('hanoivip::admin.layouts.admin')

@section('title', 'Thống kê doanh số (2)')

@section('content')

<a href="{{route('ecmin.stats.today')}}">Doanh số trong ngày</a><br/>

<a href="{{route('ecmin.stats.month')}}">Doanh số trong tháng</a><br/>

<a href="{{route('ecmin.stats.bymonth')}}">Doanh số theo tháng</a><br/>

@endsection
