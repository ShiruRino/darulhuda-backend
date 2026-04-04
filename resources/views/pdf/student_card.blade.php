<!DOCTYPE html>
<html>
<head>
    <title>Kartu Santri - {{ $student->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; }
        .card { 
            border: 2px solid #2c3e50; 
            border-radius: 10px; 
            width: 100%; 
            padding: 15px; 
            text-align: center;
            background-color: #ecf0f1;
        }
        .header { background-color: #2c3e50; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px;}
        .photo { width: 100px; height: 120px; object-fit: cover; border: 2px solid #34495e; border-radius: 5px; margin-bottom: 10px; }
        .name { font-size: 18px; font-weight: bold; margin: 5px 0; color: #2c3e50; }
        .info { font-size: 14px; margin: 3px 0; color: #555; }
        .footer { margin-top: 20px; font-size: 10px; color: #7f8c8d; border-top: 1px solid #ccc; padding-top: 5px;}
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3 style="margin: 0;">KARTU SANTRI</h3>
            <p style="margin: 0; font-size: 12px;">Madrasah Aliyah Darul Huda</p>
        </div>
        
        {{-- DomPDF butuh absolute path (public_path) untuk membaca gambar dengan baik --}}
        @if($student->photo_url)
            <img src="{{ public_path('storage/' . $student->photo_url) }}" class="photo" alt="Foto">
        @else
            <img src="{{ public_path('images/default-avatar.png') }}" class="photo" alt="Foto Default">
        @endif

        <div class="name">{{ strtoupper($student->name) }}</div>
        <div class="info"><strong>NISN:</strong> {{ $student->nisn }}</div>
        <div class="info"><strong>Kelas:</strong> {{ $student->grade }}</div>
        <div class="info"><strong>Asrama:</strong> {{ $student->dormitory ?? '-' }}</div>

        <div class="footer">
            Kartu ini adalah identitas resmi santri.<br>
            Harap dibawa saat kegiatan administrasi.
        </div>
    </div>
</body>
</html>