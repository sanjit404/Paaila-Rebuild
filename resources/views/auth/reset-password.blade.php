@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<section style="background: var(--color-bg); min-height: calc(100vh - 70px); display: flex; align-items: center; justify-content: center; padding: var(--space-xl);">
    <div style="max-width: 480px; width: 100%;">
        <div class="card">
            <div class="card-body" style="padding: var(--space-xl);">

                <div style="text-align: center; margin-bottom: var(--space-xl);">
                    <div style="width: 72px; height: 72px; background: #E8F5E9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md);">
                        <i class="fas fa-lock" style="font-size: 28px; color: var(--color-primary);"></i>
                    </div>
                    <h1 style="font-size: 24px; font-weight: 700; margin-bottom: var(--space-sm);">Set new password</h1>
                    <p style="color: var(--color-text-light); margin: 0;">Choose a strong password for your account.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-error" style="margin-bottom: var(--space-lg);">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #B0BEC5; pointer-events: none;"></i>
                            <input
                                type="email"
                                name="email"
                                class="form-input"
                                style="padding-left: 42px;"
                                value="{{ old('email', $request->email) }}"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #B0BEC5; pointer-events: none;"></i>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-input"
                                style="padding-left: 42px; padding-right: 44px;"
                                placeholder="At least 8 characters"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" onclick="togglePwd('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #B0BEC5;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #B0BEC5; pointer-events: none;"></i>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-input"
                                style="padding-left: 42px; padding-right: 44px;"
                                placeholder="Repeat your password"
                                required
                                autocomplete="new-password"
                            >
                            <button type="button" onclick="togglePwd('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #B0BEC5;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-check-circle"></i>
                        Reset Password
                    </button>
                </form>

            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function togglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
@endpush
@endsection