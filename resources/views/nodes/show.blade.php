@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('View Node') }}
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <a href="{{ route('nodes.index') }}" class="btn btn-secondary">
                                {{ __('Back to list') }}
                            </a>
                        </div>

                        <div class="form-group">
                            <label for="host">{{ __('Host') }}</label>
                            <input type="text" name="host" id="host" value="{{ $node->host }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="host">{{ __('Account') }}</label>
                            <input type="text" name="account" id="account" value="{{ $node->account->name . ' (' . $node->account->provider->name . ')' }}" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12 my-2">
                <div class="card">
                    <div class="card-header">
                        {{ __('Speed Stats') }}
                    </div>

                    <div class="card-body">
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
                    <div class="card-header">{{ __('Proposal Stats') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="chart-container" style="position: relative; height:500px; width:100%">
                            <canvas id="proposalChart"></canvas>
                        </div>

                        <script>
                            var ctx = document.getElementById('proposalChart');
                            var proposalChart = new Chart(ctx, {!! json_encode($proposalChartData) !!});
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
