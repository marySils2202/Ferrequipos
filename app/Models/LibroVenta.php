<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibroVenta extends Model
{
    protected $table = 'libro_ventas';
    protected $primaryKey = 'id_libro_venta';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'monto_final',
        'id_arqueo',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];
    
    public function arqueo()
    {
        return $this->belongsTo(
            Arqueo::class,
            'id_arqueo',
            'id_arqueo'
        );
    }
}
