@extends('adminlte::page')

@section('title', 'Manajemen Data Santri')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-graduate mr-2"></i>Direktori Data Santri</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-user-plus mr-1"></i> Tambah Santri Baru
        </button>
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
            <i class="icon fas fa-exclamation-triangle mr-2"></i>Gagal menyimpan data. Pastikan format NISN/NIK unik dan file foto sesuai ketentuan.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    @endif

    {{-- Filter & Pencarian --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter Pencarian</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.students.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama / NISN / NIK..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text" name="grade" class="form-control" placeholder="Contoh: Kelas 7A" value="{{ request('grade') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="gender" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="Laki-laki" {{ request('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ request('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Urutkan Berdasarkan</label>
                            <select name="sort_by" class="form-control">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama Abjad</option>
                                <option value="admission_year" {{ request('sort_by') == 'admission_year' ? 'selected' : '' }}>Tahun Masuk</option>
                                <option value="grade" {{ request('sort_by') == 'grade' ? 'selected' : '' }}>Kelas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-info flex-fill mr-2"><i class="fas fa-search mr-1"></i> Cari</button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-default flex-fill"><i class="fas fa-undo mr-1"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data Santri --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th width="25%">Profil Santri</th>
                            <th width="20%">Identitas (NISN/NIK)</th>
                            <th width="15%">Kelas & Asrama</th>
                            <th width="20%">Wali Santri</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->photo_url ? asset('storage/' . $student->photo_url) : asset('images/default-avatar.png') }}" 
                                         class="img-circle elevation-1 mr-3" 
                                         style="width: 45px; height: 45px; object-fit: cover;" 
                                         alt="User Image">
                                    <div>
                                        <strong class="d-block">{{ $student->name }}</strong>
                                        <span class="text-muted small"><i class="fas fa-venus-mars"></i> {{ $student->gender }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="font-weight-bold">{{ $student->nisn }}</span><br>
                                <span class="text-muted small">NIK: {{ $student->nik ?? '-' }}</span>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-primary px-2 py-1">{{ $student->grade }}</span><br>
                                <span class="text-muted small"><i class="fas fa-building"></i> {{ $student->dormitory ?? ($student->gender == 'L' ? 'Asrama Putra' : 'Asrama Putri') }}</span>
                            </td>
                            <td class="align-middle">
                                @if($student->parent)
                                    <strong>{{ $student->parent->name }}</strong><br>
                                    <span class="text-muted small"><i class="fab fa-whatsapp text-success"></i> {{ $student->parent->phone_number }}</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Belum Terhubung</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    {{-- Tombol Lihat Detail (Akan mengarah ke view detail yang kita buat sebelumnya) --}}
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-info" title="Lihat Rekam Jejak">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    
                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit{{ $student->id }}" title="Edit Biodata">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Menghapus santri akan ikut menghapus seluruh nilai, absensi, dan tagihannya. Lanjutkan?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit Santri --}}
                        <div class="modal fade text-left" id="modalEdit{{ $student->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                {{-- WAJIB: enctype="multipart/form-data" agar bisa upload file --}}
                                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title font-weight-bold">Edit Biodata: {{ $student->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <select name="gender" class="form-control" required>
                                                        <option value="Laki-laki" {{ $student->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                        <option value="Perempuan" {{ $student->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>NISN <span class="text-danger">*</span></label>
                                                    <input type="text" name="nisn" class="form-control" value="{{ $student->nisn }}" required>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>NIK (Opsional)</label>
                                                    <input type="text" name="nik" class="form-control" value="{{ $student->nik }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>Kelas <span class="text-danger">*</span></label>
                                                    <input type="text" name="grade" class="form-control" value="{{ $student->grade }}" required>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Tahun Masuk <span class="text-danger">*</span></label>
                                                    <input type="number" name="admission_year" class="form-control" value="{{ $student->admission_year }}" required>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Asrama / Kamar</label>
                                                    <input type="text" name="dormitory" class="form-control" value="{{ $student->dormitory }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Ganti Foto Profil (Opsional)</label>
                                                <input type="file" name="photo" class="form-control-file" accept="image/png, image/jpeg, image/jpg">
                                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto. Maksimal 2MB.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data santri yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Santri Baru --}}
    <div class="modal fade text-left" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white font-weight-bold">Registrasi Santri Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="Sesuai Akta Kelahiran">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>NISN <span class="text-danger">*</span></label>
                                <input type="text" name="nisn" class="form-control" required placeholder="Nomor Induk Siswa Nasional">
                                <small class="text-danger">Wali santri akan menggunakan NISN ini untuk menyambungkan akun.</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>NIK (Opsional)</label>
                                <input type="text" name="nik" class="form-control" placeholder="16 Digit NIK">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Kelas <span class="text-danger">*</span></label>
                                <input type="text" name="grade" class="form-control" required placeholder="Contoh: Kelas 7A">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Tahun Masuk <span class="text-danger">*</span></label>
                                <input type="number" name="admission_year" class="form-control" required value="{{ date('Y') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Asrama / Kamar</label>
                                <input type="text" name="dormitory" class="form-control" placeholder="Opsional">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Foto Profil (Opsional)</label>
                            <input type="file" name="photo" class="form-control-file" accept="image/png, image/jpeg, image/jpg">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop