@extends('layouts.app')
@section('title','Profil')

@push('styles')
  @vite('resources/css/profile.css')
@endpush

@section('content')

{{-- === HEADER PROFIL (identitas user saja) === --}}
<section class="pf-header">
  <div class="pf-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
  <div class="pf-id">
    <h2 class="pf-name">{{ $user->name }}</h2>
    <p class="pf-mail">{{ $user->email }}</p>
  </div>
</section>

{{-- === CARD DATA AKUN === --}}
<section class="pf-wrap">
  <div class="pf-card">

    @if (session('success'))
      <div class="pf-alert success"><span class="pf-alert-dot"></span>{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="pf-alert danger">
        <span class="pf-alert-dot"></span>
        <strong>Periksa kembali:</strong>
        <ul>@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="pf-form" autocomplete="off" novalidate>
      @csrf @method('PUT')

      <div class="pf-col">
        <h4 class="pf-section-title">Data Akun</h4>
        <div class="pf-field">
          <label for="name">Nama <span class="req">*</span></label>
          <div class="pf-input">
            <img class="pf-icon" src="{{ asset('images/user-icon.png') }}" alt="">
            <input id="name" name="name" type="text" value="{{ old('name',$user->name) }}" required>
          </div>
        </div>
        <div class="pf-field">
          <label for="email">Email <span class="req">*</span></label>
          <div class="pf-input">
            <img class="pf-icon" src="{{ asset('images/contact-icon.png') }}" alt="">
            <input id="email" name="email" type="email" value="{{ old('email',$user->email) }}" required>
          </div>
        </div>
      </div>

      <div class="pf-col">
        <h4 class="pf-section-title">
          Ganti Password <span class="pf-badge">opsional</span>
        </h4>
        <div class="pf-field">
          <label for="current_password">Password Saat Ini</label>
          <div class="pf-input">
            <img class="pf-icon" src="{{ asset('images/password-icon.png') }}" alt="">
            <input id="current_password" name="current_password" type="password">
          </div>
        </div>
        <div class="pf-grid-2">
          <div class="pf-field">
            <label for="password">Password Baru</label>
            <div class="pf-input">
              <img class="pf-icon" src="{{ asset('images/password-icon.png') }}" alt="">
              <input id="password" name="password" type="password">
            </div>
          </div>
          <div class="pf-field">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <div class="pf-input">
              <img class="pf-icon" src="{{ asset('images/password-icon.png') }}" alt="">
              <input id="password_confirmation" name="password_confirmation" type="password">
            </div>
          </div>
        </div>
      </div>

      <div class="pf-actions">
        <button class="pf-btn" type="submit">Update Profile</button>
      </div>
    </form>

  </div>
</section>
@endsection
