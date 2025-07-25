
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Combustibles/Lubricantes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        /* Tu estilo personalizado aquí */
    </style>
</head>
<table style="font-size:.8em; border-collapse: collapse; width: 100%;">
        <thead>
            <th width="180px"><img src="{{ public_path('images/barlovento-logo.png') }}" alt="Logo" width="150px"></th>
            <th colspan="3" width="1000px"><b style="font-size:1.1em;">COMBUSTIBLES / LUBRICANTES</b></td>
        </thead>
</table>
<table style="font-size:.8em; border-collapse: collapse; width: 100%;">
        <tr>
            <td colspan="8" style="background-color:#000;"></td>
        </tr>
        <thead style="font-size:.9em;font-weight:bold;padding:0 5px;">
            <th width="70px">Fecha</th>
            <th width="70px">Tipo</th>
            <th width="100px">Ingreso Litros</th>
            <th width="150px">Origen</th>
            <th width="100px">Egreso Litros</th>
            <th width="150px">Destino</th>
            <th width="80px">STOCK</th>

        </thead>
        <tbody>
            
        @php 
        $stock = 0;
        @endphp

        @foreach ($records as $record)

        @php 
        
        $stock += $record->ingresoLitros;

        if($stock == 0){ 
            $stock = $record->egresoLitros;
        }else{ 
            $stock -= $record->egresoLitros;
        }

        $rowColor = $loop->odd ? '#f2f2f2' : '#ffffff'; 
        
        @endphp
        <tr style="font-size:.8em; background-color: {{ $rowColor }};padding:0 5px;text-align:center">
            @php
                $fecha = explode('-', $record->fecha);
                $fecha = $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
            @endphp
            <td>{{ $fecha }}</td>
            <td>{{ $record->tipo }}</td>
            <td>{{ $record->ingresoLitros . ' Lts' }}</td>
            <td>{{ $record->origen }}</td>
            <td>{{ $record->egresoLitros . ' Lts'}}</td>
            <td>{{ \App\Models\RodadosHerramientas::find($record->destino)?->nombre }}</td>
            <td>{{ $stock . ' Lts'}}</td>
        </tr>
        @endforeach 
        </tbody>

    </table>
</body>
</html>