<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; }
    .header { margin-bottom: 10px; border-bottom: 2px solid #c0392b; padding-bottom: 6px; }
    .header h2 { font-size: 13px; color: #0d3b6e; }
    .header p { font-size: 8px; color: #666; margin-top: 2px; }
    .aviso { background: #fff3cd; border: 1px solid #f0ad4e; padding: 5px 8px; margin-bottom: 8px; font-size: 8px; color: #856404; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th { background: #0d3b6e; color: #fff; padding: 4px 5px; text-align: left; font-size: 8px; overflow: hidden; }
    td { padding: 3px 5px; border-bottom: 1px solid #e0e0e0; font-size: 8px; overflow: hidden; word-wrap: break-word; }
    tr:nth-child(even) td { background: #f5f8fc; }
    .sin-datos { text-align: center; padding: 20px; color: #888; font-style: italic; }
</style>
</head>
<body>
<div class="header">
    <h2>{{ $titulo }}</h2>
    <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
</div>

@if($cortado)
<div class="aviso">
    ⚠ Mostrando {{ number_format($filas->count()) }} de {{ number_format($total) }} registros. Para el listado completo use Excel.
</div>
@endif

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