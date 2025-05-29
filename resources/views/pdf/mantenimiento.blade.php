
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Mantenimiento</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        /* Tu estilo personalizado aquí */
    </style>
</head>
<body>
    <table style="font-size:.8em;">
        <thead>
            <th width="180px"><img src="{{ public_path('images/barlovento-logo.png') }}" alt="Logo" width="150px"></th>
            <th colspan="3" width="480px"><b style="font-size:1.1em;">REPORTE DE MANTENIMIENTO DE RODADOS Y HERRAMIENTAS</b></th>

        </thead>

        <tr>
            <td colspan="4" style="background-color:#000;"></td>
        </tr>
        <tr style="font-size:.8em;font-weight:bold;">
            <td width="171px">Fecha del Informe:</td>
            <td width="171px">Responsables:</td>
            <td width="171px">Turno:</td>
            <td width="171px">Rodado/Herramienta:</td>
        </tr>
        <tr>
            <td>{{ \Carbon\Carbon::parse($record->fecha)->format('d-m-Y') }}</td>
            <td>{{ $record->responsable }}</td>
            <td>{{ $record->turno }}</td>
            <td>{{ \App\Models\RodadosHerramientas::find($record->rodadoHerramienta_id)?->nombre }}</td>
        </tr>
        <tr>
            <td colspan="4" style="background-color:#000;"></td>
        </tr>
    </table>
<br>
    <table>
        <thead>
            <th width="600px" align="left">Tareas Realizadas:</th>
            <th width="50px" align="center">SI</th>
            <th width="50px" align="center">NO</th>
        </thead>
        <tr>
            <td colspan="3" style="background-color:#000"></td>
        </tr>
        @php
            $tareas = json_decode($record->tareas, true);
            $listaTareas = [
                        1 => 'Nivel de agua refrigerante',
                        2 => 'Presion de los neumaticos',
                        3 => 'Lubricacion/Engrasado completo',
                        4 => 'Nivel de aceite de motor',
                        5 => 'Nivel de aceite de transmision',
                        6 => 'Nivel de aceite reductoras',
                        7 => 'Limpiado/Sopleteado radiadores y filtro de aire',
                        8 => 'Limpiado/Sopleteado de cabina',
                        9 => 'Lavado del mismo si es necesario',];
        @endphp
        @foreach ($listaTareas as $index => $item)
            <tr style="font-size:.8em;"> 
                <td>{{ $index . '- ' . $item }}</td>
                @if (in_array($index, $tareas))
                    <td align="center">
                        <b>X</b>
                    </td>
                    <td></td>
                @else
                    <td></td>
                    <td align="center">
                        <b>X</b>
                    </td>
                @endif
            </tr>
            <tr>
                <td colspan="5" style="background-color:#000;line-height:.3px"></td>
            </tr>
        @endforeach 
    </table>
    <br>
    <table>
        <tr style="font-size:.8em;font-weight:bold;">
            <td style="border-right: 1px solid #000;padding:0 20px 0 0;">Horas Motor:</td>
            <td style="border-right: 1px solid #000;padding:0 20px;">Kilometros:</td>
            <td>Observaciones:</td>
        </tr>
        <tr style="font-size:.8em;">
            <td style="border-right: 1px solid #000;padding:0 20px 0 0;">{{ number_format($record->horasMotor, 0, ',', '.') . ' Hs' }}</td>
            <td style="border-right: 1px solid #000;padding:0 20px;">{{ number_format($record->km, 0, ',', '.') . ' Kms' }}</td>
            <td>{{$record->observaciones}}</td>
        </tr>
    </table>    
</body>
</html>