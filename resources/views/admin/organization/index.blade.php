@extends('layouts.app')
@section('title', 'Profil Koperasi')
@section('page_title', 'Profil Koperasi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Profil Koperasi</h1>
    <p class="page-subtitle">Kelola identitas dan informasi resmi koperasi</p>
  </div>
</div>

<div class="card" style="max-width:700px;">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.organization.update') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="name">Nama Koperasi <span class="req">*</span></label>
          <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
            value="{{ old('name', $org->name) }}" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="legal_name">Nama Badan Hukum</label>
          <input type="text" id="legal_name" name="legal_name" class="form-control" value="{{ old('legal_name', $org->legal_name) }}">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="legal_number">No. Badan Hukum</label>
          <input type="text" id="legal_number" name="legal_number" class="form-control" value="{{ old('legal_number', $org->legal_number) }}">
        </div>
        <div class="form-group">
          <label class="form-label" for="phone">Telepon</label>
          <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $org->phone) }}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $org->email) }}">
      </div>
      <div class="form-group">
        <label class="form-label" for="address">Alamat</label>
        <textarea id="address" name="address" class="form-control">{{ old('address', $org->address) }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label" for="logo">Logo</label>
        @if($org->logo)
          <img src="{{ asset('storage/'.$org->logo) }}" alt="Logo" style="height:50px;margin-bottom:8px;display:block;border-radius:6px;">
        @endif
        <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
      </div>
      <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endsection
