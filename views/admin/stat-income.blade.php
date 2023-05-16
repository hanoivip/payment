@extends('hanoivip::admin.layouts.admin')

@section('title', 'Thống kê doanh số (2)')

@push('scripts')
    
@endpush


@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div>
  <canvas id="myChart"></canvas>
</div>

<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [{!! '"'.implode('","', $data[0]).'"' !!}],
      datasets: [{
        label: 'Revenues',
        data: [{!! '"'.implode('","', $data[1]).'"' !!}],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

<a href="{{route('ecmin.stats.today')}}">Today income</a><br/>

<a href="{{route('ecmin.stats.week')}}">This week income</a><br/>

<a href="{{route('ecmin.stats.month')}}">This month income</a><br/>

<a href="{{route('ecmin.stats.bymonth')}}">Income by month</a><br/>

@endsection
