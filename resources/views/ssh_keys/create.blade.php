@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Create a SSH Key') }}
                    </div>

                    <div class="card-body">
                        <form action="{{ route('ssh-keys.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="private_key">{{ __('Private Key') }}</label>
                                <textarea name="private_key" id="private_key" class="form-control" rows="15">{{ old('private_key') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <input type="text" name="password" id="password" value="{{ old('password') }}" class="form-control">
                            </div>

                            <div class="form-group">
                                <a href="{{ route('ssh-keys.index') }}" class="btn btn-secondary">
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
