<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Movimiento;
use App\Models\DetalleCompra;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_venta',
        'id_categoria',
        'estado',
        'stock_minimo',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }


    public function inventario()
    {
        return $this->hasOne(Inventario::class, 'id_producto', 'id_producto');
    }

    
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_producto');
    }


    public function detallesCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }
}