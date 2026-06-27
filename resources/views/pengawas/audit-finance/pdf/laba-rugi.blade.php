<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba / Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
        h2 { text-align: center; font-size: 14px; margin-bottom: 20px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header-type { background-color: #f1f5f9; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Laporan Laba / Rugi</h1>
    <h2>Periode Tahun {{ $year }}</h2>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    @php
        $totalIncome = $incomeAccounts->sum('balance');
        $totalExpense = $expenseAccounts->sum('balance');
        $netProfit = $totalIncome - $totalExpense;
    @endphp

    <table>
        <thead>
            <tr><td colspan="3" class="header-type">Pendapatan</td></tr>
            <tr><th>Kode</th><th>Nama Akun</th><th class="text-right">Saldo</th></tr>
        </thead>
        <tbody>
            @foreach($incomeAccounts as $acc)
            <tr><td>{{ $acc->code }}</td><td>{{ $acc->name }}</td><td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td></tr>
            @endforeach
            <tr><td colspan="2" class="font-bold">Total Pendapatan</td><td class="text-right font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr><td colspan="3" class="header-type">Beban</td></tr>
            <tr><th>Kode</th><th>Nama Akun</th><th class="text-right">Saldo</th></tr>
        </thead>
        <tbody>
            @foreach($expenseAccounts as $acc)
            <tr><td>{{ $acc->code }}</td><td>{{ $acc->name }}</td><td class="text-right">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td></tr>
            @endforeach
            <tr><td colspan="2" class="font-bold">Total Beban</td><td class="text-right font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <table>
        <tr>
            <td class="header-type" width="50%">{{ $netProfit >= 0 ? 'Surplus / Laba Bersih' : 'Defisit / Rugi Bersih' }}</td>
            <td class="header-type text-right">Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
