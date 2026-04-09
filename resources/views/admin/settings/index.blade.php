@extends('adminlte::page')

@section('title', 'Pengaturan Sistem')

@section('content_header')
    <h1><i class="fas fa-cogs mr-2"></i>Pengaturan Sistem</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-wallet mr-1"></i> Pengaturan Pembayaran & Tagihan</h3>
            </div>
            
            {{-- Form Update Pengaturan --}}
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    
                    {{-- Notifikasi Sukses --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    {{-- Notifikasi Error Validasi --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-exclamation-triangle"></i> Gagal menyimpan. Silakan cek kembali isian Anda.
                        </div>
                    @endif

                    {{-- Input Nomor WA Bendahara --}}
                    <div class="form-group">
                        <label for="wa_payment_number">
                            <i class="fab fa-whatsapp text-success mr-1"></i> Nomor WhatsApp Bendahara
                        </label>
                        <input type="text" 
                               name="wa_payment_number" 
                               class="form-control @error('wa_payment_number') is-invalid @enderror" 
                               id="wa_payment_number" 
                               value="{{ old('wa_payment_number', $settings['wa_payment_number'] ?? '') }}" 
                               placeholder="Contoh: 6281234567890" 
                               required>
                        
                        <small class="text-muted d-block mt-1">
                            Nomor ini akan digunakan sebagai tujuan saat orang tua menekan tombol "Checkout" di aplikasi mobile. <br>
                            <span class="text-danger">*</span> <b>Penting:</b> Gunakan kode negara <b>62</b> di awal nomor (tanpa tanda + atau angka 0).
                        </small>
                        
                        @error('wa_payment_number')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
                
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Kamu bisa menambahkan card lain di sebelah kanan (col-md-6) jika ke depannya ada pengaturan lain --}}
    {{-- 
    <div class="col-md-6">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-school mr-1"></i> Pengaturan Profil Sekolah</h3>
            </div>
            <div class="card-body">
                (Contoh tempat untuk pengaturan Nama Sekolah, Logo, dll nantinya)
            </div>
        </div>
    </div> 
    --}}
</div>
@stop