<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
  <div class="overlay"></div>
  <div class="auth-container">
    <form method="POST" action="{{ route('register.post') }}">
      @csrf
      <h2>Buat Akun Baru</h2>

      @if ($errors->any())
        <div class="error-message">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="input-group">
        <img src="{{ asset('images/user-icon.png') }}" class="input-icon" alt="user">
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required>
      </div>

      <div class="input-group">
        <img src="{{ asset('images/contact-icon.png') }}" class="input-icon" alt="email">
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
      </div>

      <div class="input-group">
        <img src="{{ asset('images/password-icon.png') }}" class="input-icon" alt="lock">
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <div class="input-group">
        <img src="{{ asset('images/password-icon.png') }}" class="input-icon" alt="lock">
        <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
      </div>

      <button type="submit" class="login-btn">Daftar</button>

      <div class="register-link">
        Sudah punya akun? <a href="{{ route('login') }}">Login sekarang</a>
      </div>
    </form>
  </div>
</body>
</html>
