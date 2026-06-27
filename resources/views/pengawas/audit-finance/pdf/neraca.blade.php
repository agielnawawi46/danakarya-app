<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Neraca Keuangan</title>
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
    <h1>Neraca Keuangan</h1>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    @php
        $totalAssets = $assets->sum(fn($a) => $a->getBalance());
        $totalLiabilities = $liabilities->sum(fn($a) => $a->getBalance());
        $totalEquities = $equities->sum(fn($a) => $a->getBalance());
    @endphp

    <table>
        <thead>
            <tr><td colspan="3" class="header-type">Aset</td></tr>
            <tr><th>Kode</th><th>Nama Akun</th><th class="text-right">Saldo</th></tr>
        </thead>
        <tbody>
            @foreach($assets as $acc)
            <tr><td>{{ $acc->code }}</td><td>{{ $acc->name }}</td><td class="text-right">Rp {{ number_format($acc->getBalance(), 0, ',', '.') }}</td></tr>
            @endforeach
            <tr><td colspan="2" class="font-bold">Total Aset</td><td class="text-right font-bold">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr><td colspan="3" class="header-type">Kewajiban</td></tr>
            <tr><th>Kode</th><th>Nama Akun</th><th class="text-right">Saldo</th></tr>
        </thead>
        <tbody>
            @foreach($liabilities as $acc)
            <tr><td>{{ $acc->code }}</td><td>{{ $acc->name }}</td><td class="text-right">Rp {{ number_format($acc->getBalance(), 0, ',', '.') }}</td></tr>
            @endforeach
            <tr><td colspan="2" class="font-bold">Total Kewajiban</td><td class="text-right font-bold">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr><td colspan="3" class="header-type">Ekuitas</td></tr>
            <tr><th>Kode</th><th>Nama Akun</th><th class="text-right">Saldo</th></tr>
        </thead>
        <tbody>
            @foreach($equities as $acc)
            <tr><td>{{ $acc->code }}</td><td>{{ $acc->name }}</td><td class="text-right">Rp {{ number_format($acc->getBalance(), 0, ',', '.') }}</td></tr>
            @endforeach
            <tr><td colspan="2" class="font-bold">Total Ekuitas</td><td class="text-right font-bold">Rp {{ number_format($totalEquities, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <table>
        <tr>
            <td class="header-type" width="50%">Total Aset</td>
            <td class="header-type text-right">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="header-type" width="50%">Total Kewajiban + Ekuitas</td>
            <td class="header-type text-right">Rp {{ number_format($totalLiabilities + $totalEquities, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
