<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mecanico extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'mecanicos';
    protected $primaryKey = 'id_mecanico';

    protected $fillable = [
        'nombre',
    ];

    protected $dates = ['deleted_at'];

    public function facturas()
    {
        return $this->hasMany(Facturacion::class, 'mecanico_id', 'id_mecanico');
    }
}
