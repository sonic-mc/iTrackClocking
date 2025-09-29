<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Logs Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h2>Audit Logs</h2><!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>iTrack Audit Logs Export</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 13px;
                color: #333;
                margin: 20px;
            }
    
            h1 {
                text-align: center;
                color: #2C3E50;
                margin-bottom: 5px;
            }
    
            .filters {
                text-align: center;
                font-size: 12px;
                color: #555;
                margin-bottom: 20px;
            }
    
            table {
                width: 100%;
                border-collapse: collapse;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            }
    
            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
    
            th {
                background-color: #2980B9;
                color: #fff;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
    
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
    
            tr:hover {
                background-color: #ecf0f1;
            }
    
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 12px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <h1>iTrack Logs</h1>
    
        @if(isset($from) && isset($to))
        <div class="filters">
            Showing logs from <strong>{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</strong>
            to <strong>{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong>
        </div>
        @endif
    
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ ucfirst($log->action) }}</td>
                    <td>{{ $log->description ?? '—' }}</td>
                    <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#999;">No logs found for this range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    
        <div class="footer">
            Exported on {{ now()->format('d M Y, H:i') }}
        </div>
    </body>
    </html>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td>{{ ucfirst($log->action) }}</td>
                <td>{{ $log->description ?? '—' }}</td>
                <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
