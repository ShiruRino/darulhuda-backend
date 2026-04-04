<!DOCTYPE html>
<html>
<head>
    <title>Kuitansi Pembayaran #{{ $invoice->id }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; color: #2c3e50; margin: 0; }
        .subtitle { font-size: 14px; color: #7f8c8d; margin: 0; }
        .row { width: 100%; display: table; margin-bottom: 20px; }
        .col { display: table-cell; width: 50%; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; color: #333; }
        .total-row td { font-weight: bold; font-size: 16px; background-color: #ecf0f1;}
        .status-paid { color: #27ae60; font-weight: bold; border: 2px solid #27ae60; padding: 5px 10px; text-transform: uppercase; display: inline-block; transform: rotate(-10deg);}
    </style>
</head>
<body>
    <div class="header">
        <p class="title">KUITANSI PEMBAYARAN RESMI</p>
        <p class="subtitle">Madrasah Teknologi Al-Fulan | Tlp: (021) 12345678</p>
    </div>

    <div class="row">
        <div class="col">
            <strong>Diterima dari (Wali Santri):</strong><br>
            {{ $invoice->student->parent ? $invoice->student->parent->name : 'Wali Santri' }}<br>
            <strong>Untuk Santri:</strong><br>
            {{ $invoice->student->name }} ({{ $invoice->student->nisn }} - Kelas {{ $invoice->student->grade }})
        </div>
        <div class="col" style="text-align: right;">
            <strong>Tanggal Terbit:</strong> {{ $invoice->created_at->format('d M Y') }}<br>
            <strong>Nomor Invoice:</strong> #INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}<br>
            <br>
            @if($invoice->status == 'paid')
                <div class="status-paid">L U N A S</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="65%">Keterangan Pembayaran</th>
                <th width="30%" style="text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $invoice->title }}</td>
                <td style="text-align: right;">{{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">TOTAL PEMBAYARAN</td>
                <td style="text-align: right;">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 12px; color: #7f8c8d;">Catatan: Bukti pembayaran ini di-generate secara otomatis oleh sistem dan sah tanpa tanda tangan.</p>
</body>
</html>