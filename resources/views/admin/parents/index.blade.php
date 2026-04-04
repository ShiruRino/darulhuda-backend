@extends('adminlte::page')

@section('title', 'Manajemen Orang Tua')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-friends mr-2"></i>Manajemen Akun Wali Santri</h1>
    </div>
@stop

@section('content')
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icon fas fa-check mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="icon fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan pada input form.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    @endif

    {{-- Filter & Pencarian --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Cari Akun</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.parents.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIK / No. HP / Email..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="{{ route('admin.parents.index') }}" class="btn btn-default">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th width="20%">Nama Wali</th>
                            <th width="20%">Identitas (NIK & Hubungan)</th>
                            <th width="20%">Kontak & Login</th>
                            <th width="10%" class="text-center">Akses Aplikasi</th>
                            <th width="30%" class="text-center">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $p)
                        <tr>
                            <td class="align-middle">
                                <strong>{{ $p->name }}</strong>
                            </td>
                            <td class="align-middle">
                                <span class="font-weight-bold">{{ $p->nik ?? '-' }}</span><br>
                                <span class="text-muted small">Status: {{ $p->relationship }}</span>
                            </td>
                            <td class="align-middle">
                                <span><i class="fab fa-whatsapp text-success"></i> {{ $p->phone_number }}</span><br>
                                <span class="text-muted small"><i class="far fa-envelope"></i> {{ $p->email ?? 'Email tidak diisi' }}</span>
                            </td>
                            <td class="text-center align-middle">
                                @if($p->is_active)
                                    <span class="badge badge-success px-3 py-2"><i class="fas fa-user-check"></i> Aktif</span>
                                @else
                                    <span class="badge badge-danger px-3 py-2"><i class="fas fa-user-lock"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                {{-- Tombol Edit Profil --}}
                                <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalEdit{{ $p->id }}" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                {{-- Tombol Ganti Password --}}
                                <button class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#modalPass{{ $p->id }}" title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>

                                {{-- Toggle Status Aktif/Nonaktif --}}
                                <form action="{{ route('admin.parents.status', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun ini?');">
                                    @csrf @method('PATCH')
                                    @if($p->is_active)
                                        <button class="btn btn-sm btn-secondary" title="Nonaktifkan Akses">
                                            <i class="fas fa-ban"></i> Matikan
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-success" title="Aktifkan Akses">
                                            <i class="fas fa-check-circle"></i> Aktifkan
                                        </button>
                                    @endif
                                </form>

                                {{-- Hapus Permanen --}}
                                <form action="{{ route('admin.parents.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Peringatan: Menghapus akun ini akan memutuskan akses orang tua ke data santri. Lanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit Data Wali --}}
                        <div class="modal fade text-left" id="modalEdit{{ $p->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.parents.update', $p->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title font-weight-bold">Edit Data Wali: {{ $p->name }}</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Lengkap Wali</label>
                                                <input type="text" name="name" class="form-control" value="{{ $p->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Hubungan dengan Santri</label>
                                                <select name="relationship" class="form-control" required>
                                                    <option value="Ayah" {{ $p->relationship == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                                    <option value="Ibu" {{ $p->relationship == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                                    <option value="Wali" {{ $p->relationship == 'Wali' ? 'selected' : '' }}>Wali Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Nomor Telepon / WhatsApp</label>
                                                <input type="text" name="phone_number" class="form-control" value="{{ $p->phone_number }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat Email (Opsional)</label>
                                                <input type="email" name="email" class="form-control" value="{{ $p->email }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Modal Reset Password --}}
                        <div class="modal fade text-left" id="modalPass{{ $p->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.parents.password', $p->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title font-weight-bold">Ganti Password: {{ $p->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-light text-sm">
                                                <i class="fas fa-info-circle text-info"></i> Gunakan fitur ini jika orang tua lupa password dan tidak bisa login ke aplikasi.
                                            </div>
                                            <div class="form-group">
                                                <label>Password Baru <span class="text-danger">*</span></label>
                                                <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                                            </div>
                                            <div class="form-group">
                                                <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru" required minlength="8">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning"><i class="fas fa-key mr-1"></i> Update Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Data akun orang tua tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $parents->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@stop