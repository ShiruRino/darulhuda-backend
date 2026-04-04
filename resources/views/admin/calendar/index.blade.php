@extends('adminlte::page')

@section('title', 'Kalender Akademik')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-alt mr-2"></i>Kalender Akademik</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus-circle"></i> Tambah Agenda
        </button>
    </div>
@stop

@section('content')
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icon fas fa-check mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Filter & Pencarian --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter Kalender</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.calendar.index') }}" method="GET">
                <div class="row">
                    {{-- Pencarian --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cari Nama Agenda</label>
                            <input type="text" name="search" class="form-control" placeholder="Contoh: Ujian Semester..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    {{-- Filter Kategori --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="type" class="form-control">
                                <option value="">-- Semua Kategori --</option>
                                <option value="holiday" {{ request('type') == 'holiday' ? 'selected' : '' }}>Libur</option>
                                <option value="exam" {{ request('type') == 'exam' ? 'selected' : '' }}>Ujian</option>
                                <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Kegiatan</option>
                                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    {{-- Sortir Waktu --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Urutkan Tanggal</label>
                            <select name="sort_dir" class="form-control">
                                <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Terdekat (ASC)</option>
                                <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Terlama (DESC)</option>
                            </select>
                            {{-- Parameter hidden untuk acuan sort_by di controller --}}
                            <input type="hidden" name="sort_by" value="start_date">
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-info flex-fill mr-2">
                                <i class="fas fa-filter mr-1"></i> Terapkan
                            </button>
                            <a href="{{ route('admin.calendar.index') }}" class="btn btn-default flex-fill">
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
                            <th width="25%">Waktu Pelaksanaan</th>
                            <th width="40%">Nama Agenda & Keterangan</th>
                            <th width="15%" class="text-center">Kategori</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y') }}</strong>
                                @if($event->end_date && $event->start_date != $event->end_date)
                                    <br><span class="text-muted">s/d {{ \Carbon\Carbon::parse($event->end_date)->translatedFormat('d M Y') }}</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-lg">{{ $event->title }}</strong><br>
                                <span class="text-muted">{{ \Illuminate\Support\Str::limit($event->description, 80, '...') ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeColors = [
                                        'holiday' => 'danger', 
                                        'exam' => 'warning', 
                                        'event' => 'primary', 
                                        'other' => 'secondary'
                                    ];
                                    $typeLabels = [
                                        'holiday' => 'Libur', 
                                        'exam' => 'Ujian', 
                                        'event' => 'Kegiatan', 
                                        'other' => 'Lainnya'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $badgeColors[$event->type] }} px-3 py-2 text-uppercase">
                                    {{ $typeLabels[$event->type] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit{{ $event->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('admin.calendar.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit per Item --}}
                        <div class="modal fade text-left" id="modalEdit{{ $event->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('admin.calendar.update', $event->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title font-weight-bold">Edit Agenda Akademik</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Agenda <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Tanggal Mulai <span class="text-danger">*</span></label>
                                                        <input type="date" name="start_date" class="form-control" value="{{ $event->start_date }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Tanggal Selesai (Opsional)</label>
                                                        <input type="date" name="end_date" class="form-control" value="{{ $event->end_date }}">
                                                        <small class="text-muted">Kosongkan jika agenda hanya 1 hari.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label>Kategori <span class="text-danger">*</span></label>
                                                <select name="type" class="form-control" required>
                                                    <option value="event" {{ $event->type == 'event' ? 'selected' : '' }}>Kegiatan Biasa</option>
                                                    <option value="holiday" {{ $event->type == 'holiday' ? 'selected' : '' }}>Libur Nasional / Pesantren</option>
                                                    <option value="exam" {{ $event->type == 'exam' ? 'selected' : '' }}>Ujian (UTS/UAS)</option>
                                                    <option value="other" {{ $event->type == 'other' ? 'selected' : '' }}>Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Keterangan / Deskripsi Tambahan</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $event->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada agenda akademik yang dijadwalkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $events->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Agenda --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.calendar.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white font-weight-bold">Tambah Agenda Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Agenda <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Libur Idul Fitri" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Selesai (Opsional)</label>
                                    <input type="date" name="end_date" class="form-control">
                                    <small class="text-muted">Kosongkan jika agenda hanya berlangsung 1 hari.</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="event">Kegiatan Biasa</option>
                                <option value="holiday">Libur Nasional / Pesantren</option>
                                <option value="exam">Ujian (UTS/UAS/Tahfidz)</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keterangan / Deskripsi Tambahan</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan keterangan detail agenda di sini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Agenda</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop