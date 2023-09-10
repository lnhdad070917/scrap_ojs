@extends('auth/app')

@section('Login', 'Halaman Login')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Registrasi') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-group">
                                <label for="nama">{{ __('Nama') }}</label>
                                <input id="nama" type="text" class="form-control" name="nama"
                                    value="{{ old('nama') }}" required autofocus>
                            </div>

                            <div class="form-group">
                                <label for="email">{{ __('Alamat Email') }}</label>
                                <input id="email" type="email" class="form-control" name="email"
                                    value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="username">{{ __('Username') }}</label>
                                <input id="username" type="text" class="form-control" name="username"
                                    value="{{ old('username') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ __('Registrasi') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
