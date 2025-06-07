<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id_compra';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'cantidad',
        'total',
    ];

    public function detalles()
    {
        return $this->hasMany(\App\Models\DetalleCompra::class, 'id_compra');
    }


    public function proveedor()
    {
        return $this->belongsTo(\App\Models\Proveedor::class, 'id_proveedor');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'id_usuario');
    }

    public function getTotalFormateadoAttribute()
    {
        return number_format($this->total, 2);
    }
    protected $casts = [
        'fecha' => 'datetime',
    ];
    public function producto()
{
    return $this->belongsTo(Producto::class, 'id_producto');
}

}
