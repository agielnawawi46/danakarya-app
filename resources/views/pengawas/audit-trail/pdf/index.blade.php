<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Log Audit Trail</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; word-wrap: break-word; }
        th { background-color: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Log Audit Trail</h1>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="12%">Waktu</th>
                <th width="12%">Pengguna</th>
                <th width="10%">Aksi</th>
                <th width="15%">Model</th>
                <th width="41%">Deskripsi</th>
                <th width="10%">IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                <td>{{ $log->user?->name ?? '—' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ class_basename($log->model) }}@if($log->model_id) #{{ $log->model_id }}@endif</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->ip_address }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;">Tidak ada log data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
