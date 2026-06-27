<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Simpanan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Rekap Simpanan Anggota</h1>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama Anggota</th>
                <th class="text-right">Pokok</th>
                <th class="text-right">Wajib</th>
                <th class="text-right">Sukarela</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $m)
            <tr>
                <td>{{ $m->employee_id ?? '-' }}</td>
                <td>{{ $m->name }}</td>
                <td class="text-right">Rp {{ number_format($m->simpanan_pokok, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($m->simpanan_wajib, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($m->simpanan_sukarela, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($m->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;">Belum ada data anggota</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Total Keseluruhan</th>
                <th class="text-right">Rp {{ number_format($totals['pokok'], 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totals['wajib'], 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totals['sukarela'], 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totals['grand'], 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
