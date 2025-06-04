
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Service</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        /* Tu estilo personalizado aquí */
    </style>
</head>
<table style="font-size:.8em; border-collapse: collapse; width: 100%;">
        <thead>
            <th width="180px"><img src="{{ public_path('images/barlovento-logo.png') }}" alt="Logo" width="150px"></th>
            <th colspan="3" width="1000px"><b style="font-size:1.1em;">ROTURAS / REPARACIONES</b></td>
        </thead>
</table>
<table style="font-size:.8em; border-collapse: collapse; width: 100%;">
        <tr>
            <td colspan="8" style="background-color:#000;"></td>
        </tr>
        <thead style="font-size:.9em;font-weight:bold;padding:0 5px;">
            <th width="80px">Fecha</th>
            <th width="150px">Rodado/Herramienta</th>
            <th width="130px">Encargado</th>
            <th width="170px">Descripci&oacute;n de Rotura</th>
            <th width="130px">Operario a Cargo</th>
            <th width="170px">Descripci&oacute;n de Reparaci&oacute;n</th>
            <th width="130px">Tipo de Trabajo</th>
            <th width="80px">Horas de Trabajo / $ Importe</th>        

        </thead>
        <tbody>
            
     
        @foreach ($records as $record)+
        @php $rowColor = $loop->odd ? '#f2f2f2' : '#ffffff'; @endphp
        <tr style="font-size:.8em; background-color: {{ $rowColor }};padding:0 5px;text-align:center">
            @php
                $fecha = explode('-', $record->fecha);
                $fecha = $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
            @endphp
            <td>{{ $fecha }}</td>
            <td>{{ \App\Models\RodadosHerramientas::find($record->rodadoHerramienta_id)?->nombre }}</td>
            <td>{{ $record->encargado }}</td>
            <td>{{ $record->descripcion }}</td>
            <td>{{ $record->operario }}</td>
            <td>{{ $record->descripcionReparacion }}</td>
            <td>{{ $record->tipo }}</td>
            @if ($record->tipo == 'Propio')
                <td>{{ $record->horas }} Hs</td>
            @else
                <td>$ {{ number_format($record->importe,2,',','.') }}</td>        
            @endif
        </tr>
        @endforeach 
        </tbody>

    </table>
</body>
</html>