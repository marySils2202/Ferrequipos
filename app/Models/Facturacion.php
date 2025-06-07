<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facturacion extends Model
{
    protected $table      = 'facturacion';
    protected $primaryKey = 'id_factura';
    public    $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'total',
        'mano_obra',
        'descuento',      
        'monto_pago',
        'vuelto',
        'metodo_pago',
        'saldo_pendiente',
        'mecanico_id',
    ];

    protected $casts = [
        'fecha'             => 'datetime',
        'total'             => 'decimal:2',
        'mano_obra'         => 'decimal:2',
        'descuento'         => 'decimal:2',
        'monto_pago'        => 'decimal:2',
        'vuelto'            => 'decimal:2',
        'saldo_pendiente'   => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class, 'id_factura', 'id_factura');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function credito()
    {
        return $this->hasOne(Credito::class, 'id_factura', 'id_factura');
    }

    public function mecanico()
    {
        return $this->belongsTo(Mecanico::class, 'mecanico_id', 'id_mecanico');
    }

    /**
     * Accesor: calcula la ganancia de esta factura (Venta del Día),
     * usando para cada detalle la diferencia entre precio_venta y costo de compra promedio.
     */
    public function getGananciaDiariaAttribute()
    {
        $ganancia = 0;

        // Recorremos cada detalle de la factura
        foreach ($this->detalles as $detalle) {
            // Precio de venta por unidad
            $precioVenta = $detalle->producto->precio_venta;

            // Costo de compra promedio del producto:
            // asumimos que en Producto está definida la relación detallesCompras()
            $precioCompraPromedio = $detalle->producto
                                            ->detallesCompras
                                            ->avg('precio_unitario');

            // Cantidad vendida en este detalle
            $cantidadVendida = $detalle->cantidad;

            // Si por alguna razón no hay compras registradas,
            // asumimos costo cero para no romper cálculo
            $costo = $precioCompraPromedio ?: 0;

            // Sumar (precioVenta - costo) * cantidadVendida
            $ganancia += ($precioVenta - $costo) * $cantidadVendida;
        }

        return $ganancia;
    }
}
