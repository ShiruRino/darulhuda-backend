<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul_tagihan' => $this->title,
            // Format angka menjadi format Rupiah (misal: 800000 -> Rp 800.000)
            'nominal_formatted' => 'Rp ' . number_format($this->amount, 0, ',', '.'),
            'nominal_raw' => $this->amount,
            // Format tanggal (misal: 2026-02-01 -> 01 Feb 2026)
            'jatuh_tempo' => Carbon::parse($this->due_date)->translatedFormat('d M Y'),
            'status' => $this->status, // 'unpaid', 'pending_verification', 'paid'
        ];
    }
}