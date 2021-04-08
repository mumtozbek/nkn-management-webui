@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Create a Account') }}
                    </div>

                    <div class="card-body">
                        <form action="{{ route('accounts.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="provider_id">{{ __('Provider') }}</label>
                                <select name="provider_id" id="provider_id" class="form-control" required>
                                    <option value="">{{ __('Select the Provider') }}</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}"{{ $provider->id === old('provider_id') ? ' selected' : '' }}>{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                                    {{ __('Back to list') }}
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create') }}
                                </button>
                            </div>

                            @if(count($errors))
                                <ul class="alert alert-danger">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
