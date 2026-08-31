<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Docentes Aptos para Promoción</title>
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
        .badge-si { background: #dcfce7; color: #166534; }
        .badge-no { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name') }} — Reporte de Docentes Aptos para Promoción</h1>
        <p>
            Escuela/Unidad: {{ $escuelaNombre ?? 'Todas' }} ·
            Estado: {{ ['aptos' => 'Aptos', 'no_aptos' => 'No aptos', 'todos' => 'Todos'][$filtroEstado] ?? $filtroEstado }}
        </p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 24%;">Docente</th>
                <th style="width: 18%;">Escuela / Unidad</th>
                <th style="width: 10%;">Cat. Actual</th>
                <th style="width: 12%;">Cat. Siguiente</th>
                <th style="width: 16%;">Puntaje</th>
                <th style="width: 10%;">%</th>
                <th style="width: 10%;">¿Apto?</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($docentes as $r)
                @php [$docente, $c] = [$r['docente'], $r['calculo']]; @endphp
                <tr>
                    <td>{{ $docente->name }} {{ $docente->apellidos }}</td>
                    <td>{{ $docente->institution?->escuela?->name ?? '—' }}</td>
                    <td>{{ $c['categoria_actual'] }}</td>
                    <td>{{ $c['siguiente_categoria'] ?? '— (máxima)' }}</td>
                    <td>{{ number_format($c['total_ganado'], 2) }} / {{ number_format($c['total_maximo'], 2) }} pts</td>
                    <td>{{ $c['porcentaje'] }}%</td>
                    <td>
                        <span class="badge {{ $c['cumple_ascenso'] ? 'badge-si' : 'badge-no' }}">
                            {{ $c['cumple_ascenso'] ? 'Sí' : 'No' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 15px;">No hay docentes que coincidan con los filtros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total de registros: {{ $docentes->count() }}
    </div>

</body>
</html>
