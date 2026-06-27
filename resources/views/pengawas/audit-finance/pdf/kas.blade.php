<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Kas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
        h2 { text-align: center; font-size: 14px; margin-bottom: 20px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Arus Kas</h1>
    <h2>Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</h2>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <tr>
            <th>Total Kas Masuk (Kredit)</th>
            <td class="text-right font-bold">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Kas Keluar (Debit)</th>
            <td class="text-right font-bold">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Selisih (Net)</th>
            <td class="text-right font-bold">Rp {{ number_format($totalCredit - $totalDebit, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="15%">Sumber</th>
                <th width="19%" class="text-right">Debit</th>
                <th width="19%" class="text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($journals as $j)
            <tr>
                <td>{{ $j->date->format('d/m/Y') }}</td>
                <td>{{ $j->description }}</td>
                <td>{{ $j->source_type }}</td>
                <td class="text-right">
                    @php $d = $j->lines->sum('debit'); @endphp
                    {{ $d > 0 ? 'Rp ' . number_format($d, 0, ',', '.') : '-' }}
                </td>
                <td class="text-right">
                    @php $c = $j->lines->sum('credit'); @endphp
                    {{ $c > 0 ? 'Rp ' . number_format($c, 0, ',', '.') : '-' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;">Tidak ada transaksi</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
