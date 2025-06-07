<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Arqueo extends Model
{
    protected $table      = 'arqueo';
    protected $primaryKey = 'id_arqueo';
    public    $timestamps = false;

    protected $fillable = [
        'fecha',
        'monto_inicial',
        'monto_final',
        'salida_caja',
        'razon_salida',
        'printed_by',    
        'printed_at',    
    ];

    protected $casts = [
        'fecha'      => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function impresor()
    {
        return $this->belongsTo(Usuario::class, 'printed_by', 'id_usuario');
    }

public function getDiferenciaAttribute()
{
    return $this->monto_final 
         - $this->monto_inicial 
         - $this->salida_caja;
}
public function libroVentas()
{
    return $this->hasMany(
        \App\Models\LibroVenta::class,
        'id_arqueo',
        'id_arqueo'
    );
}

}
