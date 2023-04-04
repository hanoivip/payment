@extends('hanoivip::admin.layouts.admin')

@section('title', 'Thống kê doanh số (2)')

@section('content')

<a href="{{route('ecmin.stats.today')}}">Today income</a><br/>

<a href="{{route('ecmin.stats.week')}}">This week income</a><br/>

<a href="{{route('ecmin.stats.month')}}">This month income</a><br/>

<a href="{{route('ecmin.stats.bymonth')}}">Income by month</a><br/>

@endsection
