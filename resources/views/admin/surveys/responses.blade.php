@extends('adminlte::page')

@section('title', 'Hasil Survei')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-poll-h mr-2"></i>Hasil Survei Responden</h1>
            <h5 class="text-muted mt-2">{{ $survey->title }}</h5>
        </div>
        <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Daftar Responden (Total: {{ $responses->total() }} Orang)</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="20%">Waktu Mengisi</th>
                            <th width="40%">Nama Responden (Wali Santri)</th>
                            <th width="25%" class="text-center">Total Jawaban</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($responses as $index => $resp)
                        <tr>
                            <td class="text-center align-middle">{{ $responses->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <strong>{{ $resp->created_at->translatedFormat('d M Y') }}</strong><br>
                                <span class="text-muted small">{{ $resp->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="align-middle">
                                <span class="font-weight-bold">{{ $resp->user->name ?? 'User Tidak Diketahui' }}</span><br>
                                <small class="text-muted">{{ $resp->user->email ?? $resp->user->phone_number }}</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-info">{{ $resp->answers->count() }} Jawaban</span>
                            </td>
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalLihat{{ $resp->id }}" title="Lihat Jawaban">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Detail Jawaban --}}
                        <div class="modal fade text-left" id="modalLihat{{ $resp->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-clipboard-list mr-2"></i> Jawaban: {{ $resp->user->name ?? 'User' }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body p-4">
                                        @foreach($survey->questions as $q)
                                            @php
                                                // Mencari jawaban spesifik dari responden ini untuk pertanyaan ini
                                                $answer = $resp->answers->firstWhere('survey_question_id', $q->id);
                                            @endphp
                                            <div class="mb-4 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                                <h6 class="font-weight-bold text-dark mb-2">
                                                    {{ $loop->iteration }}. {{ $q->question_text }}
                                                </h6>
                                                
                                                @if($answer)
                                                    @if($q->type == 'rating')
                                                        <div class="text-warning text-lg">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= (int)$answer->answer_value ? '' : 'text-black-50' }}"></i>
                                                            @endfor
                                                            <span class="text-dark text-sm ml-2 font-weight-bold">({{ $answer->answer_value }} / 5)</span>
                                                        </div>
                                                    @else
                                                        <p class="text-muted mb-0 bg-light p-2 rounded border" style="white-space: pre-line;">
                                                            {{ $answer->answer_value }}
                                                        </p>
                                                    @endif
                                                @else
                                                    <p class="text-danger small mb-0"><i>(Tidak dijawab)</i></p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Belum ada wali santri yang mengisi survei ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $responses->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@stop