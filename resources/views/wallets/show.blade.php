@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Edit the Account') }}
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="host">{{ __('Host') }}</label>
                            <input type="text" name="host" id="host" value="{{ $account->host }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="host">{{ __('Provider') }}</label>
                            <input type="text" name="provider" id="provider" value="{{ $account->provider->name }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                                {{ __('Back to list') }}
                            </a>
                        </div>

                        @if(count($errors))
                            <ul class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
