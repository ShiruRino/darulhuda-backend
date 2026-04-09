@extends('adminlte::page')

@section('title', 'Manajemen Nilai Santri')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1><i class="fas fa-graduation-cap mr-2"></i>Manajemen Nilai Akademik</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus-circle"></i> Input Nilai Baru
        </button>
    </div>
@stop

@section('content')
    {{-- Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icon fas fa-check mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filter & Pencarian</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.grades.index') }}" method="GET">
                        <div class="row">
                            {{-- Search Nama/NISN --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cari Santri/Mapel</label>
                                    <input type="text" name="search" class="form-control" placeholder="Nama, NISN, atau Mapel..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            {{-- Filter Semester --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester" class="form-control">
                                        <option value="">-- Semua --</option>
                                        <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                        <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Filter Tipe --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tipe Nilai</label>
                                    <select name="type" class="form-control">
                                        <option value="">-- Semua --</option>
                                        <option value="Tugas" {{ request('type') == 'Tugas' ? 'selected' : '' }}>Tugas</option>
                                        <option value="UTS" {{ request('type') == 'UTS' ? 'selected' : '' }}>UTS</option>
                                        <option value="UAS" {{ request('type') == 'UAS' ? 'selected' : '' }}>UAS</option>
                                        <option value="Rapor" {{ request('type') == 'Rapor' ? 'selected' : '' }}>Rapor</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Sortir Tahun Ajaran --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Urutan Tahun</label>
                                    <select name="sort_dir" class="form-control">
                                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Terbaru (DESC)</option>
                                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Terlama (ASC)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-info flex-fill mr-2">
                                        <i class="fas fa-filter mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.grades.index') }}" class="btn btn-default flex-fill">
                                        <i class="fas fa-undo mr-1"></i> Reset
                                    </a>
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
                                    <th>Santri</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Tahun Ajaran</th>
                                    <th class="text-center">Semester</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grades as $g)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold">{{ $g->student->name }}</span><br>
                                                <small class="text-muted">{{ $g->student->nisn }} | {{ $g->student->grade }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $g->subject }}</td>
                                    <td class="text-center">{{ $g->academic_year }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $g->semester == 'Ganjil' ? 'badge-primary' : 'badge-info' }}">
                                            {{ $g->semester }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $g->type }}</span>
                                    </td>
                                    <td class="text-center">
                                        <h5 class="mb-0 font-weight-bold {{ $g->score < 75 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($g->score, 0) }}
                                        </h5>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.grades.destroy', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus nilai ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Data nilai tidak ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        {{ $grades->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.grades.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">Input Nilai Santri</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Santri <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-control select2" style="width: 100%" required>
                                <option value="">-- Cari Nama atau NISN --</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nisn }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Mata Pelajaran <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control" placeholder="Contoh: Fiqih Ibadah" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tahun Ajaran <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" class="form-control" placeholder="2025/2026" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester" class="form-control">
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipe Nilai</label>
                                    <select name="type" class="form-control">
                                        <option value="Tugas">Tugas</option>
                                        <option value="UTS">UTS</option>
                                        <option value="UAS">UAS</option>
                                        <option value="Rapor">Rapor (Akhir)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Skor Nilai (0-100) <span class="text-danger">*</span></label>
                                    <input type="number" name="score" class="form-control" step="0.01" min="0" max="100" placeholder="00.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Catatan/Keterangan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Hafalan lancar, tajwid perlu ditingkatkan."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Nilai</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- Memuat CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- PERBAIKAN: Menggunakan versi valid (1.5.2) --}}
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
            // Inisialisasi Select2 dengan Tema Bootstrap 4
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "-- Cari Nama atau NISN --",
                allowClear: true,
                // PERBAIKAN: Pasang dropdownParent agar search box bisa diklik di dalam Modal
                dropdownParent: $('#modalTambah')
            });

            // Fokus otomatis ke input pencarian Select2 saat modal dibuka
            $('#modalTambah').on('shown.bs.modal', function () {
                $(this).find('.select2').select2('open');
            });
        });
    </script>
@stop