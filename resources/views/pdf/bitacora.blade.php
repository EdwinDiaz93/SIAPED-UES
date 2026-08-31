<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Bitácora {{ $anio }}</title>
    <style>
        @page { margin: 25px 20px; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #111827; font-size: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { color: #960000; font-size: 16px; margin: 0 0 4px 0; }
        .header p { margin: 0; font-size: 10px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #960000;
            color: #ffffff;
            padding: 6px 5px;
            text-align: left;
            font-size: 9px;
        }
        tbody td {
            padding: 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 15px; font-size: 8px; color: #9ca3af; text-align: right; }
        .badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-create { background: #dcfce7; color: #166534; }
        .badge-edit   { background: #fef9c3; color: #854d0e; }
        .badge-delete { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name') }} — Reporte de Bitácora de Auditoría</h1>
        <p>Año: {{ $anio }}</p>
        @if ($filtrosTexto)
            <p>Filtros aplicados: {{ $filtrosTexto }}</p>
        @endif
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 13%;">Fecha</th>
                <th style="width: 20%;">Usuario</th>
                <th style="width: 10%;">Acción</th>
                <th style="width: 15%;">Tabla</th>
                <th style="width: 8%;">Registro</th>
                <th style="width: 34%;">Detalle</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                @php
                    $etiquetas = ['CREATE' => 'Creación', 'EDIT' => 'Edición', 'DELETE' => 'Eliminación'];
                    $clases = ['CREATE' => 'badge-create', 'EDIT' => 'badge-edit', 'DELETE' => 'badge-delete'];
                @endphp
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user?->name }} {{ $log->user?->apellidos }}</td>
                    <td><span class="badge {{ $clases[$log->action] ?? '' }}">{{ $etiquetas[$log->action] ?? $log->action }}</span></td>
                    <td>{{ $log->table_name }}</td>
                    <td>#{{ $log->record_id }}</td>
                    <td>
                        @if ($log->action === 'DELETE')
                            {{ Str::limit(json_encode($log->old_value, JSON_UNESCAPED_UNICODE), 150) }}
                        @elseif ($log->action === 'CREATE')
                            {{ Str::limit(json_encode($log->new_value, JSON_UNESCAPED_UNICODE), 150) }}
                        @else
                            {{ Str::limit(json_encode($log->new_value, JSON_UNESCAPED_UNICODE), 150) }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 15px;">No hay registros en la bitácora para el año {{ $anio }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total de registros: {{ $logs->count() }}
    </div>

</body>
</html>
