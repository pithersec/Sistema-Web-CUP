<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
    h2 { color: #0d3b6e; border-bottom: 2px solid #c0392b; padding-bottom: 4px; margin-bottom: 12px; }
    .meta { font-size: 10px; color: #666; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0d3b6e; color: white; padding: 6px 8px; text-align: left; font-size: 10px; }
    td { padding: 5px 8px; border-bottom: 1px solid #e0e0e0; font-size: 10px; }
    tr:nth-child(even) td { background: #f5f8fc; }
    .sin-datos { text-align: center; padding: 20px; color: #888; font-style: italic; }
</style>
</head>
<body>
<h2>{{ $titulo }}</h2>
<p class="meta">Generado el {{ now()->format('d/m/Y H:i') }}</p>

@if($filas->isEmpty())
    <p class="sin-datos">No hay datos disponibles para generar el reporte.</p>
@else
<table>
    <thead>
        <tr>@foreach($columnas as $col)<th>{{ $col }}</th>@endforeach</tr>
    </thead>
    <tbody>
        @foreach($filas as $fila)
        <tr>
            @foreach((array)$fila as $valor)
            <td>{{ $valor }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@endif
</body>
</html>
