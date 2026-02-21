<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compra';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'nombre_producto'
    ];

    public function compra()
    {
        return $this->belongsTo(\App\Models\Compra::class, 'id_compra');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }


}
