@extends('adminlte::page')

@section('title', 'Detail Santri - ' . $student->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-id-card mr-2"></i>Rekam Jejak & Profil Santri</h1>
        <a href="{{ route('admin.students.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>
@stop

@section('content')
<div class="row">
    {{-- ========================================== --}}
    {{-- KOLOM KIRI: INFORMASI PROFIL & WALI --}}
    {{-- ========================================== --}}
    <div class="col-md-3">
        
        {{-- Kartu Profil Utama --}}
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center mb-3">
                    <img class="profile-user-img img-fluid img-circle elevation-2"
                         src="{{ $student->photo_url ? asset('storage/' . $student->photo_url) : asset('images/default-avatar.png') }}"
                         alt="Foto Profil {{ $student->name }}" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                         <a href="{{ route('admin.documents.card', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block mt-2">
                            <i class="fas fa-print"></i> Cetak Kartu Santri
                        </a>
                </div>

                <h3 class="profile-username text-center font-weight-bold">{{ $student->name }}</h3>
                <p class="text-muted text-center">{{ $student->nisn }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Kelas</b> <a class="float-right badge badge-primary px-2 py-1">{{ $student->grade }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Jenis Kelamin</b> <a class="float-right">{{ $student->gender }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Asrama</b> <a class="float-right">{{ $student->dormitory ?? 'Belum ditentukan' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Tahun Masuk</b> <a class="float-right">{{ $student->admission_year }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>NIK</b> <a class="float-right">{{ $student->nik ?? '-' }}</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Kartu Informasi Orang Tua / Wali --}}
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-friends mr-1"></i> Informasi Wali</h3>
            </div>
            <div class="card-body">
                @if($student->parent)
                    <strong><i class="fas fa-user mr-1"></i> Nama Wali</strong>
                    <p class="text-muted mb-2">{{ $student->parent->name }} ({{ $student->parent->relationship }})</p>
                    
                    <strong><i class="fab fa-whatsapp mr-1"></i> Kontak</strong>
                    <p class="text-muted mb-2">{{ $student->parent->phone_number }}</p>
                    
                    <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                    <p class="text-muted mb-2">{{ $student->parent->email ?? '-' }}</p>
                    
                    <strong><i class="fas fa-mobile-alt mr-1"></i> Akses Aplikasi</strong>
                    <p class="text-muted mt-1 mb-0">
                        @if($student->parent->is_active)
                            <span class="badge badge-success"><i class="fas fa-check"></i> Aktif Terhubung</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-ban"></i> Akses Dinonaktifkan</span>
                        @endif
                    </p>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i><br>
                        <small>Belum ada akun orang tua yang terhubung dengan santri ini.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- KOLOM KANAN: TABS (NILAI, TAGIHAN, ABSEN) --}}
    {{-- ========================================== --}}
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills" id="student-tabs">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" href="#tab-nilai" data-toggle="tab">
                            <i class="fas fa-graduation-cap mr-1"></i> Nilai Akademik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="#tab-tagihan" data-toggle="tab">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> Riwayat Keuangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="#tab-absensi" data-toggle="tab">
                            <i class="fas fa-user-check mr-1"></i> Kehadiran (30 Terakhir)
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    
                    {{-- TAB 1: NILAI AKADEMIK --}}
                    <div class="active tab-pane" id="tab-nilai">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tahun/Semester</th>
                                        <th>Mata Pelajaran</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Skor</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($student->grades as $grade)
                                        <tr>
                                            <td class="align-middle">
                                                <strong>{{ $grade->academic_year }}</strong> <br> 
                                                <span class="text-muted small">{{ $grade->semester }}</span>
                                            </td>
                                            <td class="align-middle font-weight-bold">{{ $grade->subject }}</td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary px-2 py-1">{{ $grade->type }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <h5 class="mb-0 font-weight-bold {{ $grade->score < 75 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($grade->score, 0) }}
                                                </h5>
                                            </td>
                                            <td class="align-middle text-muted small">{{ $grade->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i><br>
                                                Belum ada data nilai akademik.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: TAGIHAN & KEUANGAN --}}
                    <div class="tab-pane" id="tab-tagihan">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tanggal Terbit</th>
                                        <th>Rincian Tagihan</th>
                                        <th>Nominal</th>
                                        <th>Jatuh Tempo</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($student->invoices as $invoice)
                                        <tr>
                                            <td class="align-middle">{{ $invoice->created_at->format('d M Y') }}</td>
                                            <td class="align-middle font-weight-bold">{{ $invoice->title }}</td>
                                            <td class="align-middle">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                            <td class="align-middle">
                                                @php
                                                    $isOverdue = \Carbon\Carbon::parse($invoice->due_date)->isPast() && $invoice->status != 'paid';
                                                @endphp
                                                <span class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
                                                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                                    @if($isOverdue) <br><small><i class="fas fa-exclamation-circle"></i> Terlewat</small> @endif
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($invoice->status == 'paid')
                                                    <span class="badge badge-success px-3 py-2"><i class="fas fa-check-double"></i> Lunas</span>
                                                @elseif($invoice->status == 'pending_verification')
                                                    <span class="badge badge-warning px-3 py-2"><i class="fas fa-clock"></i> Cek Mutasi</span>
                                                @else
                                                    <span class="badge badge-danger px-3 py-2"><i class="fas fa-times"></i> Belum Bayar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-file-invoice fa-3x mb-3 text-light"></i><br>
                                                Belum ada riwayat tagihan keuangan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: ABSENSI --}}
                    <div class="tab-pane" id="tab-absensi">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th class="text-center">Kehadiran</th>
                                        <th>Keterangan Tambahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($student->attendances as $attendance)
                                        <tr>
                                            <td class="align-middle">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}</td>
                                            <td class="align-middle">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</td>
                                            <td class="text-center align-middle">
                                                @php
                                                    $badges = ['present' => 'success', 'sick' => 'warning', 'leave' => 'info', 'absent' => 'danger'];
                                                    $labels = ['present' => 'Hadir', 'sick' => 'Sakit', 'leave' => 'Izin', 'absent' => 'Alpa'];
                                                @endphp
                                                <span class="badge badge-{{ $badges[$attendance->status] }} px-3 py-2">
                                                    {{ $labels[$attendance->status] }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-muted small">{{ $attendance->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i><br>
                                                Belum ada data riwayat absensi.
                                            </td>
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
    // Menyimpan posisi tab terakhir yang dibuka oleh Admin agar tidak kembali ke tab awal saat halam di-refresh
    $(document).ready(function() {
        var activeTab = localStorage.getItem('activeStudentDetailTab');
        if(activeTab){
            $('#student-tabs a[href="' + activeTab + '"]').tab('show');
        }
        
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeStudentDetailTab', $(e.target).attr('href'));
        });
    });
</script>
@stop