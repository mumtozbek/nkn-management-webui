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
                        <form action="{{ route('accounts.update', $account->id) }}" method="POST">
                            @csrf

                            @method('put')

                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $account->name) }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="name">{{ __('Provider') }}</label>
                                <select name="provider_id" id="provider_id" class="form-control" required>
                                    <option value="">{{ __('Select the Provider') }}</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}"{{ $provider->is($account->provider) ? ' selected' : '' }}>{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                                    {{ __('Back to list') }}
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
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
