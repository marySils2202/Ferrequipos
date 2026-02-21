<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';

    public $timestamps = false;  
    protected $fillable = ['nombre', 'domicilio', 'telefono'];

   
public function compras()
{
    return $this->hasMany(\App\Models\Compra::class, 'id_proveedor');
}
public function getDomicilioAttribute()
{
    return $this->attributes['Domicilio'] ?? null;
}
}
