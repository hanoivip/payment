@extends('hanoivip::admin.layouts.admin')

@section('title', 'Thống kê doanh số (2)')

@push('scripts')
    
@endpush


@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<a href="{{route('ecmin.stats.month')}}" class="badge badge-primary">Monthly stats</a><br/>

<a href="{{route('ecmin.stats.today')}}" class="badge badge-secondary">Today income</a><br/>

<a href="{{route('ecmin.stats.thisweek')}}" class="badge badge-secondary">This week income</a><br/>

<a href="{{route('ecmin.stats.thismonth')}}" class="badge badge-secondary">This month income</a><br/>

<a href="{{route('ecmin.stats.bymonth')}}" class="badge badge-secondary">Find by month</a><br/>


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


@endsection
