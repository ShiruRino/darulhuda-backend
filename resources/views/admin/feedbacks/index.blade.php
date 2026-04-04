@extends('adminlte::page')

@section('title', 'Kritik & Saran')

@section('content_header')
    <h1><i class="fas fa-comments mr-2"></i>Manajemen Kritik & Saran</h1>
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
            <h3 class="card-title">Filter Masukan Orang Tua</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.feedbacks.index') }}" method="GET">
                <div class="row">
                    {{-- Search --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Cari Pesan / Nama</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari isi pesan atau pengirim..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Filter Kepuasan --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tingkat Kepuasan</label>
                            <select name="satisfaction" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="satisfied" {{ request('satisfaction') == 'satisfied' ? 'selected' : '' }}>Puas (Satisfied)</option>
                                <option value="not_satisfied" {{ request('satisfaction') == 'not_satisfied' ? 'selected' : '' }}>Tidak Puas</option>
                            </select>
                        </div>
                    </div>

                    {{-- Filter Status Baca --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status Baca</label>
                            <select name="is_read" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Belum Dibaca (Baru)</option>
                                <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
                            </select>
                        </div>
                    </div>

                    {{-- Sortir Waktu --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Urutan Waktu</label>
                            <select name="sort_dir" class="form-control">
                                <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Terbaru (DESC)</option>
                                <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Terlama (ASC)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-info flex-fill mr-2">
                                <i class="fas fa-filter mr-1"></i> Terapkan
                            </button>
                            <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-default flex-fill">
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
                <table class="table table-hover mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th width="5%">Status</th>
                            <th width="15%">Pengirim</th>
                            <th width="15%" class="text-center">Tingkat Kepuasan</th>
                            <th>Isi Pesan</th>
                            <th width="15%">Waktu Kirim</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $fb)
                        <tr class="{{ $fb->is_read ? 'text-muted' : 'bg-light font-weight-bold' }}" style="{{ $fb->is_read ? '' : 'border-left: 4px solid #007bff' }}">
                            <td class="text-center align-middle">
                                @if(!$fb->is_read)
                                    <span class="badge badge-primary">BARU</span>
                                @else
                                    <i class="fas fa-check-double text-success" title="Sudah dibaca"></i>
                                @endif
                            </td>
                            <td class="align-middle">
                                <strong>{{ $fb->user->name }}</strong><br>
                                <small>{{ $fb->user->relationship }} - {{ $fb->user->phone_number }}</small>
                            </td>
                            <td class="text-center align-middle">
                                @if($fb->satisfaction == 'satisfied')
                                    <span class="badge badge-success px-3 py-2">
                                        <i class="fas fa-smile mr-1"></i> PUAS
                                    </span>
                                @else
                                    <span class="badge badge-danger px-3 py-2">
                                        <i class="fas fa-frown mr-1"></i> TIDAK PUAS
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ \Illuminate\Support\Str::limit($fb->message, 120, '...') }}
                                @if(strlen($fb->message) > 120)
                                    <a href="#" class="ml-1" data-toggle="modal" data-target="#modalDetail{{ $fb->id }}">Baca Detail</a>
                                @endif
                            </td>
                            <td class="align-middle">
                                <small>{{ $fb->created_at->translatedFormat('d M Y') }}</small><br>
                                <small class="text-muted">{{ $fb->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    {{-- Tombol Tandai Sudah Dibaca --}}
                                    @if(!$fb->is_read)
                                        <form action="{{ route('admin.feedbacks.read', $fb->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Tandai Sudah Dibaca">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Detail Modal Button --}}
                                    <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalDetail{{ $fb->id }}" title="Lihat Detail Lengkap">
                                        <i class="fas fa-info-circle"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.feedbacks.destroy', $fb->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus masukan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Detail Pesan --}}
                        <div class="modal fade" id="modalDetail{{ $fb->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header {{ $fb->satisfaction == 'satisfied' ? 'bg-success' : 'bg-danger' }} text-white">
                                        <h5 class="modal-title">Detail Kritik & Saran</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Pengirim:</small>
                                                <strong>{{ $fb->user->name }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Waktu:</small>
                                                <strong>{{ $fb->created_at->format('d/m/Y H:i') }}</strong>
                                            </div>
                                        </div>
                                        <hr>
                                        <h6><strong>Pesan:</strong></h6>
                                        <p class="text-justify" style="white-space: pre-wrap;">{{ $fb->message }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        @if(!$fb->is_read)
                                            <form action="{{ route('admin.feedbacks.read', $fb->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-primary">Tandai Dibaca</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada kritik atau saran dari orang tua santri.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $feedbacks->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Mempertebal font untuk baris yang belum dibaca */
        .bg-light.font-weight-bold {
            color: #212529;
        }
    </style>
@stop