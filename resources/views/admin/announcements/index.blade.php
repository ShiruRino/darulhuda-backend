@extends('adminlte::page')

@section('title', 'Manajemen Pengumuman')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bullhorn mr-2"></i>Manajemen Pengumuman</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus-circle"></i> Buat Pengumuman
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
            <i class="icon fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan saat menyimpan data. Pastikan semua kolom wajib diisi.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    @endif

    {{-- Filter & Pencarian --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter & Pencarian</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.announcements.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Cari Kata Kunci</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari Judul atau Isi Pengumuman..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Urutkan Berdasarkan</label>
                            <select name="sort_by" class="form-control">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Waktu Dibuat</option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Judul Pengumuman</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Arah Urutan</label>
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
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-default flex-fill">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data Pengumuman --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th width="15%">Tanggal Diterbitkan</th>
                            <th width="25%">Judul Pengumuman</th>
                            <th width="35%">Isi Singkat</th>
                            <th width="10%" class="text-center">Lampiran</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $row)
                        <tr>
                            <td class="align-middle">
                                <strong>{{ $row->created_at->translatedFormat('d M Y') }}</strong><br>
                                <span class="text-muted small"><i class="far fa-clock"></i> {{ $row->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="align-middle">
                                <strong class="text-primary">{{ $row->title }}</strong>
                            </td>
                            <td class="align-middle">
                                {{ \Illuminate\Support\Str::limit($row->content, 90, '...') }}
                            </td>
                            <td class="text-center align-middle">
                                @if($row->attachment_url)
                                    <a href="{{ $row->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat Lampiran">
                                        <i class="fas fa-link"></i> Link
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit{{ $row->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.announcements.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini secara permanen?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit Pengumuman --}}
                        <div class="modal fade text-left" id="modalEdit{{ $row->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('admin.announcements.update', $row->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title font-weight-bold">Edit Pengumuman</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Judul Pengumuman <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $row->title }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Isi Pengumuman <span class="text-danger">*</span></label>
                                                <textarea name="content" class="form-control" rows="6" required>{{ $row->content }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>URL Lampiran (Opsional)</label>
                                                <input type="url" name="attachment_url" class="form-control" value="{{ $row->attachment_url }}" placeholder="https://...">
                                                <small class="text-muted">Gunakan link Google Drive, Docs, atau link eksternal lainnya jika ada dokumen terkait.</small>
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
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada pengumuman yang diterbitkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $announcements->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pengumuman --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">Terbitkan Pengumuman Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul Pengumuman <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Pemberitahuan Libur Akhir Semester" required>
                        </div>
                        <div class="form-group">
                            <label>Isi Pengumuman <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="6" placeholder="Ketik isi pengumuman yang ingin disampaikan kepada orang tua santri..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>URL Lampiran (Opsional)</label>
                            <input type="url" name="attachment_url" class="form-control" placeholder="https://...">
                            <small class="text-muted">Masukkan URL valid (http/https). Kosongkan jika tidak ada lampiran.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Terbitkan Sekarang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop