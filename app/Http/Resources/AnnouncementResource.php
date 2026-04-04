<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->title,
            'isi_pengumuman' => $this->content,
            // Cek apakah ada lampiran. Jika ada, buatkan URL lengkapnya.
            'lampiran_url' => $this->attachment_url ? asset('storage/' . $this->attachment_url) : null,
            // Format: "14 Jan 2024" sesuai dengan desain UI
            'tanggal_dikirim' => Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}