<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Billing - {{ $monthNames[$month] }} {{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .summary-box { border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa; }
        .summary-box h3 { margin-top: 0; }
    </style>
</head>
<body>
    @include('pdf-header')
    <div class="header">
        <h1 style="font-size: 16px; margin-bottom: 5px;">Laporan Tagihan Potongan Payroll</h1>
        <p>Periode {{ $monthNames[$month] }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>Departemen</th>
                <th class="text-right">Simpanan Pokok</th>
                <th class="text-right">Simpanan Wajib</th>
                <th class="text-right">Angsuran Pinjaman</th>
                <th class="text-right">Total Potongan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($billing as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['employee_id'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['department'] }}</td>
                <td class="text-right">{{ number_format($row['simpanan_pokok'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row['simpanan_wajib'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row['angsuran'], 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($row['total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data tagihan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Keseluruhan</th>
                <th class="text-right">{{ number_format($totalSimpananPokok, 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($totalSimpananWajib, 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($totalAngsuran, 0, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($totalPotongan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        <h3>Ringkasan Tagihan</h3>
        <p>Total Karyawan: <strong>{{ count($billing) }} Orang</strong></p>
        <p>Total Simpanan Pokok: <strong>Rp {{ number_format($totalSimpananPokok, 0, ',', '.') }}</strong></p>
        <p>Total Simpanan Wajib: <strong>Rp {{ number_format($totalSimpananWajib, 0, ',', '.') }}</strong></p>
        <p>Total Angsuran Pinjaman: <strong>Rp {{ number_format($totalAngsuran, 0, ',', '.') }}</strong></p>
        <p>Grand Total Potongan: <strong style="font-size: 14px;">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</strong></p>
    </div>
</body>
</html>
