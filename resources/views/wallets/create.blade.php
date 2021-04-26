@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Create a Wallet') }}
                    </div>

                    <div class="card-body">
                        <form action="{{ route('wallets.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="node_id">{{ __('Node') }}</label>
                                <select name="node_id" id="node_id" class="form-control" required>
                                    <option value="">{{ __('Select the Node') }}</option>
                                    @foreach($nodes as $node)
                                        <option value="{{ $node->id }}"{{ $node->id == old('node_id') ? ' selected' : '' }}>{{ $node->host }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="name">{{ __('Address') }}</label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="keystore">{{ __('Keystore') }}</label>
                                <textarea name="keystore" id="keystore" class="form-control" rows="10">{{ old('keystore') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <input type="text" name="password" id="password" value="{{ old('password') }}" class="form-control">
                            </div>

                            <div class="form-group">
                                <a href="{{ route('wallets.index') }}" class="btn btn-secondary">
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
