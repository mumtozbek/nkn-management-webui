@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('View the Account') }}
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $account->name) }}" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label for="name">{{ __('Provider') }}</label>
                            <select name="provider_id" id="provider_id" class="form-control" disabled>
                                <option value="">{{ __('Select the Provider') }}</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}"{{ $provider->is($account->provider) ? ' selected' : '' }}>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="username">{{ __('Username') }}</label>
                            <input type="text" name="username" id="username" value="{{ old('username', $account->username) }}" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label for="name">{{ __('SSH Key') }}</label>
                            <select name="ssh_key_id" id="ssh_key_id" class="form-control" disabled>
                                <option value="">{{ __('Select the SSH Key') }}</option>
                                @foreach($ssh_keys as $ssh_key)
                                    <option value="{{ $ssh_key->id }}"{{ $ssh_key->is($account->sshKey) ? ' selected' : '' }}>{{ $ssh_key->name }}</option>
                                @endforeach
                            </select>
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
