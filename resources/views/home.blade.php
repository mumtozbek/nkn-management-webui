@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="chart-container" style="position: relative; height:500px; width:100%">
                        <canvas id="myChart"></canvas>
                    </div>

                    <script>
                        var ctx = document.getElementById('myChart');
                        var myChart = new Chart(ctx, {!! json_encode($chartData) !!});
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
