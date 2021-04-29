@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Edit the Wallet') }}
                    </div>

                    <div class="card-body">
                        <form action="{{ route('wallets.update', $wallet->id) }}" method="POST">
                            @csrf

                            @method('put')

                            <div class="form-group">
                                <label for="name">{{ __('Node') }}</label>
                                <select name="node_id" id="node_id" class="form-control">
                                    <option value="">{{ __('Select the Node') }}</option>
                                    @foreach($nodes as $node)
                                        <option value="{{ $node->id }}"{{ $node->is($wallet->node) ? ' selected' : '' }}>{{ $node->host }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="name">{{ __('Address') }}</label>
                                <input type="text" name="address" id="address" value="{{ old('address', $wallet->address) }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="keystore">{{ __('Keystore') }}</label>
                                <textarea name="keystore" id="keystore" class="form-control" rows="10">{{ old('keystore', $wallet->keystore) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <input type="text" name="password" id="password" value="{{ old('password', $wallet->password) }}" class="form-control">
                            </div>

                            <div class="form-group">
                                <a href="{{ route('wallets.index') }}" class="btn btn-secondary">
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
