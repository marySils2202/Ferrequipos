<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    public const EST_PENDIENTE = 'pendiente';
    public const EST_PAGADO    = 'pagado';
    public const EST_PARCIAL   = 'parcial';

    protected $table      = 'creditos';
    protected $primaryKey = 'id_credito';
    public    $timestamps = false;

    protected $fillable = [
        'id_factura',
        'monto_total',
        'monto_pagado',
        'estado',
        'fecha_inicio',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'estado'       => 'string',
    ];


    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            self::EST_PAGADO  => 'pagado',
            self::EST_PARCIAL => 'parcial',
            default           => 'pendiente',
        };
    }

    public function factura()
    {
        return $this->belongsTo(Facturacion::class,'id_factura','id_factura');
    }
}
