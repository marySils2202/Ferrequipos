<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;        
use App\Models\Facturacion;      

class DetalleFactura extends Model
{
    protected $table        = 'detalle_factura';
    protected $primaryKey   = 'id_detalle';
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $fillable = [
        'id_factura',
        'id_producto',       
        'cantidad',
        'precio_unitario',  
        'descripcion',       
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
    ];

    

    public function factura()
    {
        return $this->belongsTo(Facturacion::class, 'id_factura', 'id_factura');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
