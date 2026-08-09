<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kasus Sekolah</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0 0 4px; font-size: 16px; }
        .header h3 { margin: 0; font-size: 14px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
        .footer { margin-top: 30px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('sibuka.school_name') }}</h2>
        <h3>LAPORAN BUKU KASUS SEKOLAH</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="70">Tanggal</th>
                <th width="60">NIS</th>
                <th>Nama</th>
                <th width="70">Kelas</th>
                <th width="50">Jur</th>
                <th width="70">Kategori</th>
                <th>Judul</th>
                <th width="40">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cases as $i => $case)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $case->tanggal_kejadian->format('d/m/Y') }}</td>
                    <td>{{ $case->student?->nis ?? '-' }}</td>
                    <td>{{ $case->student?->nama ?? '-' }}</td>
                    <td>{{ $case->student?->kelas?->nama ?? '-' }}</td>
                    <td>{{ $case->student?->kelas?->jurusan?->kode ?? '-' }}</td>
                    <td>{{ ucfirst($case->kategori->value) }}</td>
                    <td>{{ $case->judul }}</td>
                    <td>{{ $case->poin > 0 ? '+' : '' }}{{ $case->poin }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data kasus pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }} — SIBUKA</p>
</body>
</html>
