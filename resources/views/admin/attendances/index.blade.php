@extends('adminlte::page')

@section('title', 'Manajemen Absensi Santri')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1><i class="fas fa-user-check mr-2"></i>Manajemen Absensi</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus"></i> Catat Kehadiran
        </button>
    </div>
@stop

@section('content')
    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
            {{ session('success') }}
        </div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter & Pencarian</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.attendances.index') }}" method="GET">
                <div class="row">
                    {{-- Search Nama/NISN --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Cari Santri</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama atau NISN..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Filter Status --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                                <option value="sick" {{ request('status') == 'sick' ? 'selected' : '' }}>Sakit</option>
                                <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Izin</option>
                                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Alpa</option>
                            </select>
                        </div>
                    </div>

                    {{-- Rentang Tanggal --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Dari Tanggal</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Sampai Tanggal</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-info flex-fill mr-2">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('admin.attendances.index') }}" class="btn btn-default flex-fill">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- Hidden Input untuk Sorting agar tidak hilang saat filter --}}
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'date') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">
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
                            {{-- Header dengan fitur sortir --}}
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date', 'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}" class="text-white">
                                    Tanggal @if(request('sort_by') == 'date') <i class="fas fa-sort-{{ request('sort_dir') == 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th>Santri</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->date)->translatedFormat('d M Y') }}</td>
                            <td>
                                <strong>{{ $row->student->name }}</strong><br>
                                <small class="text-muted">{{ $row->student->nisn }} | {{ $row->student->grade }}</small>
                            </td>
                            <td class="text-center">
                                @php
                                    $badges = ['present' => 'success', 'sick' => 'warning', 'leave' => 'info', 'absent' => 'danger'];
                                    $labels = ['present' => 'Hadir', 'sick' => 'Sakit', 'leave' => 'Izin', 'absent' => 'Alpa'];
                                @endphp
                                <span class="badge badge-{{ $badges[$row->status] }} px-3 py-2">
                                    {{ $labels[$row->status] }}
                                </span>
                            </td>
                            <td>{{ $row->notes ?? '-' }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit{{ $row->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.attendances.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data absensi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit per Baris --}}
                        <div class="modal fade" id="modalEdit{{ $row->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.attendances.update', $row->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header"><h5>Edit Absensi: {{ $row->student->name }}</h5></div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Status Kehadiran</label>
                                                <select name="status" class="form-control">
                                                    <option value="present" {{ $row->status == 'present' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="sick" {{ $row->status == 'sick' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="leave" {{ $row->status == 'leave' ? 'selected' : '' }}>Izin</option>
                                                    <option value="absent" {{ $row->status == 'absent' ? 'selected' : '' }}>Alpa</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan Tambahan</label>
                                                <textarea name="notes" class="form-control" rows="3">{{ $row->notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Data absensi tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right">
                {{ $attendances->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Absensi --}}
    <div class="modal fade" id="modalTambah">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.attendances.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Catat Kehadiran Santri</h5>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="present">Hadir</option>
                                        <option value="sick">Sakit</option>
                                        <option value="leave">Izin</option>
                                        <option value="absent">Alpa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" placeholder="Contoh: Sakit demam, Izin pulang ada acara keluarga..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Absensi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--bootstrap4 .select2-selection--single { height: calc(2.25rem + 2px) !important; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk pencarian santri yang responsif
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "-- Cari Nama atau NISN --",
                allowClear: true
            });

            // Auto-focus pada searchbox Select2 saat modal dibuka
            $('#modalTambah').on('shown.bs.modal', function () {
                $(this).find('.select2').select2('open');
            });
        });
    </script>
@stop