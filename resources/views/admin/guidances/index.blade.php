@extends('adminlte::page')

@section('title', 'Catatan Pembinaan Santri')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-clipboard-list mr-2"></i>Buku Pembinaan & Kedisiplinan</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Catatan Baru
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
            <i class="icon fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan saat menyimpan data.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    @endif

    {{-- Filter & Pencarian --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter Pencarian</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.guidances.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cari Nama / Kasus</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama santri atau judul kejadian..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis Catatan</label>
                            <select name="type" class="form-control">
                                <option value="">-- Semua Jenis --</option>
                                <option value="achievement" {{ request('type') == 'achievement' ? 'selected' : '' }}>Prestasi (Poin Plus)</option>
                                <option value="violation" {{ request('type') == 'violation' ? 'selected' : '' }}>Pelanggaran (Poin Minus)</option>
                                <option value="guidance" {{ request('type') == 'guidance' ? 'selected' : '' }}>Bimbingan/Konseling</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Urutkan Waktu</label>
                            <select name="sort_dir" class="form-control">
                                <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Terbaru (DESC)</option>
                                <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Terlama (ASC)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-info flex-fill mr-2"><i class="fas fa-search mr-1"></i> Cari</button>
                            <a href="{{ route('admin.guidances.index') }}" class="btn btn-default flex-fill"><i class="fas fa-undo mr-1"></i> Reset</a>
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
                            <th width="15%">Tanggal</th>
                            <th width="20%">Profil Santri</th>
                            <th width="15%" class="text-center">Jenis & Poin</th>
                            <th width="25%">Judul / Detail Kejadian</th>
                            <th width="15%">Penindak / Guru</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guidances as $row)
                        <tr>
                            <td class="align-middle">
                                <strong>{{ \Carbon\Carbon::parse($row->date)->translatedFormat('d M Y') }}</strong>
                            </td>
                            <td class="align-middle">
                                <strong>{{ $row->student->name }}</strong><br>
                                <span class="text-muted small">Kelas: {{ $row->student->grade }} | {{ $row->student->nisn }}</span>
                            </td>
                            <td class="text-center align-middle">
                                @if($row->type == 'achievement')
                                    <span class="badge badge-success px-2 py-1 d-block mb-1">PRESTASI</span>
                                    <h5 class="mb-0 text-success font-weight-bold">+{{ $row->points }}</h5>
                                @elseif($row->type == 'violation')
                                    <span class="badge badge-danger px-2 py-1 d-block mb-1">PELANGGARAN</span>
                                    <h5 class="mb-0 text-danger font-weight-bold">{{ $row->points < 0 ? $row->points : '-'.$row->points }}</h5>
                                @else
                                    <span class="badge badge-info px-2 py-1 d-block mb-1">BIMBINGAN</span>
                                    <h5 class="mb-0 text-info font-weight-bold">0</h5>
                                @endif
                            </td>
                            <td class="align-middle">
                                <strong>{{ $row->title }}</strong><br>
                                <span class="text-muted small">{{ \Illuminate\Support\Str::limit($row->description, 60, '...') }}</span>
                            </td>
                            <td class="align-middle">
                                {{ $row->handled_by ?? 'Sistem / Tidak Diketahui' }}
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit{{ $row->id }}" title="Edit Catatan">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.guidances.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus catatan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade text-left" id="modalEdit{{ $row->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('admin.guidances.update', $row->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title font-weight-bold">Edit Catatan: {{ $row->student->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Jenis Catatan <span class="text-danger">*</span></label>
                                                    <select name="type" class="form-control" required>
                                                        <option value="achievement" {{ $row->type == 'achievement' ? 'selected' : '' }}>Prestasi (Poin Plus)</option>
                                                        <option value="violation" {{ $row->type == 'violation' ? 'selected' : '' }}>Pelanggaran (Poin Minus)</option>
                                                        <option value="guidance" {{ $row->type == 'guidance' ? 'selected' : '' }}>Bimbingan/Konseling</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Tanggal Kejadian <span class="text-danger">*</span></label>
                                                    <input type="date" name="date" class="form-control" value="{{ $row->date }}" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 form-group">
                                                    <label>Judul Kasus / Prestasi <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" value="{{ $row->title }}" required>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Poin (Angka) <span class="text-danger">*</span></label>
                                                    <input type="number" name="points" class="form-control" value="{{ $row->points }}" required>
                                                    <small class="text-muted">Isi 0 jika hanya bimbingan.</small>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Penindak / Guru yang Menangani</label>
                                                <input type="text" name="handled_by" class="form-control" value="{{ $row->handled_by }}" placeholder="Contoh: Ust. Fulan">
                                            </div>
                                            <div class="form-group">
                                                <label>Detail / Deskripsi <span class="text-danger">*</span></label>
                                                <textarea name="description" class="form-control" rows="4" required>{{ $row->description }}</textarea>
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
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data catatan kedisiplinan atau prestasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $guidances->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Catatan Baru --}}
    <div class="modal fade text-left" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.guidances.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">Tambah Catatan Pembinaan</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Santri <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-control select2" style="width: 100%" required>
                                <option value="">-- Cari Nama atau NISN --</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nisn }}) - Kelas: {{ $s->grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Jenis Catatan <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="achievement">Prestasi (Poin Plus)</option>
                                    <option value="violation">Pelanggaran (Poin Minus)</option>
                                    <option value="guidance">Bimbingan/Konseling</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Tanggal Kejadian <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label>Judul Kasus / Prestasi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Terlambat Apel Pagi" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Poin Tambahan / Pengurangan <span class="text-danger">*</span></label>
                                <input type="number" name="points" class="form-control" value="0" required>
                                <small class="text-muted">Gunakan angka minus (misal: -5) untuk pelanggaran.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Penindak / Guru yang Menangani</label>
                            <input type="text" name="handled_by" class="form-control" placeholder="Contoh: Ust. Fulan">
                        </div>
                        <div class="form-group">
                            <label>Detail / Deskripsi Kejadian <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Tuliskan kronologi singkat atau rincian pembinaan di sini..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Catatan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- Memuat CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- PERBAIKAN: Menggunakan versi valid (1.5.2) bukan x.x.x --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--bootstrap4 .select2-selection--single { height: calc(2.25rem + 2px) !important; }
    </style>
@stop

@section('js')
    {{-- Memuat JS Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 dengan Tema Bootstrap 4 untuk dropdown santri
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "-- Cari Nama atau NISN --",
                allowClear: true,
                // PERBAIKAN: Beritahu Select2 agar menempel pada Modal, bukan pada body
                dropdownParent: $('#modalTambah') 
            });

            // Auto-focus ke kolom pencarian Select2 saat modal ditambah
            $('#modalTambah').on('shown.bs.modal', function () {
                $(this).find('.select2').select2('open');
            });
        });
    </script>
@stop