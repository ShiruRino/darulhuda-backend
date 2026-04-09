@extends('adminlte::page')

@section('title', 'Kritik & Saran')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-comments mr-2"></i>Daftar Kritik & Saran Wali Santri</h1>
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

    {{-- Filter & Pencarian --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter Pencarian</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.feedbacks.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Cari Kata Kunci / Nama Pengirim</label>
                            <input type="text" name="search" class="form-control" placeholder="Ketik nama atau isi pesan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status Pesan</label>
                            <select name="is_read" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Belum Dibaca (Baru)</option>
                                <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-info flex-fill mr-2"><i class="fas fa-search mr-1"></i> Filter</button>
                            <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-default flex-fill"><i class="fas fa-undo mr-1"></i> Reset</a>
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
                            <th width="15%">Tanggal Masuk</th>
                            <th width="20%">Pengirim</th>
                            <th width="20%">Rating Kepuasan</th>
                            <th width="25%">Cuplikan Pesan</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $row)
                        <tr class="{{ !$row->is_read ? 'bg-light font-weight-bold' : '' }}">
                            <td class="align-middle">
                                {{ $row->created_at->translatedFormat('d M Y') }}<br>
                                <span class="text-muted small"><i class="far fa-clock"></i> {{ $row->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="align-middle">
                                {{ $row->user->name ?? 'User Dihapus' }}<br>
                                <span class="text-muted small">Wali Santri</span>
                            </td>
                            <td class="align-middle">
                                <div class="mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $row->rating ? 'text-warning' : 'text-black-50' }}"></i>
                                    @endfor
                                </div>
                                @if($row->rating >= 4)
                                    <span class="badge badge-success px-2 py-1">Puas</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1">Tidak Puas</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ \Illuminate\Support\Str::limit($row->message, 50, '...') }}
                            </td>
                            <td class="text-center align-middle">
                                @if($row->is_read)
                                    <span class="badge badge-secondary"><i class="fas fa-check-double"></i> Dibaca</span>
                                @else
                                    <span class="badge badge-primary"><i class="fas fa-envelope"></i> Baru</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    {{-- Tombol Lihat Detail / Baca Pesan --}}
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalBaca{{ $row->id }}" title="Baca Pesan">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.feedbacks.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini secara permanen?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Baca Pesan --}}
                        <div class="modal fade text-left" id="modalBaca{{ $row->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-envelope-open-text mr-2"></i> Detail Pesan
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                            <div>
                                                <strong>Dari:</strong> {{ $row->user->name ?? 'User Tidak Dikenal' }}<br>
                                                <small class="text-muted">{{ $row->created_at->translatedFormat('l, d F Y H:i') }}</small>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-warning text-lg">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $row->rating ? '' : 'text-black-50' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mb-0" style="white-space: pre-line;">{{ $row->message }}</p>
                                    </div>
                                    <div class="modal-footer justify-content-between bg-light">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                        
                                        {{-- Tombol Tandai Sudah Dibaca (Jika status masih baru) --}}
                                        @if(!$row->is_read)
                                            <form action="{{ route('admin.feedbacks.read', $row->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check-circle mr-1"></i> Tandai Sudah Dibaca
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success font-weight-bold"><i class="fas fa-check-double"></i> Telah dibaca</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Belum ada kritik atau saran yang masuk.
                            </td>
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