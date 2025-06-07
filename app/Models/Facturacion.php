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


    public function getGananciaDiariaAttribute()
    {
        $ganancia = 0;


        foreach ($this->detalles as $detalle) {
            // Precio de venta por unidad
            $precioVenta = $detalle->producto->precio_venta;


            $precioCompraPromedio = $detalle->producto
                                            ->detallesCompras
                                            ->avg('precio_unitario');


            $cantidadVendida = $detalle->cantidad;

            $costo = $precioCompraPromedio ?: 0;


            $ganancia += ($precioVenta - $costo) * $cantidadVendida;
        }

        return $ganancia;
    }
}
