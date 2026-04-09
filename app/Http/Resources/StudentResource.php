<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nisn' => $this->nisn,
            'nik' => $this->nik,
            'nama_lengkap' => $this->name,
            'jenis_kelamin' => $this->gender,
            'kelas' => $this->grade,
            // Jika dormitory null, tampilkan 'Belum ditentukan'
            'asrama' => $this->dormitory ?? 'Belum ditentukan', 
            'tahun_masuk' => $this->admission_year,
            'foto' => $this->photo_url ? asset('storage/' . $this->photo_url) : asset('images/default-avatar.png'),
        ];
    }
}