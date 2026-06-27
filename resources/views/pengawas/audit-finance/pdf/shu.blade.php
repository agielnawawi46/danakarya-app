<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan SHU</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
        h2 { text-align: center; font-size: 14px; margin-bottom: 20px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header-type { background-color: #f1f5f9; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Laporan Distribusi SHU</h1>
    <h2>Tahun {{ $year }}</h2>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    @forelse($distributions as $dist)
    <table>
        <thead>
            <tr><td colspan="4" class="header-type">Distribusi SHU (Status: {{ ucfirst($dist->status) }})</td></tr>
        </thead>
        <tbody>
            <tr>
                <td width="25%">Laba Bersih</td><td width="25%" class="text-right font-bold">Rp {{ number_format($dist->total_profit, 0, ',', '.') }}</td>
                <td width="25%">Dana Cadangan</td><td width="25%" class="text-right">Rp {{ number_format($dist->total_dana_cadangan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bagian Anggota</td><td class="text-right">Rp {{ number_format($dist->total_anggota, 0, ',', '.') }}</td>
                <td>Dana Pengurus</td><td class="text-right">Rp {{ number_format($dist->total_pengurus, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Dana Karyawan</td><td class="text-right">Rp {{ number_format($dist->total_karyawan, 0, ',', '.') }}</td>
                <td>Dana Pendidikan</td><td class="text-right">Rp {{ number_format($dist->total_pendidikan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($dist->memberDetails && $dist->memberDetails->count())
    <table>
        <thead>
            <tr><td colspan="4" class="header-type">Rincian SHU Anggota</td></tr>
            <tr>
                <th>Nama Anggota</th>
                <th class="text-right">Jasa Modal</th>
                <th class="text-right">Jasa Pinjaman</th>
                <th class="text-right">Total SHU</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dist->memberDetails as $detail)
            <tr>
                <td>{{ $detail->user?->name ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($detail->jasa_modal, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->jasa_pinjaman, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($detail->total_shu, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @empty
    <p style="text-align:center;">Belum ada perhitungan SHU untuk tahun ini.</p>
    @endforelse
</body>
</html>
