@extends('hanoivip::admin.layouts.admin')

@section('title', 'Find user by order')

@section('content')

<form method="POST" action="{{ route('ecmin.webtopup.finduser') }}">
{{ csrf_field() }}
Order: <input id="order" name="order" value="" />
<button type="submit">Find</button>
</form>

@endsection
