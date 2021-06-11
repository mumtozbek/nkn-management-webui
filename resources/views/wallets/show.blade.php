@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Show the Wallet') }}
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('Node') }}</label>
                            <input type="text" name="node" id="node" value="{{ $wallet->node->host }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="name">{{ __('Address') }}</label>
                            <input type="text" name="address" id="address" value="{{ $wallet->address }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="keystore">{{ __('Keystore') }}</label>
                            <textarea name="keystore" id="keystore" class="form-control" rows="10" readonly>{{ $wallet->keystore }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input type="text" name="password" id="password" value="{{ $wallet->password }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('wallets.index') }}" class="btn btn-secondary">
                                {{ __('Back to list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
