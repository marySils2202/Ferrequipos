<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Inventario extends Model
{
    protected $table = 'inventario';
    protected $primaryKey = 'id_inventario';
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'cantidad_stock',
        'fecha_actualizacion',
    ];


    protected $casts = [
        'fecha_actualizacion' => 'datetime',
    ];

 
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto')
                    ->withTrashed();
    }
}