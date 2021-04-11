@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Speed Stats') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="chart-container" style="position: relative; height:500px; width:100%">
                        <canvas id="speedChart"></canvas>
                    </div>

                    <script>
                        var ctx = document.getElementById('speedChart');
                        var speedChart = new Chart(ctx, {!! json_encode($speedChartData) !!});
                    </script>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12 mt-2">
            <div class="card">
                <div class="card-header">{{ __('Block Stats') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="chart-container" style="position: relative; height:500px; width:100%">
                        <canvas id="blockChart"></canvas>
                    </div>

                    <script>
                        var ctx = document.getElementById('blockChart');
                        var blockChart = new Chart(ctx, {!! json_encode($blockChartData) !!});
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
