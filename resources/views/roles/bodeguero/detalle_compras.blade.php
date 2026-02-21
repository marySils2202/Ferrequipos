<h2 class="mb-3">Detalle de Compras</h2>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th><strong>Producto</strong></th>
            <th><strong>Proveedor</strong></th>
            <th><strong>Usuario</strong></th>
            <th><strong>Cantidad</strong></th>
            <th><strong>Precio Unitario</strong></th>
            <th><strong>Subtotal</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($detalles as $d)
            <tr>
                <td>{{ $d->producto->nombre ?? 'Sin producto' }}</td>
                <td>{{ $d->compra->proveedor->nombre ?? 'Sin proveedor' }}</td>
                <td>{{ $d->compra->usuario->nombre ?? 'Sin usuario' }}</td>
                <td>{{ $d->cantidad }}</td>
                <td>${{ number_format($d->precio_unitario, 2) }}</td>
                <td>${{ number_format($d->cantidad * $d->precio_unitario, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
