@extends('adminlte::page')

@section('title', 'Manajemen Survei')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-poll-h mr-2"></i>Manajemen Survei Kepuasan</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah" id="btn-tambah-survei">
            <i class="fas fa-plus-circle mr-1"></i> Buat Survei Baru
        </button>
    </div>
@stop

@section('content')
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="icon fas fa-check mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="icon fas fa-times-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="icon fas fa-exclamation-triangle mr-2"></i>Gagal menyimpan! Pastikan semua kolom pertanyaan dan judul sudah diisi.
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Tabel Data Survei --}}
    <div class="card card-outline card-primary">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th width="30%">Judul Survei</th>
                            <th width="15%" class="text-center">Responden</th>
                            <th width="20%">Batas Waktu</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surveys as $survey)
                        <tr>
                            <td class="align-middle">
                                <strong class="text-lg">{{ $survey->title }}</strong><br>
                                <span class="text-muted small">{{ \Illuminate\Support\Str::limit($survey->description, 50, '...') }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-info px-3 py-2" style="font-size: 14px;">
                                    <i class="fas fa-users mr-1"></i> {{ $survey->responses_count }} Orang
                                </span>
                            </td>
                            <td class="align-middle">
                                @if($survey->end_date)
                                    @php
                                        $isExpired = \Carbon\Carbon::parse($survey->end_date)->isPast() && !$survey->end_date->isToday();
                                    @endphp
                                    <span class="{{ $isExpired ? 'text-danger font-weight-bold' : '' }}">
                                        {{ \Carbon\Carbon::parse($survey->end_date)->translatedFormat('d M Y') }}
                                        @if($isExpired) <br><small><i class="fas fa-times-circle"></i> Berakhir</small> @endif
                                    </span>
                                @else
                                    <span class="text-muted">Tidak ada batas waktu</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <form action="{{ route('admin.surveys.status', $survey->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    @if($survey->is_active)
                                        <button type="submit" class="btn btn-sm btn-success" title="Klik untuk menonaktifkan">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-secondary" title="Klik untuk mengaktifkan">
                                            <i class="fas fa-ban"></i> Ditutup
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-center align-middle">
                                <a href="{{ route('admin.surveys.responses', $survey->id) }}" class="btn btn-sm btn-success" title="Lihat Hasil Responden">
                                    <i class="fas fa-users"></i> Hasil
                                </a>
                                <form action="{{ route('admin.surveys.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('Peringatan: Menghapus survei akan ikut menghapus semua data jawaban dari orang tua. Lanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada survei yang dibuat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $surveys->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Survei & Form Dinamis --}}
    <div class="modal fade" id="modalTambah" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="{{ route('admin.surveys.store') }}" method="POST" id="form-survei">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white font-weight-bold">Buat Survei / Kuesioner Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    
                    <div class="modal-body bg-light">
                        <div class="row">
                            {{-- Kolom Kiri: Informasi Dasar Survei --}}
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white">
                                        <h6 class="font-weight-bold mb-0">Pengaturan Survei</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Judul Survei <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="Contoh: Kualitas Makanan Kantin" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Batas Waktu (Opsional)</label>
                                            <input type="date" name="end_date" class="form-control" min="{{ date('Y-m-d') }}">
                                            <small class="text-muted">Setelah tanggal ini, orang tua tidak bisa mengisi.</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Deskripsi / Pengantar</label>
                                            <textarea name="description" class="form-control" rows="5" placeholder="Tuliskan tujuan survei ini untuk dibaca oleh orang tua..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan: Daftar Pertanyaan Dinamis --}}
                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <h6 class="font-weight-bold mb-0">Daftar Pertanyaan</h6>
                                        <button type="button" class="btn btn-sm btn-info" id="btn-add-question">
                                            <i class="fas fa-plus"></i> Tambah Pertanyaan
                                        </button>
                                    </div>
                                    <div class="card-body" style="max-height: 60vh; overflow-y: auto;" id="questions-container">
                                        
                                        {{-- Template Pertanyaan Pertama (Wajib ada 1) --}}
                                        <div class="question-block border p-3 mb-3 bg-white rounded position-relative">
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <label>Pertanyaan #1 <span class="text-danger">*</span></label>
                                                    <input type="text" name="questions[0][question_text]" class="form-control" placeholder="Ketik pertanyaan di sini..." required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group mb-0">
                                                    <label>Jenis Jawaban</label>
                                                    <select name="questions[0][type]" class="form-control" required>
                                                        <option value="rating">Rating (Bintang 1 - 5)</option>
                                                        <option value="text">Teks Bebas / Uraian</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group mb-0">
                                                    <label>Wajib Diisi?</label>
                                                    <select name="questions[0][is_required]" class="form-control" required>
                                                        <option value="1">Ya, Wajib</option>
                                                        <option value="0">Tidak (Opsional)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Akhir Template Pertanyaan Pertama --}}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Publikasikan Survei</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Index array dimulai dari 1 karena index 0 sudah dipakai oleh pertanyaan pertama
        let questionIndex = 1; 

        // Fungsi Tambah Pertanyaan
        $('#btn-add-question').click(function() {
            let questionHtml = `
                <div class="question-block border p-3 mb-3 bg-white rounded position-relative" style="display: none;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute btn-remove-question" style="top: 10px; right: 10px;" title="Hapus Pertanyaan">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="row">
                        <div class="col-md-11 form-group">
                            <label>Pertanyaan #${questionIndex + 1} <span class="text-danger">*</span></label>
                            <input type="text" name="questions[${questionIndex}][question_text]" class="form-control" placeholder="Ketik pertanyaan di sini..." required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-0">
                            <label>Jenis Jawaban</label>
                            <select name="questions[${questionIndex}][type]" class="form-control" required>
                                <option value="rating">Rating (Bintang 1 - 5)</option>
                                <option value="text">Teks Bebas / Uraian</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label>Wajib Diisi?</label>
                            <select name="questions[${questionIndex}][is_required]" class="form-control" required>
                                <option value="1">Ya, Wajib</option>
                                <option value="0">Tidak (Opsional)</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            
            // Tambahkan elemen ke container dan beri efek animasi slideDown
            let newElement = $(questionHtml).appendTo('#questions-container');
            newElement.slideDown(200);
            
            // Scroll otomatis ke pertanyaan terbaru
            $('#questions-container').animate({
                scrollTop: $('#questions-container')[0].scrollHeight
            }, 300);

            questionIndex++;
        });

        // Fungsi Hapus Pertanyaan (Delegasi Event karena elemen ditambahkan dinamis)
        $('#questions-container').on('click', '.btn-remove-question', function() {
            $(this).closest('.question-block').slideUp(200, function() {
                $(this).remove();
            });
        });

        // Reset form jika modal ditutup agar saat dibuka lagi tidak menumpuk
        $('#modalTambah').on('hidden.bs.modal', function () {
            // Hapus semua block pertanyaan kecuali yang pertama
            $('.question-block').not(':first').remove();
            // Reset input fields
            $('#form-survei')[0].reset();
            // Reset index kembali ke 1
            questionIndex = 1;
        });
    });
</script>
@stop