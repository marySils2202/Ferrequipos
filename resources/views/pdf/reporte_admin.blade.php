<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>{{ $titulo }}</title>
  <style>
    body { font-family: sans-serif; color: #333; margin:0; padding:0; }
    .header { display:flex; align-items:center; padding:20px; background:#f3f3f3; border-bottom:2px solid #4f46e5; }
    .header img { height:50px; margin-right:20px; }
    .logo-placeholder { width:50px; height:50px; background:#ddd; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#888; margin-right:20px; }
    .header h1 { font-size:1.8rem; color:#4f46e5; margin:0; }
    .subtitle { text-align:center; margin:10px 0; color:#666; }
    table { width:100%; border-collapse:collapse; margin:20px 0; }
    thead th { background:#4f46e5; color:#fff; padding:10px; border:1px solid #ddd; text-align:left; }
    tbody tr:nth-child(even) { background:#f9f9f9; }
    tbody td { border:1px solid #ddd; padding:8px; font-size:0.9rem; }
    .footer { text-align:center; margin:30px 0; color:#999; font-size:0.8rem; }
    .text-center { text-align:center; }
    .text-end { text-align:right; }
  </style>
</head>
<body>

  <div class="header">
    @if (file_exists(public_path('imagenes/logo_quinteros.png')))
      <img src="{{ public_path('imagenes/logo_quinteros.png') }}" alt="Logo">
    @else
      <div class="logo-placeholder">Logo</div>
    @endif
    <h1>{{ $titulo }}</h1>
  </div>

  <div class="subtitle">
    Generado: {{ now()->format('Y-m-d H:i') }}
  </div>

  @if($items->isEmpty())
    <p style="text-align:center; color:#666; margin-top:2rem;">
      No hay registros para mostrar.
    </p>

  @else
    <table>
      <thead>
        <tr>
          @switch($tipo_reporte)
            @case('clientes')
              <th>Nombre</th>
              <th>Dirección</th>
              <th>Teléfono</th>
              @break

            @case('usuarios')
              <th>Username</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              @break

            @case('proveedores')
              <th>Nombre</th>
              <th>Domicilio</th>
              <th>Teléfono</th>
              @break

            @case('facturas')
              <th>Cliente</th>
              <th>Facturó</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Pago</th>
              <th>Vuelto</th>
              <th>Mano obra</th>
              @break

            @case('compras')
              <th>Producto</th>
              <th>Proveedor</th>
              <th>Fecha</th>
              <th class="text-center">Cantidad</th>
              <th class="text-end">Total (C$ )</th>
              @break

            @case('productos')
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio venta</th>
              <th>Stock mínimo</th>
              <th>Categoría</th>
              <th>Estado</th>
              @break
          @endswitch
        </tr>
      </thead>
      <tbody>
        @if($tipo_reporte === 'compras')
          {{-- Compras: loop each compra and its detalles --}}
          @foreach($items as $compra)
            @foreach($compra->detalles as $det)
              <tr>
                <td>{{ $det->producto->nombre }}</td>
                <td>{{ $compra->proveedor->nombre }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($compra->fecha)->format('Y-m-d') }}</td>
                <td class="text-center">{{ $det->cantidad }}</td>
                <td class="text-end">C$  {{ number_format($det->cantidad * $det->precio_unitario, 2) }}</td>
              </tr>
            @endforeach
          @endforeach

        @else
          {{-- All other report types --}}
          @foreach($items as $row)
            <tr>
              @switch($tipo_reporte)
                @case('clientes')
                  <td>{{ $row->nombre }}</td>
                  <td>{{ $row->direccion }}</td>
                  <td>{{ $row->telefono }}</td>
                  @break

                @case('usuarios')
                  <td>{{ $row->username }}</td>
                  <td>{{ $row->nombre }}</td>
                  <td>{{ $row->email }}</td>
                  <td>{{ ucfirst($row->rol) }}</td>
                  @break

                @case('proveedores')
                  <td>{{ $row->nombre }}</td>
                  <td>{{ $row->domicilio }}</td>
                  <td>{{ $row->telefono }}</td>
                  @break

                @case('facturas')
                  <td>{{ $row->cliente->nombre }}</td>
                  <td>{{ $row->usuario->nombre }}</td>
                  <td>{{ $row->fecha->format('Y-m-d H:i') }}</td>
                  <td>C$  {{ number_format($row->total,2) }}</td>
                  <td>C$  {{ number_format($row->monto_pago,2) }}</td>
                  <td>C$  {{ number_format($row->vuelto,2) }}</td>
                  <td>C$  {{ number_format($row->mano_obra,2) }}</td>
                  @break

                @case('productos')
                  <td>{{ $row->nombre }}</td>
                  <td>{{ $row->descripcion }}</td>
                  <td>C$  {{ number_format($row->precio_venta,2) }}</td>
                  <td>{{ $row->stock_minimo }}</td>
                  <td>{{ optional($row->categoria)->nombre_categoria }}</td>
                  <td>{{ $row->estado ? 'Activo' : 'Inactivo' }}</td>
                  @break
              @endswitch
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  @endif

  <div class="footer">
    &copy; {{ date('Y') }} MotoRepuestosQuinteros – Todos los derechos reservados.
  </div>

</body>
</html>
