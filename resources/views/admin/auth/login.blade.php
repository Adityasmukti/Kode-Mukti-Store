@extends('layouts.app')

@section('title', 'Admin Login - Kode Mukti')

@section('content')
<div class="container">
    <div class="admin-login-page">
        <div class="card admin-login-card">
            <h1 class="heading-text text-center mb-lg">Admin Login</h1>

            <form action="{{ route('admin.login.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-input @error('email') error @enderror"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email">
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input @error('password') error @enderror"
                           required
                           autocomplete="current-password">
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-full">Login</button>
            </form>
        </div>
    </div>
</div>
@endsection
