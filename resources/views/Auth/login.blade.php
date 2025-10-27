<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
  <div class="overlay"></div>

  <div class="auth-container">
    <form method="POST" action="{{ route('login.post') }}" novalidate>
      @csrf
      <h2>Login ke Akun Anda</h2>

      @if (session('success'))
        <div class="alert success" role="status">{{ session('success') }}</div>
      @endif

      @if (session('error'))
        <div class="alert danger" role="alert">{{ session('error') }}</div>
      @endif

      @error('email')
        <div class="alert danger" role="alert">{{ $message }}</div>
      @enderror

      <div class="input-group">
        <img src="{{ asset('images/contact-icon.png') }}" class="input-icon" alt="">
        <input type="email" name="email" value="{{ old('email') }}"
               placeholder="Masukkan email" required autocomplete="email">
      </div>

      <div class="input-group">
        <img src="{{ asset('images/password-icon.png') }}" class="input-icon" alt="">
        <input type="password" name="password" placeholder="Masukkan password"
               required autocomplete="current-password" id="loginPass">
        <button type="button" class="eye" aria-label="Tampil/Sembunyi password" data-target="#loginPass">👁</button>
      </div>

      <label class="check-row">
        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <span>Ingat saya</span>
      </label>

      <button type="submit" class="primary-btn">Login</button>

      <div class="separator">atau</div>

      <div class="google-wrapper">
        <button type="button" class="google-btn" id="googleLoginBtn" aria-label="Login dengan Google">
          <img src="{{ asset('images/google-icon.png') }}" alt="">
          <span>Login dengan Google</span>
        </button>
        <noscript>
          <div style="margin-top:.75rem;">
            <a href="{{ route('login.google') }}" class="google-btn" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
              <img src="{{ asset('images/google-icon.png') }}" alt="" style="width:22px;height:22px;">
              <span>Login dengan Google</span>
            </a>
          </div>
        </noscript>
      </div>

      <div class="register-link">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
      </div>
    </form>
  </div>

  <script>
    // toggle show/hide password
    document.querySelectorAll('.eye').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.querySelector(btn.dataset.target);
        if (!input) return;
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        btn.classList.toggle('on', type === 'text');
      });
    });

    // redirect ke Google
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('googleLoginBtn');
      if (!btn) return;
      btn.addEventListener('click', () => {
        btn.classList.add('is-loading');
        btn.disabled = true;
        const span = btn.querySelector('span');
        if (span) span.textContent = 'Mengalihkan ke Google';
        setTimeout(() => { window.location.href = "{{ route('login.google') }}"; }, 350);
      });
    });
  </script>
</body>
</html>
