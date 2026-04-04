<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Map status database ke label bahasa Indonesia
        $statusLabels = [
            'present' => 'Hadir',
            'sick'    => 'Sakit',
            'leave'   => 'Izin',
            'absent'  => 'Alpa',
        ];

        return [
            'id' => $this->id,
            // Format tanggal (misal: 2026-04-03 -> 03 April 2026)
            'tanggal_raw' => $this->date->format('Y-m-d'),
            'tanggal_formatted' => Carbon::parse($this->date)->translatedFormat('d F Y'),
            'hari' => Carbon::parse($this->date)->translatedFormat('l'),
            'status_raw' => $this->status,
            'status_label' => $statusLabels[$this->status] ?? $this->status,
            'keterangan' => $this->notes ?? '-',
        ];
    }
}