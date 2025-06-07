
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Movimientos (PDF)</title>
<STYle>
    @page { margin: 20mm 15mm; }
    body {
      font-family: 'Segoe UI', sans-serif;
      font-size: 12px;
      color: #333;
      margin: 0;
      padding: 0;
    }
    header {
      text-align: center;
      margin-bottom: 10px;
      border-bottom: 2px solid #4361ee;
      padding-bottom: 8px;
    }
    header img {
      height: 50px;
      display: block;
      margin: 0 auto 5px;
    }
    header h2 {
      margin: 0;
      font-size: 20px;
      color: #4361ee;
    }
    header .date {
      font-size: 10px;
      color: #666;
      margin-top: 4px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    thead th {
      background: #4361ee;
      color: #fff;
      padding: 6px 4px;
      border: 1px solid #ddd;
      font-weight: normal;
      text-align: center;
      font-size: 11px;
    }
    tbody td {
      padding: 4px;
      border: 1px solid #ddd;
      text-align: center;
    }

    tbody tr:nth-child(odd) {
      background: #f9f9f9;
    }
    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 10px;
      color: #999;
      border-top: 1px solid #ccc;
      padding-top: 4px;
    }
</STYle>
</head>
<body>
  <header>
    <img src="{{ public_path('imagenes/logo_quinteros.png') }}" alt="Logo MotoRepuestosQuinteros">
    <h2>Histórico de Movimientos</h2>
    <div class="date">
      Generado: {{ now()->format('Y-m-d H:i') }}
      @if(request('filtro'))
        — Filtro: "{{ request('filtro') }}"
      @endif
    </div>
  </header>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Fecha</th>
        <th>Producto</th>
        <th>Tipo</th>
        <th>Cantidad</th>
        <th>Monto (C$)</th>
        <th>Descripción</th>
      </tr>
    </thead>
    <tbody>
      @forelse($movimientos as $mov)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $mov->fecha }}</td>
          <td>{{ $mov->producto->nombre }}</td>
          <td>{{ ucfirst($mov->tipo) }}</td>
          <td>{{ $mov->cantidad }}</td>
          <td>{{ $mov->monto ?? '—' }}</td>
          <td>{{ $mov->descripcion ?? '—' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7">No hay movimientos registrados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <footer>
    © {{ date('Y') }} MotoRepuestosQuinteros – Todos los derechos reservados.
  </footer>
</body>
</html>
