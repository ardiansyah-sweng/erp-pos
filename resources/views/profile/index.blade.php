@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: #0d1117;
        color: #e6edf3;
        min-height: 100vh;
    }

    .header {
        background: #161b22;
        border-bottom: 1px solid #21262d;
        padding: 24px 32px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
    }
    .header-label {
        color: #2dd4bf;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .header-title { font-size: 28px; font-weight: 700; color: #f0f6fc; }
    .header-sub { font-size: 13px; color: #8b949e; margin-top: 4px; }
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        background: #21262d; border: 1px solid #30363d;
        color: #e6edf3; padding: 8px 16px; border-radius: 8px;
        text-decoration: none; font-size: 13px; transition: background 0.2s;
    }
    .btn-back:hover { background: #30363d; }

    .main { padding: 28px 32px; max-width: 800px; margin: 0 auto; }

    .alert {
        padding: 12px 16px; border-radius: 8px; font-size: 13px;
        margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
    }
    .alert-success { background: #22c55e22; border: 1px solid #22c55e44; color: #22c55e; }
    .alert-error { background: #ef444422; border: 1px solid #ef444444; color: #ef4444; }

    .avatar-card {
        background: #161b22; border: 1px solid #21262d; border-radius: 12px;
        padding: 24px; display: flex; align-items: center; gap: 20px; margin-bottom: 20px;
    }
    .avatar {
        width: 64px; height: 64px; border-radius: 50%;
        background: linear-gradient(135deg, #2dd4bf, #3b82f6);
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; font-weight: 700; color: #0d1117; flex-shrink: 0;
    }
    .avatar-info h2 { font-size: 18px; font-weight: 700; color: #f0f6fc; }
    .avatar-info p { font-size: 13px; color: #8b949e; margin-top: 4px; }
    .badge-kasir {
        display: inline-block; background: #2dd4bf22; color: #2dd4bf;
        border: 1px solid #2dd4bf44; border-radius: 20px; font-size: 11px;
        font-weight: 700; padding: 2px 10px; margin-top: 6px;
        letter-spacing: 1px; text-transform: uppercase;
    }

    .panel {
        background: #161b22; border: 1px solid #21262d;
        border-radius: 12px; overflow: hidden; margin-bottom: 20px;
    }
    .panel-header { padding: 16px 20px; border-bottom: 1px solid #21262d; }
    .panel-title { font-size: 14px; font-weight: 700; color: #f0f6fc; }
    .panel-sub { font-size: 12px; color: #8b949e; margin-top: 2px; }
    .panel-body { padding: 20px; }

    .form-group { margin-bottom: 16px; }
    .form-label {
        display: block; font-size: 12px; font-weight: 600;
        color: #8b949e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;
    }
    .form-input {
        width: 100%; background: #0d1117; border: 1px solid #30363d;
        color: #e6edf3; border-radius: 8px; padding: 10px 14px;
        font-size: 14px; transition: border-color 0.2s;
    }
    .form-input:focus { outline: none; border-color: #2dd4bf; box-shadow: 0 0 0 3px #2dd4bf15; }
    .form-input.is-error { border-color: #ef4444; }
    .form-error { font-size: 12px; color: #ef4444; margin-top: 4px; }
    .form-hint { font-size: 12px; color: #8b949e; margin-top: 4px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

    .btn-primary {
        background: #2dd4bf; color: #0d1117; border: none; border-radius: 8px;
        padding: 10px 20px; font-size: 14px; font-weight: 700; cursor: pointer; transition: opacity 0.2s;
    }
    .btn-primary:hover { opacity: 0.85; }

    .btn-danger {
        background: #ef444422; color: #ef4444; border: 1px solid #ef444444;
        border-radius: 8px; padding: 10px 20px; font-size: 14px;
        font-weight: 700; cursor: pointer; transition: all 0.2s;
    }
    .btn-danger:hover { background: #ef444433; }

    .divider { border: none; border-top: 1px solid #21262d; margin: 16px 0; }
</style>
@endpush

@section('content')

<div class="header">
    <div>
        <div class="header-label">Akun</div>
        <div class="header-title">Profil Saya</div>
        <div class="header-sub">Kelola informasi dan keamanan akun kasir</div>
    </div>
    <a href="{{ url('/pos') }}" class="btn-back">← Kembali ke POS</a>
</div>

<div class="main">

    @if(session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    @if($errors->any() && !$errors->has('current_password'))
        <div class="alert alert-error">✕ {{ $errors->first() }}</div>
    @endif

    <div class="avatar-card">
        <div class="avatar">{{ strtoupper(substr($cashier->name, 0, 1)) }}</div>
        <div class="avatar-info">
            <h2>{{ $cashier->name }}</h2>
            <p>{{ $cashier->username }}</p>
            <span class="badge-kasir">Kasir</span>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Informasi Profil</div>
            <div class="panel-sub">Perbarui nama dan username akun kamu</div>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-input {{ $errors->has('name') ? 'is-error' : '' }}" value="{{ old('name', $cashier->name) }}" placeholder="Nama lengkap">
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input {{ $errors->has('username') ? 'is-error' : '' }}" value="{{ old('username', $cashier->username) }}" placeholder="Username login">
                        @error('username')<div class="form-error">{{ $message }}</div>@enderror
                        <div class="form-hint">Digunakan untuk login ke sistem</div>
                    </div>
                </div>

                <hr class="divider">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Ganti Password</div>
            <div class="panel-sub">Pastikan menggunakan password yang kuat dan mudah diingat</div>
        </div>
        <div class="panel-body">

            @if($errors->has('current_password'))
                <div class="alert alert-error" style="margin-bottom:16px;">✕ {{ $errors->first('current_password') }}</div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-input {{ $errors->has('current_password') ? 'is-error' : '' }}" placeholder="Masukkan password lama">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-input {{ $errors->has('new_password') ? 'is-error' : '' }}" placeholder="Minimal 6 karakter">
                        @error('new_password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-input" placeholder="Ulangi password baru">
                    </div>
                </div>

                <hr class="divider">
                <button type="submit" class="btn-danger">Perbarui Password</button>
            </form>
        </div>
    </div>

</div>

@endsection
