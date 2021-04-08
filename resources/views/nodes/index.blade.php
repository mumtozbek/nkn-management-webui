@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 class="mb-4">
                    {{ __('Monitoring Nodes') }}
                </h2>

                {{ $dataTable->table([], false, false) }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{$dataTable->scripts()}}
@endpush
