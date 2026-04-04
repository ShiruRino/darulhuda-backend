@extends('adminlte::page')

@section('title', 'Data Pembayaran')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-file-invoice-dollar mr-2"></i>Manajemen Keuangan</h1>
    </div>
@stop

@section('content')
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="icon fas fa-check mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="icon fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan input.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
    @endif

    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="payment-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="tab-invoices-tab" data-toggle="pill" href="#tab-invoices" role="tab">
                        <i class="fas fa-users mr-1"></i> Data Tagihan Santri
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab-master-tab" data-toggle="pill" href="#tab-master" role="tab">
                        <i class="fas fa-cog mr-1"></i> Pengaturan Master Tagihan
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <div class="tab-content" id="payment-tabs-content">
                
                {{-- ========================================== --}}
                {{-- TAB 1: DATA TAGIHAN SANTRI --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade show active" id="tab-invoices" role="tabpanel">
                    
                    {{-- Filter & Pencarian Tagihan --}}
                    <div class="bg-light p-3 rounded mb-3 border">
                        <form action="{{ route('admin.payments.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Pencarian</label>
                                    <input type="text" name="search" class="form-control" placeholder="Judul / Nama / NISN..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Status Bayar</label>
                                    <select name="status" class="form-control">
                                        <option value="">-- Semua --</option>
                                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                                        <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Menunggu Cek</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Urutkan Berdasarkan</label>
                                    <select name="sort_by" class="form-control">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tgl Diterbitkan</option>
                                        <option value="due_date" {{ request('sort_by') == 'due_date' ? 'selected' : '' }}>Jatuh Tempo</option>
                                        <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>Nominal</option>
                                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status Bayar</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Arah Urutan</label>
                                    <select name="sort_dir" class="form-control">
                                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Terbaru (DESC)</option>
                                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Terlama (ASC)</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-info flex-fill mr-2"><i class="fas fa-filter"></i> Filter</button>
                                    <a href="{{ route('admin.payments.index') }}" class="btn btn-default flex-fill"><i class="fas fa-undo"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tabel Tagihan Santri --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped border">
                            <thead class="bg-dark">
                                <tr>
                                    <th>Santri</th>
                                    <th>Rincian Tagihan</th>
                                    <th>Nominal</th>
                                    <th>Jatuh Tempo</th>
                                    <th class="text-center">Status Saat Ini</th>
                                    <th class="text-center">Ubah Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td>
                                            <strong>{{ $inv->student->name }}</strong><br>
                                            <span class="text-muted small"><i class="fas fa-id-badge"></i> {{ $inv->student->nisn }} | Kelas: {{ $inv->student->grade }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $inv->title }}</strong><br>
                                            <span class="text-muted small">Diterbitkan: {{ $inv->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <h5 class="mb-0 font-weight-bold">Rp {{ number_format($inv->amount, 0, ',', '.') }}</h5>
                                        </td>
                                        <td>
                                            @php
                                                $isOverdue = \Carbon\Carbon::parse($inv->due_date)->isPast() && $inv->status != 'paid';
                                            @endphp
                                            <span class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
                                                {{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}
                                                @if($isOverdue) <br><small><i class="fas fa-exclamation-circle"></i> Terlewat</small> @endif
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($inv->status == 'paid')
                                                <span class="badge badge-success px-3 py-2"><i class="fas fa-check-double"></i> Lunas</span>
                                            @elseif($inv->status == 'pending_verification')
                                                <span class="badge badge-warning px-3 py-2"><i class="fas fa-clock"></i> Cek Mutasi</span>
                                            @else
                                                <span class="badge badge-danger px-3 py-2"><i class="fas fa-times"></i> Belum Bayar</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            {{-- Form Ubah Status Instan --}}
                                            <form action="{{ route('admin.payments.invoice.update_status', $inv->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="input-group input-group-sm">
                                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                                        <option value="unpaid" {{ $inv->status == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                                                        <option value="pending_verification" {{ $inv->status == 'pending_verification' ? 'selected' : '' }}>Pending Cek</option>
                                                        <option value="paid" {{ $inv->status == 'paid' ? 'selected' : '' }}>Lunas</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.documents.invoice', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Download Kuitansi">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada data tagihan ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <div class="float-right">
                            {{ $invoices->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- TAB 2: PENGATURAN MASTER TAGIHAN --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade" id="tab-master" role="tabpanel">
                    
                    <div class="row">
                        {{-- Form Tambah Master --}}
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">Buat Master Baru</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.payments.master.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label>Nama Tagihan</label>
                                            <input type="text" name="title" class="form-control" placeholder="Contoh: SPP Bulanan" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Nominal Default (Rp)</label>
                                            <input type="number" name="amount" class="form-control" placeholder="Contoh: 500000" required>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save"></i> Simpan Master</button>
                                    </form>
                                </div>
                                <div class="card-footer text-muted small">
                                    <i class="fas fa-info-circle"></i> Master ini akan digunakan sebagai *template* saat fitur *generate* tagihan massal dijalankan.
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Master --}}
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-secondary">
                                        <tr>
                                            <th>Nama Template Tagihan</th>
                                            <th>Nominal Default</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($masterInvoices as $master)
                                            <tr>
                                                <td class="align-middle font-weight-bold">{{ $master->title }}</td>
                                                <td class="align-middle">Rp {{ number_format($master->amount, 0, ',', '.') }}</td>
                                                <td class="text-center align-middle">
                                                    <span class="badge {{ $master->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                        {{ $master->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <form action="{{ route('admin.payments.master.destroy', $master->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus template ini? Tagihan santri yang sudah terbuat tidak akan terhapus.');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-3 text-muted">Belum ada master tagihan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Script agar ketika halaman di-refresh setelah filter/paginasi, 
        // tab yang sedang aktif (Data Tagihan) tidak berpindah ke Tab Master.
        $(document).ready(function() {
            var activeTab = localStorage.getItem('activePaymentTab');
            if(activeTab){
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }
            
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                localStorage.setItem('activePaymentTab', $(e.target).attr('href'));
            });
        });
    </script>
@stop