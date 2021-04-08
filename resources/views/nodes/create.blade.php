@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Create a Node') }}
                    </div>

                    <div class="card-body">
                        <form action="{{ route('nodes.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="host">{{ __('Host') }}</label>
                                <input type="text" name="host" id="title" value="{{ old('host') }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <a href="{{ route('nodes.index') }}" class="btn btn-secondary">
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
