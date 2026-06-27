<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Besar</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header-type { background-color: #f1f5f9; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Laporan Buku Besar</h1>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    @php
        $typeLabels = ['asset'=>'Aset','liability'=>'Kewajiban','equity'=>'Ekuitas','income'=>'Pendapatan','expense'=>'Beban'];
        $grouped = $accounts->groupBy('type');
    @endphp

    @foreach($typeLabels as $type => $label)
        @if(isset($grouped[$type]) && $grouped[$type]->count())
            <table>
                <thead>
                    <tr>
                        <td colspan="5" class="header-type">{{ $label }}</td>
                    </tr>
                    <tr>
                        <th width="15%">Kode</th>
                        <th>Nama Akun</th>
                        <th width="20%" class="text-right">Total Debit</th>
                        <th width="20%" class="text-right">Total Kredit</th>
                        <th width="20%" class="text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grouped[$type]->sortBy('code') as $acc)
                        <tr>
                            <td>{{ $acc->code }}</td>
                            <td>{{ $acc->name }}</td>
                            <td class="text-right">Rp {{ number_format($acc->journalLines->sum('debit'), 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($acc->journalLines->sum('credit'), 0, ',', '.') }}</td>
                            <td class="text-right font-bold">Rp {{ number_format($acc->getBalance(), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
</body>
</html>
